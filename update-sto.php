<?php
header('Content-Type: application/json');

include 'db.php'; 

// 接收前端資料
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$safe = intval($data['safe_quantity'] ?? 0);
$category = $data['category'] ?? '';
$quantity = intval($data['quantity'] ?? 0);

// 安全檢查
if ($name === '') {
    http_response_code(400);
    echo json_encode(['message' => '缺少原物料名稱']);
    exit;
}

try {
    $sql = "UPDATE 原物料 SET 安全庫存量 = ?, 種類 = ?, 庫存量 = ? WHERE 原物料名稱 = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$safe, $category, $quantity, $name]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '更新失敗']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '資料庫錯誤: ' . $e->getMessage()]);
}
