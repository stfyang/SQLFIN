document.addEventListener('DOMContentLoaded', function () {
  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');
  const tableBody = document.getElementById('purchaseTableBody');

  const editModal = document.getElementById('editSinModal');
  const closeEditBtn = document.getElementById('closeEditModalBtn');
  const cancelEditBtn = document.getElementById('cancelEditModalBtn');
  const saveEditBtn = document.getElementById('saveEditBtn');

  // Modal 欄位
  const editIdInput = document.getElementById('editinName'); // 這裡實際上是 id
  const editMaterialInput = document.getElementById('editMaterial');
  const editQuantityInput = document.getElementById('editQuantity');

  // 查詢資料
  searchBtn.addEventListener('click', () => {
    const keyword = searchInput.value.trim();

    axios.get('queryin.php', {
      params: { supplier: keyword }
    })
    .then(response => {
      const data = response.data;
      tableBody.innerHTML = ''; // 清空原本的資料
      data.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${item.id}</td>
          <td>${item.time}</td>
          <td>${item.supplier}</td>
          <td>${item.material}</td>
          <td>${item.quantity}</td>
          <td><button class="button is-small is-warning edit-btn" data-id="${item.id}">修改</button></td>
        `;
        tableBody.appendChild(tr);
      });

      // 加上修改按鈕事件
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const row = btn.closest('tr');
          editIdInput.value = id;
          editMaterialInput.value = row.children[3].textContent;
          editQuantityInput.value = row.children[4].textContent;
          editModal.classList.add('is-active');
        });
      });
    })
    .catch(error => {
      console.error('查詢失敗：', error);
    });
  });

  // 關閉 modal
  closeEditBtn.addEventListener('click', () => editModal.classList.remove('is-active'));
  cancelEditBtn.addEventListener('click', () => editModal.classList.remove('is-active'));

  // 儲存修改
  saveEditBtn.addEventListener('click', () => {
    const id = editIdInput.value;
    const material = editMaterialInput.value.trim();
    const quantity = editQuantityInput.value.trim();

    axios.post('addin.php', {
      id,
      material,
      quantity
    })
    .then(response => {
      alert('修改成功');
      editModal.classList.remove('is-active');
      searchBtn.click(); // 重新查詢更新畫面
    })
    .catch(error => {
      alert('修改失敗');
      console.error(error);
    });
  });
});
