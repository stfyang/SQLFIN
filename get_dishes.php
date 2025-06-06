<?php
require 'db.php';

// 撈菜品
$stmt = $pdo->query("SELECT * FROM dishes ORDER BY id DESC");
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 撈所有菜品原物料關聯
$stmt2 = $pdo->query("SELECT dm.*, m.name as material_name FROM dish_materials dm JOIN materials m ON dm.material_id = m.id");
$materials = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// 把原物料依 dish_id 分組
$dishMaterialsMap = [];
foreach ($materials as $m) {
    $dishMaterialsMap[$m['dish_id']][] = [
        'material_id' => $m['material_id'],
        'material_name' => $m['material_name'],
        'quantity' => $m['quantity'],
    ];
}

// 組合結果
foreach ($dishes as &$dish) {
    $dish['materials'] = $dishMaterialsMap[$dish['id']] ?? [];
}

header('Content-Type: application/json');
echo json_encode($dishes);
