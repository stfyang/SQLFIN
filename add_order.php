<?php
//訂單新增相關
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$dishes = $data['dishes'] ?? [];

if (count($dishes) === 0) {
    echo json_encode(['error' => '未選擇餐點']);
    exit;
}

// 檢查庫存
$insufficient = [];

foreach ($dishes as $dish => $qty) {
    $stmt = $pdo->prepare("SELECT 原物料名稱, 消耗物料量 FROM 消耗 WHERE 菜品名 = ?");
    $stmt->execute([$dish]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $material = $row['原物料名稱'];
        $needed = $row['消耗物料量'] * $qty;

        // 查詢庫存量
        $stockStmt = $pdo->prepare("SELECT 庫存量 FROM 原物料 WHERE 原物料名稱 = ?");
        $stockStmt->execute([$material]);
        $stock = $stockStmt->fetchColumn();

        if ($stock === false) {
            echo json_encode(['error' => "找不到原物料：$material"]);
            exit;
        }

        if ($stock < $needed) {
            $maxPortions = floor($stock / $row['消耗物料量']);
            $insufficient[] = ['name' => $material, 'max' => $maxPortions];
        }
    }
}

if (count($insufficient) > 0) {
    $messages = [];
    foreach ($insufficient as $item) {
        $messages[] = "{$item['name']} 庫存不足，最多可製作 {$item['max']} 份";
    }
    echo json_encode([
        'error' => '原物料不足，無法建立訂單。',
        'details' => $messages
    ]);
    exit;
}

// 計算總價
$total = 0;
foreach ($dishes as $name => $qty) {
    $stmt = $pdo->prepare("SELECT 單價 FROM 菜品 WHERE 菜品名 = ?");
    $stmt->execute([$name]);
    $price = $stmt->fetchColumn();

    if ($price === false) {
        echo json_encode(['error' => "找不到菜品：$name"]);
        exit;
    }

    $total += $price * $qty;
}

// 建立訂單
$now = date("Y-m-d H:i:s");

$stmt = $pdo->prepare("INSERT INTO 訂單 (點餐日期與時間, 小計) VALUES (?, ?)");
$stmt->execute([$now, $total]);
$orderId = $pdo->lastInsertId();

// 插入包含表
$stmt = $pdo->prepare("INSERT INTO 包含 (訂單編號, 菜品名, 數量) VALUES (?, ?, ?)");
foreach ($dishes as $dish => $qty) {
    $stmt->execute([$orderId, $dish, $qty]);
}

// 扣除庫存
foreach ($dishes as $dish => $qty) {
    $stmt = $pdo->prepare("SELECT 原物料名稱, 消耗物料量 FROM 消耗 WHERE 菜品名 = ?");
    $stmt->execute([$dish]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $material = $row['原物料名稱'];
        $need = $row['消耗物料量'] * $qty;

        $updateStmt = $pdo->prepare("UPDATE 原物料 SET 庫存量 = 庫存量 - ? WHERE 原物料名稱 = ?");
        $updateStmt->execute([$need, $material]);
    }
}

echo json_encode(['message' => '訂單新增成功']);
