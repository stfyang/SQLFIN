<?php
//庫存新增後端
header('Content-Type: application/json');
include 'db.php';

// 取得前端
$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$category = trim($data['category'] ?? '');
$safe_quantity = intval($data['safe_quantity'] ?? 0);
$quantity = intval($data['quantity'] ?? 0);

if ($name === '') {
    echo json_encode(['success' => false, 'message' => '請填入名稱']);
    exit;
}

try {
    $sql = "INSERT INTO 原物料 (原物料名稱, 種類, 安全庫存量, 庫存量) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $category, $safe_quantity, $quantity]);
    echo json_encode(['success' => true, 'message' => '新增成功']);
} catch (PDOException $e) {
    
    echo json_encode(['success' => false, 'message' => '請確認菜單名稱是否重複，並稍後再試']);
}



