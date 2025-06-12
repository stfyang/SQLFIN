<?php
header('Content-Type: application/json');
//刪除供應商
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = $data['name'] ?? '';

if (!$name) {
    http_response_code(400);
    echo json_encode(['message' => '缺少供應商名稱']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM 供應商 WHERE 供應商名稱 = ?");
    $success = $stmt->execute([$name]);

    if ($success) {
        echo json_encode(['message' => '刪除成功']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => '刪除失敗']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => '刪除失敗', 'error' => $e->getMessage()]);
}
