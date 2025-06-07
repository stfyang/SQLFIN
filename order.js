<script>
document.getElementById('orderForm').addEventListener('submit', function(e) {
  e.preventDefault();

  // 從表單收集所有餐點的數量
  const form = e.target;
  const formData = new FormData(form);
  const dishes = {};

  formData.forEach((value, key) => {
    const qty = parseInt(value);
    if (qty > 0) {
      dishes[key] = qty;
    }
  });

  // 若未選擇任何餐點
  if (Object.keys(dishes).length === 0) {
    document.getElementById('message').innerHTML = '<p style="color:red;">請選擇至少一項餐點。</p>';
    return;
  }

  // 發送到 PHP
  fetch('新增訂單.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ dishes })
  })
  .then(response => response.json())
  .then(data => {
    const msgBox = document.getElementById('message');
    if (data.error) {
      msgBox.innerHTML = `<p style="color:red;">${data.error}</p>`;
      if (data.details && Array.isArray(data.details)) {
        const list = data.details.map(item => `<li>${item}</li>`).join('');
        msgBox.innerHTML += `<ul style="color:red;">${list}</ul>`;
      }
    } else {
      msgBox.innerHTML = `<p style="color:green;">${data.message}</p>`;
      form.reset(); // 清空表單
    }
  })
  .catch(err => {
    document.getElementById('message').innerHTML = '<p style="color:red;">系統錯誤，請稍後再試。</p>';
    console.error(err);
  });
});
</script>
