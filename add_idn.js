document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('addSupplierModal');
  const openBtn = document.getElementById('openAddSupplierBtn');
  const closeBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelModalBtn');
  const saveBtn = document.getElementById('saveSupplierBtn');
  const queryBtn = document.getElementById('querySupplierBtn'); // 🔍 新增查詢按鈕

  openBtn.addEventListener('click', () => modal.classList.add('is-active'));
  closeBtn.addEventListener('click', () => modal.classList.remove('is-active'));
  cancelBtn.addEventListener('click', () => modal.classList.remove('is-active'));

  saveBtn.addEventListener('click', () => {
  const name = document.getElementById('supplierName').value.trim();
  const phone = document.getElementById('supplierPhone').value.trim();

  if (!name || !phone) {
    alert('請填寫所有欄位');
    return;
  }

  axios.post('add-idn.php', { name, phone })
    .then(res => {
      if (res.data.success) {
        alert(res.data.message || '新增成功');
        modal.classList.remove('is-active');
        document.getElementById('supplierName').value = '';
        document.getElementById('supplierPhone').value = '';
      } else {
        alert(res.data.message || '新增失敗');
      }
    })
    .catch(err => {
      // 檢查是否是重複名稱錯誤（MySQL error 1062）
      if (err.response && err.response.data && err.response.data.error) {
        if (err.response.data.error.includes('Duplicate entry')) {
          alert('名稱已存在，請使用其他名稱');
        } else {
          alert('新增失敗: ' + err.response.data.error);
        }
      } else {
        alert('新增失敗，請稍後再試');
      }
      console.error(err);
    });
});

  

  // 🔍 查詢供應商功能
  queryBtn.addEventListener('click', () => {
    const name = document.getElementById('supplierName').value.trim();

    if (!name) {
      alert('請輸入要查詢的供應商名稱');
      return;
    }

    axios.post('query-idn.php', { name })
      .then(res => {
        if (res.data.length > 0) {
          const supplier = res.data[0]; // 假設只取第一筆
          alert(`供應商：${supplier.名稱}\n電話：${supplier.電話}`);
        } else {
          alert('查無資料');
        }
      })
      .catch(err => {
        alert('查詢失敗');
        console.error(err);
      });
  });

  function updateTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('time').innerText = `${hours}:${minutes}`;
  }

  setInterval(updateTime, 1000);
  updateTime();
});
