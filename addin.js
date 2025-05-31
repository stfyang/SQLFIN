
document.addEventListener('DOMContentLoaded', () => {
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
    location.reload(); // 或自行更新畫面
  } catch (error) {
    alert('新增失敗');
    console.error(error);
  }
};

});

