<?php
//進貨單新增
include 'db.php';

$時間 = $_POST['時間'];
$名稱 = $_POST['供應商名稱'];
$原物料 = $_POST['原物料'];
$原料數量 = $_POST['原料數量'];
$價格 = $_POST['價格'];
$期限 = $_POST['期限'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO 進貨單 (時間, 供應商名稱) VALUES (?, ?)");
    $stmt->execute([$時間, $名稱]);
    $last_id = $pdo->lastInsertId();

    for ($i = 0; $i < count($原物料); $i++) {
        $mat = $原物料[$i];
        $qty = (int)$原料數量[$i];
        $price = (int)$價格[$i];
        $due = $期限[$i];

        if ($qty <= 0) continue;

        $pdo->prepare("INSERT INTO 擁有 (原物料名稱, ID, 原料數量, 價格, 期限) VALUES (?, ?, ?, ?, ?)")
            ->execute([$mat, $last_id, $qty, $price, $due]);

        $pdo->prepare("UPDATE 原物料 SET 庫存量 = 庫存量 + ? WHERE 原物料名稱 = ?")
            ->execute([$qty, $mat]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    exit("發生錯誤：" . $e->getMessage());
}
?>

<!DOCTYPE html>
<!-- 完成頁面 -->
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>進貨完成</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" />
  <style>
    body {
      background-color: white;
      color: #333;
      padding: 2rem 1rem;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
    }
    .box {
      background-color: #fff9db;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(255, 225, 100, 0.3);
      padding: 2rem;
    }
    .title {
      color: #222;
    }
    .buttons .button {
      background-color: #ffe164;
      color: #333;
      font-weight: bold;
      min-width: 120px;
    }
    .buttons .button:hover {
      background-color: #ffd633;
    }
  </style>
</head>
<body>
  <section class="section">
    <div class="container">
      <div class="box has-text-centered">
        <h2 class="title is-4">✅ 進貨成功！</h2>
        <p>進貨單已成功建立並更新原物料庫存。</p>
        <div class="buttons is-centered mt-4">
          <button class="button" onclick="window.history.back()">⬅ 返回上一頁</button>
          <a href="index.html" class="button">🏠 首頁</a>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
