document.addEventListener('DOMContentLoaded', () => {
  // ✅ 正確的 async IIFE（立即執行函式）
  (async () => {
    const openBtn = document.getElementById('openAddPurchaseBtn');
    const modal = document.getElementById('addPurchaseModal');
    const closeBtn = document.getElementById('closePurchaseModalBtn');
    const cancelBtn = document.getElementById('cancelPurchaseModalBtn');
    const saveBtn = document.getElementById('savePurchaseBtn');

    openBtn.onclick = () => modal.classList.add('is-active');
    closeBtn.onclick = cancelBtn.onclick = () => modal.classList.remove('is-active');

    saveBtn.onclick = async () => {
      const supplierName = document.getElementById('supplierName').value.trim();
      const materialName = document.getElementById('materialName').value.trim();
      const quantity = document.getElementById('quantity').value.trim();

      if (!supplierName || !materialName || !quantity) {
        alert('所有欄位皆為必填');
        return;
      }

      try {
        const response = await axios.post('addin.php', {
          name: supplierName,
          material: materialName,
          quantity: parseInt(quantity, 10)
        });
        alert(response.data.message);
        modal.classList.remove('is-active');
        location.reload();
      } catch (error) {
        alert('新增失敗');
        console.error(error);
      }
    };

    // ✅ 載入供應商選單
    try {
      const response = await axios.get('list_suppliers.php');
      const suppliers = response.data.suppliers;

      const supplierSelect = document.getElementById('supplierName');
      suppliers.forEach(name => {
        const option = document.createElement('option');
        option.value = name;
        option.textContent = name;
        supplierSelect.appendChild(option);
      });
    } catch (error) {
      console.error('載入供應商失敗', error);
      alert('無法載入供應商選項');
    }
  })(); // ✅ 正確的立即執行 async 函式結尾
});
