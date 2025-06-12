//供應商新增相關
document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.getElementById('saveSupplierBtn');
    const openBtn = document.getElementById('openAddSupplierBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const modal = document.getElementById('addSupplierModal');
    const messageBox = document.getElementById('message-box');

    openBtn.addEventListener('click', () => modal.classList.add('is-active'));
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    addBtn.addEventListener('click', async () => {
    const nameInput = document.getElementById('supplierName');
    const phoneInput = document.getElementById('supplierPhone');
    const name = nameInput.value.trim();
    const phone = phoneInput.value.trim();

    // 防呆->不能為空
    if (!name || !phone) {
        showMessage('❌ 請輸入正確名稱與電話', 'error');
        return;
    }

    // 防呆->名稱不能只有符號或空白
    if (!/^[\u4e00-\u9fa5a-zA-Z0-9\s]+$/.test(name)) {
        showMessage('❌ 名稱不可包含特殊符號', 'error');
        return;
    }

    // 防呆->電話格式檢查
    if (!/^0\d{7,9}$/.test(phone)) {
    showMessage('❌ 電話格式錯誤，請輸入 8~10 碼的市內電話或手機號碼', 'error');
    return;
}


        

    // 防呆->防止重複
    addBtn.disabled = true;
    addBtn.textContent = '儲存中...';

    try {
        const response = await fetch('add-idn.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, phone })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showMessage('✅ ' + result.message, 'success');
            closeModal();
        } else {
            showMessage('❌ ' + (result.message || '新增失敗'), 'error');
        }
    } catch (err) {
        showMessage('❌ 發生錯誤，請稍後再試', 'error');
    } finally {
        addBtn.disabled = false;
        addBtn.textContent = '儲存';
    }
});

//通知設定
    function showMessage(message, type) {
        const toast = document.getElementById('toast');
        toast.textContent = message;

        if (type === 'success') {
            toast.style.backgroundColor = '#d4edda';  
            toast.style.color = '#155724';
        } else if (type === 'error') {
            toast.style.backgroundColor = '#f8d7da'; 
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


    function closeModal() {
        document.getElementById('supplierName').value = '';
        document.getElementById('supplierPhone').value = '';
        modal.classList.remove('is-active');
    }
});

