<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('無效資料');
    }

    $name = trim($data['name'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $materials = $data['materials'] ?? [];

    // 基本資料驗證
    if ($name === '' || $price <= 0 || !is_array($materials) || count($materials) === 0) {
        throw new Exception('資料格式錯誤');
    }

    // 名稱格式防呆
    if (!preg_match('/^(?!\d+$)[\x{4e00}-\x{9fa5}a-zA-Z0-9\s]{1,20}$/u', $name)) {
        echo json_encode(['success' => false, 'error' => '菜品名稱格式錯誤']);
        exit;
    }

    // 檢查菜品名稱重複
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM 菜品 WHERE 菜品名 = ?");
    $stmtCheck->execute([$name]);
    if ($stmtCheck->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'error' => '菜品名稱已存在，請更換名稱']);
        exit;
    }

    // 檢查原物料是否重複
    $materialNames = array_column($materials, 'material_name');
    if (count($materialNames) !== count(array_unique($materialNames))) {
        echo json_encode(['success' => false, 'error' => '原物料不可重複']);
        exit;
    }

    // 開始交易
    $pdo->beginTransaction();

    // 新增菜品
    $stmtDish = $pdo->prepare("INSERT INTO 菜品 (菜品名, 單價) VALUES (?, ?)");
    $stmtDish->execute([$name, $price]);

    // 新增消耗資料
    $stmtMat = $pdo->prepare("INSERT INTO 消耗 (菜品名, 原物料名稱, 消耗物料量) VALUES (?, ?, ?)");
    foreach ($materials as $mat) {
        $mat_name = trim($mat['material_name'] ?? '');
        $qty = floatval($mat['quantity'] ?? 0);
        if ($mat_name === '' || $qty <= 0) {
            throw new Exception('原物料資料錯誤');
        }
        $stmtMat->execute([$name, $mat_name, $qty]);
    }

    // 完成交易
    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
