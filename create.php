<?php
include 'db.php';
include 'expire_cleanup.php'; // 每次打開頁面自動檢查過期原料
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>新增進貨單</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="book.css">

  <script>
    function addRow() {
      const row = document.getElementById('template').cloneNode(true);
      row.style.display = 'table-row';
      row.removeAttribute('id');

      const delCell = document.createElement('td');
      delCell.innerHTML = `<button class="button is-danger is-light is-small" onclick="removeRow(this)">
        <i class="fas fa-trash"></i></button>`;
      row.appendChild(delCell);

      document.getElementById('material-rows').appendChild(row);
    }

    function removeRow(button) {
      button.closest('tr').remove();
    }

    function checkDuplicateMaterials() {
  const rows = document.querySelectorAll('#material-rows tr');
  const selectedValues = [];

  for (let row of rows) {
    if (row.style.display === 'none') continue; 

    const material = row.querySelector('select[name="原物料[]"]');
    const quantity = row.querySelector('input[name="原料數量[]"]');
    const price = row.querySelector('input[name="價格[]"]');
    const expire = row.querySelector('input[name="期限[]"]');

    if (!material || !quantity || !price || !expire) continue;

    if (material.value === '') {
      alert("❗請選擇原物料，不能為空。");
      return false;
    }

    if (selectedValues.includes(material.value)) {
      alert(`❗原物料「${material.value}」重複，請刪除其中一筆。`);
      return false;
    }

    selectedValues.push(material.value);

    if (quantity.value === '') {
      alert("❗請填寫數量，不能為空。");
      return false;
    }

    if (price.value === '') {
      alert("❗請填寫價格，不能為空。");
      return false;
    }

    if (expire.value === '') {
      alert("❗請填寫期限，不能為空。");
      return false;
    }
  }

  return true;
}


    window.onload = () => {
      addRow(); // 預設先新增一筆
    }
  </script>
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title has-text-centered">新增進貨單</h1>

    <form action="create_action.php" method="POST" onsubmit="return checkDuplicateMaterials()">
      <div class="field">
        <label class="label">進貨時間</label>
        <div class="control">
          <input class="input" type="datetime-local" name="時間" required>
        </div>
      </div>

      <div class="field">
        <label class="label">供應商名稱</label>
        <div class="control">
          <div class="select">
            <select name="供應商名稱">
              <?php
              $suppliers = $pdo->query("SELECT 供應商名稱 FROM 供應商")->fetchAll();
              foreach ($suppliers as $s) {
                  echo "<option value='{$s['供應商名稱']}'>{$s['供應商名稱']}</option>";
              }
              ?>
            </select>
          </div>
        </div>
      </div>

      <div class="table-container">
        <table class="table is-bordered is-fullwidth">
          <thead>
            <tr>
              <th>原物料</th>
              <th>數量</th>
              <th>價格</th>
              <th>期限</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody id="material-rows">
            <tr id="template" style="display:none;">
              <td>
                <div class="select is-fullwidth">
                  <select name="原物料[]">
                    <option value="">請選擇原物料</option>
                    <?php
                    $materials = $pdo->query("SELECT 原物料名稱 FROM 原物料")->fetchAll();
                    foreach ($materials as $m) {
                        echo "<option value='{$m['原物料名稱']}'>{$m['原物料名稱']}</option>";
                    }
                    ?>
                  </select>
                </div>
              </td>
              <td><input class="input" type="number" name="原料數量[]"></td>
              <td><input class="input" type="number" name="價格[]"></td>
              <td><input class="input" type="date" name="期限[]"></td>
              <!-- 刪除按鈕動態加 -->
            </tr>
          </tbody>
        </table>
      </div>

      <div class="has-text-centered my-4">
        <button type="button" class="button is-warning" onclick="addRow()">
          <i class="fas fa-plus"></i>&nbsp;新增原物料
        </button>
      </div>

      <div class="has-text-centered">
        <input type="submit" class="button is-success" value="✅ 送出進貨單">
        <a href="in.php" class="button is-light ml-2">
          <i class="fas fa-arrow-left"></i>&nbsp;回上一頁
        </a>
      </div>
    </form>
  </div>
</section>
</body>
</html>
