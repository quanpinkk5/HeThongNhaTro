document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('id');
    const form = document.getElementById('extensionForm');
    const alertBox = document.getElementById('alert_container');


    const API_URL = '../../../public/js/api/customer/extension_handler.php';

    fetch(`${API_URL}?id=${contractId}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                document.getElementById('room_name_display').innerText = res.data.room_name;
                document.getElementById('building_name').innerText = res.data.building_name;
                document.getElementById('end_date').innerText = new Date(res.data.end_date).toLocaleDateString('vi-VN');
            } else {
                alert("Hợp đồng không hợp lệ!");
                window.location.href = 'phongdangthue.php';
            }
        })
        .catch(err => console.error("Lỗi load data:", err));

    form.onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('.btn-submit');
        submitBtn.disabled = true;
        submitBtn.innerText = 'ĐANG GỬI...';

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id: contractId,
                    months: document.getElementById('months').value,
                    note: document.getElementById('note').value
                })
            });

            const result = await response.json();
            if (result.status === 'success') {
                alertBox.innerHTML = `<div class="alert success" style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> ${result.message}
                </div>`;
                form.reset();
            } else {
                alertBox.innerHTML = `<div class="alert error" style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;">
                    <i class="fas fa-exclamation-circle"></i> ${result.message}
                </div>`;
            }
        } catch (err) {
            console.error("Lỗi gửi yêu cầu:", err);
            alertBox.innerHTML = `<div class="alert error">Không thể kết nối tới máy chủ.</div>`;
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'GỬI YÊU CẦU GIA HẠN';
        }
    };
});