<?php
//進貨單棄置
include 'db.php'; 

$id = $_GET['ID'] ?? 0;
if (!$id) {
    header("Location: in.php");
    exit;
}

// 棄置紀錄
$file = 'discard_log.json';
$log = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// 加入新棄置紀錄
if (!in_array($id, $log)) {
    $log[] = $id;
    file_put_contents($file, json_encode($log));
}

header("Location: view.php?ID=$id&discarded=1");
exit;
