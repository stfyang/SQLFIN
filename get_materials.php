<?php
header('Content-Type: application/json');

include 'db.php';  

try {
    $stmt = $pdo->query("SELECT 原物料名稱 AS name FROM 原物料");
    $materials = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($materials, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => '查詢失敗：' . $e->getMessage()]);
}
