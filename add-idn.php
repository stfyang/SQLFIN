<?php
//新增供應商後端
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$phone = $data['phone'] ?? '';

if (!$name || !$phone) {
    http_response_code(400);
    echo json_encode(['message' => '請提供名稱與電話']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO 供應商 (供應商名稱, 電話) VALUES (?, ?)");
    $stmt->execute([$name, $phone]);
    echo json_encode(['success' => true, 'message' => '新增成功']);
} catch (PDOException $e) {
    http_response_code(400);
    $errorMsg = $e->getMessage();
    if (strpos($errorMsg, 'Duplicate entry') !== false) {
        echo json_encode([
            'success' => false,
            'message' => '名稱已存在，請使用其他名稱',
            'error' => $errorMsg
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '新增失敗',
            'error' => $errorMsg
        ]);
    }
}
