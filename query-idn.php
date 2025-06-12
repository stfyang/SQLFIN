<?php
//查供應商
header('Content-Type: application/json');
include 'db.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$name = isset($data['name']) ? trim($data['name']) : '';

try {
    if ($name !== '') {
        $search = '%' . $name . '%';
        $stmt = $pdo->prepare("SELECT 供應商名稱, 電話 FROM 供應商 WHERE 供應商名稱 LIKE ? OR 電話 LIKE ?");
        $stmt->execute([$search, $search]);
    } else {
        $stmt = $pdo->query("SELECT 供應商名稱, 電話 FROM 供應商");
    }

    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($suppliers, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'SQL 錯誤: ' . $e->getMessage()]);
}
?>
