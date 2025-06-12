<?php
header('Content-Type: application/json');
include 'db.php'; 


$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');

// 防呆->欄位不能空
if (!$name || !$phone) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '❌ 名稱與電話不得為空']);
    exit;
}


// 防呆->電話格式8~10碼
if (!preg_match('/^0\d{7,9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '❌ 電話格式錯誤，請輸入正確號碼']);
    exit;
}

// 執行更新
try {
    $sql = "UPDATE 供應商 SET 電話 = :phone WHERE 供應商名稱 = :name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => '✅ 更新成功']);
    } else {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => '⚠️ 無資料變動（名稱不存在或電話相同）']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '❌ 資料庫錯誤: ' . $e->getMessage()]);
}
