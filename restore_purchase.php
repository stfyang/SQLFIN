<?php
include 'db.php'; 
$id = $_GET['ID'] ?? 0;
if (!$id) {
    header("Location: discarded.php");
    exit;
}

$logFile = 'discard_log.json';
$list = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

if (in_array($id, $list)) {
    $list = array_values(array_filter($list, fn($x) => $x != $id));
    file_put_contents($logFile, json_encode($list));
}

header("Location: discarded.php?restored=1");
exit;
