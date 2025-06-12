//頁面跳轉
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

//新增進貨彈窗
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addPurchaseModal');
    const openBtn = document.getElementById('openAddPurchaseBtn');
    const closeBtn = document.getElementById('closePurchaseModalBtn');
    const cancelBtn = document.getElementById('cancelPurchaseModalBtn');

    if (openBtn && modal) {
        openBtn.addEventListener('click', () => {
            modal.classList.add('is-active');
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('is-active');
        });
    }

    if (cancelBtn && modal) {
        cancelBtn.addEventListener('click', () => {
            modal.classList.remove('is-active');
        });
    }
});
