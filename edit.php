<?php
include 'db.php';
$id = $_GET['ID'] ?? 0;
//修改進貨單
// 撈主表
$main = $pdo->prepare("SELECT * FROM 進貨單 WHERE ID = ?");
$main->execute([$id]);
$進貨單 = $main->fetch();
if (!$進貨單) exit("查無資料");



// 撈明細
$details = $pdo->prepare("SELECT * FROM 擁有 WHERE ID = ?");
$details->execute([$id]);
$明細 = $details->fetchAll();

// 儲存更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $時間 = $_POST['時間'];
    $供應商 = $_POST['供應商名稱'];
    $原物料 = $_POST['原物料'] ?? [];
    $數量 = $_POST['數量'] ?? [];
    $價格 = $_POST['價格'] ?? [];
    $期限 = $_POST['期限'] ?? [];

    $pdo->beginTransaction();

    // 更新主表
    $pdo->prepare("UPDATE 進貨單 SET 時間=?, 供應商名稱=? WHERE ID=?")
        ->execute([$時間, $供應商, $id]);

    // 扣除原來庫存
    foreach ($明細 as $d) {
        $pdo->prepare("UPDATE 原物料 SET 庫存量 = 庫存量 - ? WHERE 原物料名稱 = ?")
            ->execute([$d['原料數量'], $d['原物料名稱']]);
    }

    // 刪除舊明細
    $pdo->prepare("DELETE FROM 擁有 WHERE ID = ?")->execute([$id]);

    // 插入新明細 + 更新庫存
    for ($i = 0; $i < count($原物料); $i++) {
        if (!$原物料[$i] || $數量[$i] <= 0) continue;
        $pdo->prepare("INSERT INTO 擁有 (原物料名稱, ID, 原料數量, 價格, 期限) VALUES (?, ?, ?, ?, ?)")
            ->execute([$原物料[$i], $id, $數量[$i], $價格[$i], $期限[$i]]);
        $pdo->prepare("UPDATE 原物料 SET 庫存量 = 庫存量 + ? WHERE 原物料名稱 = ?")
            ->execute([$數量[$i], $原物料[$i]]);
    }

    $pdo->commit();
    header("Location: edit.php?ID=$id&success=1");

    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>修改進貨單</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" />
  <script>
    <?= file_get_contents(__FILE__, false, null, __COMPILER_HALT_OFFSET__) ?>
  </script>
  <script>
function addRow() {
  const template = document.getElementById('row-template');
  const clone = template.content.cloneNode(true);
  document.getElementById('material-rows').appendChild(clone);
}

function removeRow(btn) {
  btn.closest('tr').remove();
}

function checkDuplicateMaterials() {
  const selects = document.querySelectorAll('select[name="原物料[]"]');
  const values = Array.from(selects).map(select => select.value);
  const uniqueValues = new Set(values);

  const warning = document.getElementById('duplicate-warning');
  if (values.length !== uniqueValues.size) {
    warning.style.display = 'block';
    // 自動隱藏通知
    setTimeout(() => {
      warning.style.display = 'none';
    }, 3000);
    return false;
  }

  warning.style.display = 'none';
  return true;
}
</script>

</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">✏️ 修改進貨單</h1>

    <div id="duplicate-warning" class="notification is-danger is-light" style="display:none;">
  ❌ 原物料不可重複！
</div>

<?php if (isset($_GET['success'])): ?>
  <div class="notification is-success is-light">
    ✅ 進貨單已成功修改!
  </div>
  <script>
    setTimeout(() => {
      window.location.href = "view.php?ID=<?= $id ?>";
    }, 2000);
  </script>
<?php endif; ?>


    <?php
$allMaterials = $pdo->query("SELECT 原物料名稱 FROM 原物料")->fetchAll(PDO::FETCH_COLUMN);
?>


    <form method="POST" onsubmit="return checkDuplicateMaterials()">
      <div class="field">
        <label class="label">時間</label>
        <div class="control">
          <input type="datetime-local" name="時間" class="input" value="<?= date('Y-m-d\TH:i', strtotime($進貨單['時間'])) ?>" required>
        </div>
      </div>

      <div class="field">
        <label class="label">供應商名稱</label>
        <div class="control">
          <div class="select is-fullwidth">
            <select name="供應商名稱" required>
              <?php
              $sups = $pdo->query("SELECT 供應商名稱 FROM 供應商")->fetchAll();
              foreach ($sups as $s) {
                $sel = $s['供應商名稱'] == $進貨單['供應商名稱'] ? 'selected' : '';
                echo "<option value='{$s['供應商名稱']}' $sel>{$s['供應商名稱']}</option>";
              }
              ?>
            </select>
          </div>
        </div>
      </div>

      <h2 class="subtitle">📦 原物料明細</h2>
      <table class="table is-bordered is-fullwidth">
        <thead>
          <tr>
            <th>原物料</th><th>數量</th><th>價格</th><th>期限</th><th>操作</th>
          </tr>
        </thead>
        <tbody id="material-rows">
          <?php foreach ($明細 as $row): ?>
            <tr>
              <td>
                <select class="select is-fullwidth" name="原物料[]" required>
                <?php foreach ($allMaterials as $mat): ?>
                  <option value="<?= $mat ?>" <?= $mat == $row['原物料名稱'] ? 'selected' : '' ?>><?= $mat ?></option>
                <?php endforeach; ?>
              </select>

              </td>
              <td><input type="number" class="input" name="數量[]" value="<?= $row['原料數量'] ?>" required></td>
              <td><input type="number" class="input" name="價格[]" value="<?= $row['價格'] ?>" required></td>
              <td><input type="date" class="input" name="期限[]" value="<?= $row['期限'] ?>" required></td>
              <td><button type="button" class="button is-danger is-small" onclick="removeRow(this)">刪除</button></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <button type="button" class="button is-warning is-light" onclick="addRow()">➕ 新增原物料</button>

      <div class="has-text-centered mt-4">
        <button type="submit" class="button is-success">💾 儲存修改</button>
        <a href="view.php?ID=<?= $id ?>" class="button is-light">⬅ 返回</a>
      </div>
    </form>
  </div>
</section>

<template id="row-template">
   <tr>
    <td>
      <div class="select is-fullwidth">
        <select name="原物料[]" required>
          <?php foreach ($allMaterials as $mat): ?>
            <option value="<?= $mat ?>"><?= $mat ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </td>

    <td><input type="number" class="input" name="數量[]" required></td>
    <td><input type="number" class="input" name="價格[]" required></td>
    <td><input type="date" class="input" name="期限[]" required></td>
    <td><button type="button" class="button is-danger is-small" onclick="removeRow(this)">刪除</button></td>
  </tr>
</template>

</body>
</html>
<?php __halt_compiler(); ?>
