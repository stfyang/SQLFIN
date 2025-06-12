<?php
//刪除菜單
header('Content-Type: application/json; charset=utf-8');

include 'db.php'; 

// 取得輸入資料
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'error' => '未提供菜品名稱']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt1 = $pdo->prepare("DELETE FROM `消耗` WHERE `菜品名` = ?");
    $stmt1->execute([$name]);

    $stmt2 = $pdo->prepare("DELETE FROM `菜品` WHERE `菜品名` = ?");
    $stmt2->execute([$name]);

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
