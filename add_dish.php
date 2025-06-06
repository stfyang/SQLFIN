<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$price = $data['price'] ?? 0;
$materials = $data['materials'] ?? [];

if (!$name || !$price) {
    http_response_code(400);
    echo json_encode(['error' => '菜品名稱及單價不可為空']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO dishes (name, price) VALUES (?, ?)");
    $stmt->execute([$name, $price]);
    $dish_id = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO dish_materials (dish_id, material_id, quantity) VALUES (?, ?, ?)");
    foreach ($materials as $m) {
        if (!$m['material_id'] || !$m['quantity']) continue;
        $stmt2->execute([$dish_id, $m['material_id'], $m['quantity']]);
    }

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
