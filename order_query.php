<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>訂單查詢</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" />
  <style>

    body {
      background-color: white;
      color: #333;
      padding: 2rem 1rem;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
    }
    h1.title {
      color: #333;
      margin-bottom: 2rem;
    }
    table.table {
      background-color: #fff9db; /* 淡淡黃色背景 */
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 0 6px rgba(255, 225, 100, 0.3);
    }
    thead tr {
      background-color: #ffe164;
      color: #222;
    }
    tbody tr:nth-child(odd) {
      background-color: #fffbe0;
    }
    tbody tr:nth-child(even) {
      background-color: #fff9db;
    }
    .button.is-info {
      background-color: #ffe164;
      color: #333;
      border: none;
      min-width: 80px;
    }
    .button.is-info:hover {
      background-color: #ffd633;
      color: #222;
    }
    .button.is-danger {
      min-width: 80px;
    }
    a.button.is-link {
      background-color: #ffe164;
      color: #333;
      border: none;
      margin-top: 1.5rem;
      min-width: 120px;
      font-weight: bold;
    }
    a.button.is-link:hover {
      background-color: #ffd633;
      color: #222;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="title">📋 訂單查詢</h1>
    <table class="table is-fullwidth is-striped is-hoverable">
      <thead>
        <tr>
          <th>訂單編號</th>
          <th>時間</th>
          <th>菜品清單</th>
          <th>總數量</th>
          <th>總金額</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
       <?php
include 'db.php'; 

// 抓所有訂單
$stmtOrders = $pdo->query("SELECT * FROM 訂單 ORDER BY 訂單編號 DESC");
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// 查詢包含菜品
$stmtItems = $pdo->prepare("SELECT 菜品名, 數量 FROM 包含 WHERE 訂單編號 = ?");

foreach ($orders as $order) {
    $orderId = $order['訂單編號'];

    $stmtItems->execute([$orderId]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    $dishCount = [];
    $totalQty = 0;
    foreach ($items as $item) {
        $name = $item['菜品名'];
        $qty = $item['數量'];
        $dishCount[$name] = $qty;
        $totalQty += $qty;
    }

    $dishDisplay = '';
    foreach ($dishCount as $name => $qty) {
        $dishDisplay .= htmlspecialchars($name) . " × " . $qty . "<br>";
    }

    echo "<tr>
        <td>{$orderId}</td>
        <td>{$order['點餐日期與時間']}</td>
        <td>{$dishDisplay}</td>
        <td>{$totalQty}</td>
        <td>{$order['小計']}</td>
        <td>
          <a class='button is-info' href='edit_order.php?id={$orderId}'>修改</a>
          <a class='button is-danger' href='delete_order.php?id={$orderId}' onclick='return confirm(\"確定要刪除這筆訂單嗎？\")'>刪除</a>
        </td>
    </tr>";
}

?>

      </tbody>
    </table>

    <a href="order.html" class="button is-link">⬅ 返回點餐</a>
  </div>
 <!-- 通知-->
<div id="toast" class="notification" style="display:none; position: fixed; top: 20px; right: 20px; z-index: 1000;">
  <span id="toast-message">操作結果</span>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const toast = document.getElementById('toast');
    const message = document.getElementById('toast-message');

    let type = ''; 
    let text = ''; // 要顯示的文字

    if (params.get('delete') === 'success') {
      type = 'is-success';
      text = '刪除成功！';
    } else if (params.get('delete') === 'fail') {
      type = 'is-danger';
      text = '刪除失敗！';
    } else if (params.get('update') === 'success') {
      type = 'is-success';
      text = '修改成功！';
    } else if (params.get('update') === 'fail') {
      type = 'is-danger';
      text = '修改失敗！';
    }

    if (type && text) {
      toast.className = `notification ${type}`;
      message.textContent = text;
      toast.style.display = 'block';

      setTimeout(() => {
        toast.style.display = 'none';
        history.replaceState(null, '', window.location.pathname); // 移除參數
      }, 3000);
    }
  });
</script>



</body>
</html>
