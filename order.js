/* async function submitOrder() {
  if (Object.keys(cart).length === 0) {
    showMessage('請先加入餐點', true);
    return;
  }

  try {
    const res = await axios.post('add_order.php', { dishes: cart });

    // 先清空訊息區
    let msgBox = document.getElementById('message');
    if (!msgBox) {
      msgBox = document.createElement('div');
      msgBox.id = 'message';
      msgBox.style.marginTop = '1em';
      document.body.prepend(msgBox);
    }
    msgBox.innerHTML = '';

    // 顯示主訊息
    msgBox.innerHTML = `<p style="color:green;">${res.data.message}</p>`;

    // 如果有 details 就列成清單
    if (res.data.details && Array.isArray(res.data.details)) {
      const listItems = res.data.details.map(item => `<li>${item}</li>`).join('');
      msgBox.innerHTML += `<ul style="color:red; margin-left: 1em;">${listItems}</ul>`;
    }

    cart = {};
    renderCart();

  } catch (err) {
    let msg = '新增失敗';
    if (err.response && err.response.data && err.response.data.message) {
      msg = err.response.data.message;
    }

    let msgBox = document.getElementById('message');
    if (!msgBox) {
      msgBox = document.createElement('div');
      msgBox.id = 'message';
      msgBox.style.marginTop = '1em';
      document.body.prepend(msgBox);
    }
    msgBox.innerHTML = `<p style="color:red;">${msg}</p>`;
    console.error(err);
  }
}
 */