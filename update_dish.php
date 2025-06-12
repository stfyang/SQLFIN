<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$oldName = $data['old_name'] ?? null;
$newName = $data['name'] ?? null;
$price = $data['price'] ?? null;
$materials = $data['materials'] ?? [];

if (!$oldName || !$newName || !$price || empty($materials)) {
    echo json_encode(['success' => false, 'error' => '資料不完整']);
    exit;
}

// 檢查原物料是否有重複
$materialNames = array_column($materials, 'material_name');
if (count($materialNames) !== count(array_unique($materialNames))) {
    echo json_encode(['success' => false, 'error' => '原物料不可重複']);
    exit;
}

$name = trim($data['name']);
if (!preg_match('/^(?!\d+$)[\x{4e00}-\x{9fa5}a-zA-Z0-9\s]{1,20}$/u', $name)) {
    echo json_encode(['success' => false, 'error' => '菜品名稱格式錯誤']);
    exit;
}
try {
    $pdo->beginTransaction();

    // 更新菜品
    $stmt = $pdo->prepare("UPDATE 菜品 SET 菜品名 = ?, 單價 = ? WHERE 菜品名 = ?");
    $stmt->execute([$newName, $price, $oldName]);

    // 刪除舊的消耗資料
    $stmt = $pdo->prepare("DELETE FROM 消耗 WHERE 菜品名 = ?");
    $stmt->execute([$oldName]);

    // 新增新的消耗資料
    $stmt = $pdo->prepare("INSERT INTO 消耗 (菜品名, 原物料名稱, 消耗物料量) VALUES (?, ?, ?)");
    foreach ($materials as $m) {
        $stmt->execute([$newName, $m['material_name'], $m['quantity']]);
    }

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => '更新失敗: ' . $e->getMessage()]);
}
