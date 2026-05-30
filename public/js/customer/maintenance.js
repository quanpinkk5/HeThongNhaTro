document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get('id');
    const form = document.getElementById('maintenanceForm');
    const alertBox = document.getElementById('alert_container');

    const API_URL = '../../../public/js/api/customer/maintenance_handler.php';

    if (roomId) {
        fetch(`${API_URL}?id=${roomId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    document.getElementById('room_title_display').innerText = "Phòng: " + res.data.title;
                } else {
                    alertBox.innerHTML = `<div class="alert error">Lỗi: ${res.message}</div>`;
                }
            })
            .catch(err => console.error("Lỗi load phòng:", err));
    }

    document.getElementById('file-input').addEventListener('change', function(e) {
        const previewArea = document.getElementById('preview-area');
        previewArea.innerHTML = '';
        [...e.target.files].forEach(file => {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `<img src="${ev.target.result}" style="width:80px;height:80px;object-fit:cover;border-radius:5px;margin:5px;">`;
                previewArea.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    form.onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('.btn-submit');
        submitBtn.disabled = true;
        submitBtn.innerText = 'ĐANG GỬI...';

        const formData = new FormData(form);
        formData.append('room_id', roomId);
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.status === 'success') {
                alertBox.innerHTML = `<div class="alert success" style="background:#d4edda;color:#155724;padding:15px;margin-bottom:20px;border-radius:5px;">
                    <i class="fas fa-check-circle"></i> ${result.message}
                </div>`;
                form.reset();
                document.getElementById('preview-area').innerHTML = '';
            } else {
                alertBox.innerHTML = `<div class="alert error" style="background:#f8d7da;color:#721c24;padding:15px;margin-bottom:20px;border-radius:5px;">
                    <i class="fas fa-exclamation-circle"></i> ${result.message}
                </div>`;
            }
        } catch (error) {
            alertBox.innerHTML = `<div class="alert error">Lỗi kết nối máy chủ!</div>`;
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'GỬI YÊU CẦU HỖ TRỢ';
        }
    };
});