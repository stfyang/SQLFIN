<?php
include 'db.php';

$logFile = 'discard_log.json';
$discardedIDs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

if (empty($discardedIDs)) {
    $rows = [];
} else {
    $placeholders = implode(',', array_fill(0, count($discardedIDs), '?'));
    $stmt = $pdo->prepare("SELECT * FROM 進貨單 WHERE ID IN ($placeholders)");
    $stmt->execute($discardedIDs);
    $rows = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>棄置進貨單清單</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <style>
    body {
      padding: 2rem;
      background-color: #f8f8f8;
    }
    .box {
      background-color: #fff9db;
    }
  </style>
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title has-text-centered">🗃️ 棄置進貨單清單</h1>

    <?php if (isset($_GET['restored'])): ?>
      <div class="notification is-success is-light">✅ 進貨單已成功還原。</div>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
      <div class="notification is-info is-light has-text-centered">
        目前沒有被棄置的進貨單。
      </div>
    <?php else: ?>
      <div class="table-container box mt-4">
        <table class="table is-striped is-fullwidth is-hoverable">
          <thead>
            <tr>
              <th>ID</th>
              <th>時間</th>
              <th>供應商</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= $row['ID'] ?></td>
                <td><?= $row['時間'] ?></td>
                <td><?= htmlspecialchars($row['供應商名稱']) ?></td>
                <td>
                  <a href="view.php?ID=<?= $row['ID'] ?>" class="button is-small is-info">
                    <i class="fas fa-eye"></i>&nbsp;查看
                  </a>
                  <a href="restore_purchase.php?ID=<?= $row['ID'] ?>" 
                     class="button is-small is-success" 
                     onclick="return confirm('還原此進貨單？')">
                    <i class="fas fa-undo"></i>&nbsp;還原
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="has-text-centered mt-4">
      <a href="in.php" class="button is-dark is-light">
        <i class="fas fa-arrow-left"></i>&nbsp;返回進貨清單
      </a>
    </div>
  </div>
</section>
</body>
</html>
