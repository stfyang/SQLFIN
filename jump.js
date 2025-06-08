function 點餐() {
    window.location.href = "order.html"; 
  }
  function 菜單() {
    window.location.href = "menu.html"; 
  }
  function 進貨() {
    window.location.href = "in.php"; 
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
/* document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('querySupplierBtn');
  btn.addEventListener('click', () => {
    window.location.href = 'queryidn.html';
  });
});*/
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

  
});
