<?php
header('Content-Type: application/json');
//供應商列表
include 'db.php';

try {
    $stmt = $pdo->query("SELECT 供應商名稱 FROM 供應商");
    $suppliers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(["suppliers" => $suppliers], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // 查詢失敗回傳空陣列或錯誤訊息
    echo json_encode(["suppliers" => [], "error" => $e->getMessage()]);
}
