<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['菜品名'] ?? 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => '缺少菜品ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM 菜品 WHERE 菜品名=?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
