<?php
//刪除訂單
include 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM 訂單 WHERE 訂單編號 = ?");
    if ($stmt->execute([$id])) {
        // 刪除成功
        header("Location: order_query.php?delete=success");
        exit;
    } else {
        // 刪除失敗
        header("Location: order_query.php?delete=fail");
        exit;
    }
} else {
    header("Location: order_query.php");
    exit;
}
?>
