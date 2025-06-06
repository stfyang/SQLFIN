<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM materials ORDER BY name");
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($materials);
