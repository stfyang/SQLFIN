<?php
header('Content-Type: application/json');
//菜品查詢
include 'db.php';

try {
    // 查詢所有菜品
    $dish_stmt = $pdo->query("SELECT `菜品名`, `單價` FROM `菜品`");
    $dishes = [];

    while ($dish = $dish_stmt->fetch()) {
        $dish_name = $dish['菜品名'];
        $price = $dish['單價'];

        // 查詢消耗的原物料
        $mat_stmt = $pdo->prepare("SELECT `原物料名稱`, `消耗物料量` FROM `消耗` WHERE `菜品名` = ?");
        $mat_stmt->execute([$dish_name]);
        $materials = $mat_stmt->fetchAll();

        // 結果
        $materials_arr = [];
        foreach ($materials as $m) {
            $materials_arr[] = [
                'material_name' => $m['原物料名稱'],
                'quantity' => $m['消耗物料量'],
            ];
        }

        $dishes[] = [
            'name' => $dish_name,
            'price' => $price,
            'materials' => $materials_arr,
        ];
    }

    echo json_encode(['success' => true, 'dishes' => $dishes], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
