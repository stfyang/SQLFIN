<?php
include 'db.php';

$id = intval($_POST['id']);
$dishes = $_POST['dish'] ?? [];
$qtys = $_POST['qty'] ?? [];

if (count($dishes) !== count($qtys)) {
    die("資料不一致");
}

try {
    $pdo->beginTransaction();

    // 刪除原本的內容
    $stmt = $pdo->prepare("DELETE FROM 包含 WHERE 訂單編號 = ?");
    $stmt->execute([$id]);


    $stmt_price = $pdo->prepare("SELECT 單價 FROM 菜品 WHERE 菜品名 = ?");
    $insert = $pdo->prepare("INSERT INTO 包含 (訂單編號, 菜品名, 數量) VALUES (?, ?, ?)");

    $totalAmount = 0;
    $priceCache = [];

    foreach ($dishes as $i => $dishName) {
        $dishName = trim($dishName);
        $qty = intval($qtys[$i]);

        if ($dishName === '' || $qty <= 0) continue;

        // 插入一筆資料
        $insert->execute([$id, $dishName, $qty]);

        // 查單價
        if (!isset($priceCache[$dishName])) {
            $stmt_price->execute([$dishName]);
            $priceCache[$dishName] = $stmt_price->fetchColumn();
        }

        $price = $priceCache[$dishName];
        if ($price !== false) {
            $totalAmount += $qty * $price;
        }
    }

    // 更新訂單總資料
    $stmt_update = $pdo->prepare("UPDATE 訂單 SET 小計 = ? WHERE 訂單編號 = ?");
    $stmt_update->execute([$totalAmount, $id]);

    $pdo->commit();
    header("Location: order_query.php?update=success");


    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("發生錯誤: " . htmlspecialchars($e->getMessage()));
}

