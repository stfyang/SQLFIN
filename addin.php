<?php
header('Content-Type: application/json');
//進貨單新增後端

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// 除錯
if ($data === null) {
    echo json_encode(["message" => "未收到有效資料", "raw" => $rawData]);
    exit;
}

$name = $data['name'] ?? '';
$material = $data['material'] ?? '';
$quantity = $data['quantity'] ?? '';

// 除錯->列出收到的值
if (empty($name) || empty($material) || empty($quantity)) {
    echo json_encode([
        "message" => "缺少必要欄位",
        "received" => [
            "name" => $name,
            "material" => $material,
            "quantity" => $quantity
        ]
    ]);
    exit;
}

include 'db.php';

try {
    $sql = "INSERT INTO 進貨單 (時間, 供應商名稱, 原料, 數量) VALUES (NOW(), ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $material, $quantity]);
    echo json_encode(["message" => "新增成功"]);
} catch (PDOException $e) {
    echo json_encode(["message" => "新增失敗", "error" => $e->getMessage()]);
}
