document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.getElementById('supplierTableBody');
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');

  // 初始化顯示全部資料
  fetchSuppliers();

  // 查詢功能
  searchBtn.addEventListener('click', () => {
    const keyword = searchInput.value.trim();
    fetchSuppliers(keyword);
  });

  // 抓資料並更新表格
  function fetchSuppliers(name = '') {
    axios.post('query-idn.php', { name })
      .then(res => {
        const suppliers = res.data;
        tableBody.innerHTML = '';

        if (suppliers.length === 0) {
          tableBody.innerHTML = '<tr><td colspan="2">查無資料</td></tr>';
          return;
        }

        for (const s of suppliers) {
          const row = `<tr><td>${s.名稱}</td><td>${s.電話}</td></tr>`;
          tableBody.innerHTML += row;
        }
      })
      .catch(err => {
        console.error('載入資料失敗', err);
        tableBody.innerHTML = '<tr><td colspan="2">載入失敗</td></tr>';
      });
  }
});
