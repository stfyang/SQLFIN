<?php
include 'db.php';

$id = $_GET['ID'] ?? 0;
if (!$id) {
    echo "缺少進貨單 ID";
    exit;
}

$main = $pdo->prepare("SELECT * FROM 進貨單 WHERE ID = ?");
$main->execute([$id]);
$進貨單 = $main->fetch();

if (!$進貨單) {
    echo "查無此進貨單";
    exit;
}

// 判斷是否棄置
function isDiscarded($id) {
    $logFile = 'discard_log.json';
    $list = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
    return in_array($id, $list);
}

$isDiscarded = isDiscarded($id);

$details = $pdo->prepare("SELECT * FROM 擁有 WHERE ID = ?");
$details->execute([$id]);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>進貨單明細</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" />
  <style>
    body {
      background-color: white;
      color: #333;
      padding: 2rem 1rem;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
    }
    .box {
      background-color: #fff9db;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(255, 225, 100, 0.3);
      padding: 2rem;
    }
    table {
      width: 100%;
    }
    thead {
      background-color: #ffe164;
    }
    tbody tr:nth-child(odd) {
      background-color: #fffbe0;
    }
    tbody tr:nth-child(even) {
      background-color: #fff9db;
    }
    th, td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #ccc;
    }
    .button {
      background-color: #ffe164;
      color: #333;
      font-weight: bold;
    }
    .button:hover {
      background-color: #ffd633;
    }
  </style>
</head>
<body>
  <section class="section">
    <div class="container">

      <?php if (isset($_GET['discarded'])): ?>
        <div class="notification is-warning is-light">
          ⚠️ 此進貨單已成功棄置，不再顯示於清單中，但仍可查閱明細。
        </div>
      <?php endif; ?>

      <div class="box">
        <h2 class="title is-4">📄 進貨單明細</h2>

        <p><strong>進貨單編號：</strong><?= $進貨單['ID'] ?></p>
        <p><strong>供應商：</strong><?= htmlspecialchars($進貨單['供應商名稱']) ?></p>
        <p><strong>時間：</strong><?= $進貨單['時間'] ?></p>

        <h3 class="subtitle is-5 mt-4">📦 原物料項目</h3>
        <table class="table is-fullwidth is-striped is-hoverable">
          <thead>
            <tr>
              <th>原物料名稱</th>
              <th>數量</th>
              <th>價格</th>
              <th>期限</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($details as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['原物料名稱']) ?></td>
                <td><?= $item['原料數量'] ?></td>
                <td><?= $item['價格'] ?></td>
                <td><?= $item['期限'] ?><?php if ($item['期限'] < date('Y-m-d')): ?>
    <span class="tag is-danger is-light">已過期</span>
  <?php endif; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="buttons is-centered mt-4">
          <?php if ($isDiscarded): ?>
            <span class="tag is-danger is-light">🗑️ 此進貨單已被棄置</span>
          <?php else: ?>
            <a href="edit.php?ID=<?= $進貨單['ID'] ?>" class="button is-warning">
              ✏️ 修改進貨單
            </a>
            <a href="discard_purchase.php?ID=<?= $進貨單['ID'] ?>" 
               class="button is-danger"
               onclick="return confirm('確定要棄置這張進貨單嗎？')">
              🗑️ 棄置進貨單
            </a>
          <?php endif; ?>
          <a href="in.php" class="button is-light">⬅ 返回列表</a>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
