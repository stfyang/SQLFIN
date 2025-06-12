<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>進貨單管理</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="book.css">
  <link rel="stylesheet" href="style.css" />
  <style>
    .scrollable-table {
      max-height: 400px;
      overflow-y: auto;
      border: 1px solid #ccc;
    }
    .scrollable-table table {
      width: 100%;
      border-collapse: collapse;
    }
    .scrollable-table th,
    .scrollable-table td {
      white-space: nowrap;
      padding: 0.5em;
    }
  </style>
  
</head>

<body>
<section class="section">
  <div class="container">
    <h1 class="title has-text-centered">進貨單清單</h1>

   
  <div class="has-text-centered mb-4 buttons is-centered">
  <a href="create.php" class="button is-warning">
    <i class="fas fa-plus"></i>&nbsp;新增進貨單
  </a>
  <a href="discarded.php" class="button is-info">
    查看棄置進貨單
  </a>
</div>

    </div>
 <!-- 搜尋 -->
    <form method="GET" class="box">
      <div class="field is-grouped is-justify-content-center">
        <div class="control">
          <div class="select">
            <select name="supplier">
              <option value="">📋 所有供應商</option>
              <?php
              $suppliers = $pdo->query("SELECT DISTINCT 供應商名稱 FROM 進貨單")->fetchAll();
              foreach ($suppliers as $s) {
                  $selected = ($_GET['supplier'] ?? '') == $s['供應商名稱'] ? 'selected' : '';
                  echo "<option value='{$s['供應商名稱']}' $selected>{$s['供應商名稱']}</option>";
              }
              ?>
            </select>
          </div>
        </div>
        <div class="control">
          <button class="button is-info" type="submit">
            <i class="fas fa-search"></i>&nbsp;查詢
          </button>
        </div>
      </div>
    </form>
    <!-- 表格顯示 -->
    <div class="scrollable-table">
      <table class="table is-bordered is-striped is-fullwidth is-narrow">
        <thead>
          <tr>
            <th>ID</th>
            <th>時間</th>
            <th>供應商</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $supplier = $_GET['supplier'] ?? '';
        if ($supplier) {
            $stmt = $pdo->prepare("SELECT * FROM 進貨單 WHERE 供應商名稱 = ?");
            $stmt->execute([$supplier]);
            $rows = $stmt->fetchAll();
        } else {
            $rows = $pdo->query("SELECT * FROM 進貨單")->fetchAll();
        }
        // 載入棄置列表
$discardFile = 'discard_log.json';
$discardedIDs = file_exists($discardFile) ? json_decode(file_get_contents($discardFile), true) : [];

// 過濾掉棄置單
$rows = array_filter($rows, function($row) use ($discardedIDs) {
    return !in_array($row['ID'], $discardedIDs);
});


        foreach ($rows as $row) {
            echo "<tr>
              <td>{$row['ID']}</td>
              <td>{$row['時間']}</td>
              <td>{$row['供應商名稱']}</td>
              <td>
                <a class='button is-small is-primary' href='view.php?ID={$row['ID']}'>
                  <i class='fas fa-eye'></i>&nbsp;查看明細
                </a>
              </td>
            </tr>";
        }
        ?>
        </tbody>
      </table>
    </div>

    <div class="has-text-centered mt-4">
      <a href="index.html" class="button is-small is-dark home-button">
        <i class="fas fa-home"></i>&nbsp;首頁
      </a>
    </div>
  </div>
</section>

</body>
</html>
