<?php
header('Content-Type: application/json');
include 'db.php';  
//訂單查詢
try {
    $stmt = $pdo->query("SELECT 菜品名, 單價 FROM 菜品");
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dishes, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => '查詢失敗: ' . $e->getMessage()]);
}
?>
