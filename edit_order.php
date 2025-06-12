<?php
include 'db.php';

$orderId = intval($_GET['id']);

// 讀取所有菜品與單價
$menu = [];
$stmt = $pdo->query("SELECT * FROM 菜品");
while ($row = $stmt->fetch()) {
    $menu[$row['菜品名']] = $row['單價'];
}

// 讀取這張訂單包含的菜品與數量
$items = [];
$stmt = $pdo->prepare("SELECT 菜品名, 數量 FROM 包含 WHERE 訂單編號 = ?");
$stmt->execute([$orderId]);
while ($row = $stmt->fetch()) {
    $name = $row['菜品名'];
    $qty = $row['數量'];
    $items[$name] = $qty;
}

?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>修改訂單</title>
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
    .table {
      background-color: #fff9db;
      border-radius: 6px;
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
    }
    .button.is-info:hover {
      background-color: #ffd633;
    }
    .button.is-danger {
      min-width: 80px;
    }

  </style>
</head>
<body class="section">
<div class="container">
  <h1 class="title">✏️ 修改訂單 #<?= htmlspecialchars($orderId) ?></h1>

  <div class="field is-grouped">
    <div class="control">
      <div class="select">
        <select id="dishSelect">
          <?php foreach ($menu as $name => $price): ?>
            <option value="<?= htmlspecialchars($name) ?>" data-price="<?= $price ?>">
              <?= htmlspecialchars($name) ?>（<?= $price ?>元）
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="control">
      <input class="input" type="number" id="dishQty" min="1" value="1">
    </div>
    <div class="control">
      <button class="button is-info" type="button" onclick="addToCart()">➕ 加入</button>
    </div>
  </div>

  <h2 class="subtitle">🛒 編輯清單</h2>
  <form method="post" action="update_order.php" onsubmit="return confirm('確定要儲存修改？')">
    <input type="hidden" name="id" value="<?= htmlspecialchars($orderId) ?>">
    <table class="table is-fullwidth is-striped is-hoverable">
      <thead>
        <tr>
          <th>菜品</th>
          <th>數量</th>
          <th>單價</th>
          <th>小計</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="cartBody"></tbody>
    </table>

    <div class="field">
      <label class="label">總價</label>
      <input class="input" id="total" readonly>
    </div>

<div class="buttons mt-4">
  <button class="button is-info">✅ 儲存修改</button>
  <a href="order_query.php" class="button is-info">⬅ 返回查詢</a>
</div>

  </form>
</div>

<script>
const cart = <?= json_encode($items, JSON_UNESCAPED_UNICODE) ?>;
const menu = <?= json_encode($menu, JSON_UNESCAPED_UNICODE) ?>;

function renderCart() {
  const tbody = document.getElementById('cartBody');
  tbody.innerHTML = '';
  let total = 0;

  for (const [name, qty] of Object.entries(cart)) {
    if (!(name in menu)) continue;

    const price = menu[name];
    const subtotal = qty * price;
    total += subtotal;

    const row = document.createElement('tr');
    row.innerHTML = `
      <td>
        <input type="hidden" name="dish[]" value="${name}">
        ${name}
      </td>
      <td>
        <input class="input qty-input" data-name="${name}" type="number" name="qty[]" min="1" value="${qty}">
      </td>
      <td>${price}</td>
      <td>${subtotal}</td>
      <td><button class="button is-small is-danger remove-btn" data-name="${name}" type="button">刪除</button></td>
    `;
    tbody.appendChild(row);
  }

  document.getElementById('total').value = total + ' 元';

  // 綁定事件
  document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      const name = e.target.getAttribute('data-name');
      removeItem(name);
    });
  });

  document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', e => {
      const name = e.target.getAttribute('data-name');
      const qty = parseInt(e.target.value);
      updateQty(name, qty);
    });
  });
}


function addToCart() {
  const select = document.getElementById('dishSelect');
  const qty = parseInt(document.getElementById('dishQty').value);
  const name = select.value;

  if (!name || qty <= 0) return;
  cart[name] = (cart[name] || 0) + qty;
  renderCart();
}

function removeItem(name) {
  delete cart[name];
  renderCart();
}

function updateQty(name, qty) {
  qty = parseInt(qty);
  if (qty > 0) {
    cart[name] = qty;
  } else {
    delete cart[name];
  }
  renderCart();
}

renderCart();
</script>
</body>
</html>
