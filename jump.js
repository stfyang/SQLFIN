
function 點餐() {
    window.location.href = "oder.html"; 
  }
  function 菜單() {
    window.location.href = "menu.html"; 
  }
  function 進貨() {
    window.location.href = "in.html"; 
  }
  function 庫存() {
    window.location.href = "sto.html"; 
  }
  function 供應商() {
    window.location.href = "idn.html"; 
  }
  function 營運分析() {
    window.location.href = "an.html"; 
  }
  
  function 首頁() {
    window.location.href = "index.html"; 

  }
  document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('querySupplierBtn');
  btn.addEventListener('click', () => {
    window.location.href = 'queryidn.html';
  });
});
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('addPurchaseModal');
  const openBtn = document.getElementById('openAddPurchaseBtn');
  const closeBtn = document.getElementById('closePurchaseModalBtn');
  const cancelBtn = document.getElementById('cancelPurchaseModalBtn');

  openBtn.addEventListener('click', () => {
    modal.classList.add('is-active');
  });

  closeBtn.addEventListener('click', () => {
    modal.classList.remove('is-active');
  });

  cancelBtn.addEventListener('click', () => {
    modal.classList.remove('is-active');
  });

  // 儲存按鈕處理：你之後可在這裡連接 PHP 處理資料
  document.getElementById('savePurchaseBtn').addEventListener('click', () => {
    const supplier = document.getElementById('supplierName').value.trim();
    const material = document.getElementById('materialName').value.trim();
    const quantity = document.getElementById('quantity').value.trim();

    if (!supplier || !material || !quantity) {
      alert('請填寫所有欄位');
      return;
    }

    // 可用 fetch 或 axios 將資料傳到 PHP 處理
    alert(`送出資料：\n供應商：${supplier}\n原料：${material}\n數量：${quantity}`);
    modal.classList.remove('is-active');
  });
});
