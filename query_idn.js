document.addEventListener('DOMContentLoaded', () => {
  // === 首頁按鈕導航 ===
  document.getElementById('querySupplierBtn')?.addEventListener('click', () => {
    window.location.href = 'queryidn.html';
  });

  // === 開啟 / 關閉新增進貨彈窗 ===
  const addPurchaseModal = document.getElementById('addPurchaseModal');
  document.getElementById('openAddPurchaseBtn')?.addEventListener('click', () => {
    addPurchaseModal?.classList.add('is-active');
  });
  document.getElementById('closePurchaseModalBtn')?.addEventListener('click', () => {
    addPurchaseModal?.classList.remove('is-active');
  });
  document.getElementById('cancelPurchaseModalBtn')?.addEventListener('click', () => {
    addPurchaseModal?.classList.remove('is-active');
  });
  document.getElementById('savePurchaseBtn')?.addEventListener('click', () => {
    const supplier = document.getElementById('supplierName').value.trim();
    const material = document.getElementById('materialName').value.trim();
    const quantity = document.getElementById('quantity').value.trim();
    if (!supplier || !material || !quantity) {
      alert('請填寫所有欄位');
      return;
    }
    alert(`送出資料：\n供應商：${supplier}\n原料：${material}\n數量：${quantity}`);
    addPurchaseModal.classList.remove('is-active');
  });

  // === 查詢與編輯供應商功能 ===
  const tableBody = document.getElementById('supplierTableBody');
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const editModal = document.getElementById('editSupplierModal');

  function fetchSuppliers(name = '') {
    axios.post('query-idn.php', { name })
      .then(res => {
        tableBody.innerHTML = '';
        const data = res.data;

        if (!Array.isArray(data)) {
          alert('資料格式錯誤');
          console.error(data);
          return;
        }

        if (data.length === 0) {
          tableBody.innerHTML = '<tr><td colspan="3">查無資料</td></tr>';
        } else {
          data.forEach(supplier => {
            const safeName = supplier.名稱.replace(/'/g, "\\'");
            const safePhone = supplier.電話.replace(/'/g, "\\'");
            const row = `
              <tr>
                <td>${supplier.名稱}</td>
                <td>${supplier.電話}</td>
                <td>
                  <button class="button is-small is-warning" onclick="openEditModal('${safeName}', '${safePhone}')">修改</button>
                  <button class="button is-small is-danger" onclick="deleteSupplier('${safeName}')">刪除</button>
                </td>
              </tr>`;
            tableBody.innerHTML += row;
          });
        }
      })
      .catch(err => {
        alert('查詢失敗');
        console.error(err);
      });
  }

  searchBtn?.addEventListener('click', () => {
    const keyword = searchInput.value.trim();
    fetchSuppliers(keyword);
  });

  fetchSuppliers(); // 初始載入

  // === 編輯彈窗控制 ===
  window.openEditModal = function (name, phone) {
    document.getElementById('editSupplierName').value = name;
    document.getElementById('editSupplierPhone').value = phone;
    editModal?.classList.add('is-active');
  };

  document.getElementById('closeEditModalBtn')?.addEventListener('click', () => {
    editModal.classList.remove('is-active');
  });

  document.getElementById('cancelEditModalBtn')?.addEventListener('click', () => {
    editModal.classList.remove('is-active');
  });

  document.getElementById('saveEditBtn')?.addEventListener('click', () => {
    const name = document.getElementById('editSupplierName').value;
    const phone = document.getElementById('editSupplierPhone').value;

    axios.post('updateidn.php', { name, phone })
      .then(res => {
        const data = res.data;
        if (data.success) {
          alert(data.message);
        } else {
          alert('注意：' + data.message);
        }
        editModal.classList.remove('is-active');
        fetchSuppliers();
      })
      .catch(err => {
        alert('更新失敗');
        console.error(err);
      });
  });

  // === 刪除供應商功能 ===
  window.deleteSupplier = function (name) {
    if (!confirm('確定要刪除這筆資料嗎？')) return;

    axios.post('deleteidn.php', { name })
      .then(res => {
        alert(res.data.message || '刪除成功');
        fetchSuppliers();
      })
      .catch(err => {
        alert('刪除失敗');
        console.error(err);
      });
  };
});
