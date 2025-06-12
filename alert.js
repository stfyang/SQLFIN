<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
//安全庫存
  function closeStockWarningModal() {
    document.getElementById('stockWarningModal').classList.remove('is-active');
  }

  document.addEventListener('DOMContentLoaded', () => {
    // 自動查詢庫存
    axios.get('query-stock.php')
      .then(res => {
        const data = res.data;
        let warnings = [];

        data.forEach(item => {
          if (item.quantity < item.safe_quantity) {
            warnings.push(`🔴 ${item.name} 剩下 ${item.quantity}（安全庫存 ${item.safe_quantity}）`);
          }
        });

        if (warnings.length > 0) {
          const content = document.getElementById('stockWarningContent');
          content.innerHTML = warnings.map(w => `<p>${w}</p>`).join('');
          document.getElementById('stockWarningModal').classList.add('is-active');
        }
      })
      .catch(err => {
        console.error("庫存查詢失敗：", err);
      });
  });
