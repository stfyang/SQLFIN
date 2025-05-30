document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('addSupplierModal');
  const openBtn = document.getElementById('openAddSupplierBtn');
  const closeBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelModalBtn');
  const saveBtn = document.getElementById('saveSupplierBtn');

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
        alert(res.data.message || '新增成功');
        modal.classList.remove('is-active');
        document.getElementById('supplierName').value = '';
        document.getElementById('supplierPhone').value = '';
      })
      .catch(err => {
        alert('新增失敗');
        console.error(err);
      });
  });

  // 時間顯示
  function updateTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('time').innerText = `${hours}:${minutes}`;
  }
  setInterval(updateTime, 1000);
  updateTime();
});
