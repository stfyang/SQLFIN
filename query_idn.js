//供應商相關
function showMessage(message, type = 'info') {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.padding = '1rem 1.5rem';
        toast.style.borderRadius = '5px';
        toast.style.fontWeight = 'bold';
        toast.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)';
        toast.style.display = 'none';
        document.body.appendChild(toast);
    }

    toast.textContent = message;

    if (type === 'success') {
        toast.style.backgroundColor = '#d4edda'; // 綠
        toast.style.color = '#155724';
    } else if (type === 'error') {
        toast.style.backgroundColor = '#f8d7da'; // 紅
        toast.style.color = '#721c24';
    } else {
        toast.style.backgroundColor = '#fefefe';
        toast.style.color = '#333';
    }

    toast.style.display = 'block';
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    //查詢按鈕
    const queryBtn = document.getElementById('querySupplierBtn');
    if (queryBtn) {
        queryBtn.addEventListener('click', () => {
            window.location.href = 'queryidn.html';
        });
    }

    //新增進貨彈窗控制
    const addPurchaseModal = document.getElementById('addPurchaseModal');
    const openBtn = document.getElementById('openAddPurchaseBtn');
    const closeBtn = document.getElementById('closePurchaseModalBtn');
    const cancelBtn = document.getElementById('cancelPurchaseModalBtn');
    const saveBtn = document.getElementById('savePurchaseBtn');

    if (openBtn && addPurchaseModal) {
        openBtn.addEventListener('click', () => {
            addPurchaseModal.classList.add('is-active');
        });
    }

    if (closeBtn && addPurchaseModal) {
        closeBtn.addEventListener('click', () => {
            addPurchaseModal.classList.remove('is-active');
        });
    }

    if (cancelBtn && addPurchaseModal) {
        cancelBtn.addEventListener('click', () => {
            addPurchaseModal.classList.remove('is-active');
        });
    }

    if (saveBtn && addPurchaseModal) {
        saveBtn.addEventListener('click', () => {
            const supplier = document.getElementById('supplierName')?.value.trim();
            const material = document.getElementById('materialName')?.value.trim();
            const quantity = document.getElementById('quantity')?.value.trim();
            if (!supplier || !material || !quantity) {
                showMessage('請填寫所有欄位', 'error');
                return;
            }

            alert(`送出資料：\n供應商：${supplier}\n原料：${material}\n數量：${quantity}`);
            addPurchaseModal.classList.remove('is-active');
        });
    }

    //查詢與修改供應商
    const tableBody = document.getElementById('supplierTableBody');
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const editModal = document.getElementById('editSupplierModal');

    function fetchSuppliers(name = '') {
        if (!tableBody) return;

        axios.post('query-idn.php', { name })
            .then(res => {
                tableBody.innerHTML = '';
                const data = res.data;

                if (!Array.isArray(data)) {
                    showMessage('資料格式錯誤', 'error');
                    console.error(data);
                    return;
                }

                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="3">查無資料</td></tr>';
                    showMessage('查無資料', 'info');
                }else {
                    data.forEach(supplier => {
                        const safeName = supplier.供應商名稱.replace(/'/g, "\\'");
                        const safePhone = supplier.電話.replace(/'/g, "\\'");
                        const row = `
              <tr>
                <td>${supplier.供應商名稱}</td>
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
                console.error(err);
            });
    }

    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', () => {
            const keyword = searchInput.value.trim();
            fetchSuppliers(keyword);
        });
    }

    fetchSuppliers(); // 頁面載入時自動查詢

    //編輯供應商
    window.openEditModal = function (name, phone) {
        const nameInput = document.getElementById('editSupplierName');
        const phoneInput = document.getElementById('editSupplierPhone');
        if (nameInput) nameInput.value = name;
        if (phoneInput) phoneInput.value = phone;
        if (editModal) editModal.classList.add('is-active');
    };

    const closeEdit = document.getElementById('closeEditModalBtn');
    const cancelEdit = document.getElementById('cancelEditModalBtn');
    const saveEdit = document.getElementById('saveEditBtn');

    if (closeEdit && editModal) {
        closeEdit.addEventListener('click', () => {
            editModal.classList.remove('is-active');
        });
    }

    if (cancelEdit && editModal) {
        cancelEdit.addEventListener('click', () => {
            editModal.classList.remove('is-active');
        });
    }

    if (saveEdit && editModal) {
        saveEdit.addEventListener('click', () => {
            const name = document.getElementById('editSupplierName')?.value;
            const phone = document.getElementById('editSupplierPhone')?.value;

            axios.post('updateidn.php', { name, phone })
                .then(res => {
                    const data = res.data;
                    if (data.success) {
                        showMessage(data.message, 'success');
                    } else {
                        showMessage('注意：' + data.message, 'error');
                    }
                    editModal.classList.remove('is-active');
                    fetchSuppliers();
                })
                .catch(err => {
                    showMessage('更新失敗', 'error');
                    console.error(err);
                });

        });
    }

    //刪除供應商
    window.deleteSupplier = function (name) {
        if (!confirm('確定要刪除這筆資料嗎？')) return;

        axios.post('deleteidn.php', { name })
            .then(res => {
                showMessage(res.data.message || '刪除成功', 'success');
                fetchSuppliers();
            })
            .catch(err => {
                showMessage('刪除失敗', 'error');
                console.error(err);
            });
    };
    
});
