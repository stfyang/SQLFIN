<?php
header('Content-Type: application/json');
//刪除庫存

include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'error' => 'name 為空']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM 原物料 WHERE 原物料名稱 = ?");
    $success = $stmt->execute([$name]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => '刪除失敗']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
