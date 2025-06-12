<?php
include 'db.php';

$today = date('Y-m-d');

// 查詢所有過期的原物料
$stmt = $pdo->prepare("SELECT * FROM 擁有 WHERE 期限 < ?");
$stmt->execute([$today]);
$expired = $stmt->fetchAll();

foreach ($expired as $batch) {
    $mat = $batch['原物料名稱'];
    $qty = $batch['原料數量'];

    // 減少原物料庫存 -< 刪除此功能，因可能和新增訂單扣除衝突(扣兩遍)且易造成混亂
  /*   $pdo->prepare("UPDATE 原物料 SET 庫存量 = GREATEST(庫存量 - ?, 0) WHERE 原物料名稱 = ?")
        ->execute([$qty, $mat]); */

    // 不刪除資料，保留進貨記錄
}

echo "";
?>
