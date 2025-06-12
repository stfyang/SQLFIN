<?php
header('Content-Type: application/json');


include 'db.php'; 

try {
   
    $sql = "SELECT 原物料名稱 AS name, 安全庫存量 AS safe_quantity, 種類 AS category, 庫存量 AS quantity FROM 原物料";

   
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => '查詢失敗: ' . $e->getMessage()]);
}
?>
