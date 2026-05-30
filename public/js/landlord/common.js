document.addEventListener("DOMContentLoaded", function() {
    
    const notifyBox = document.getElementById('notifyBox');
    const notifyDropdown = document.getElementById('notifyDropdown');
    const notifyItems = document.querySelectorAll('.notify-item');

    // 1. Toggle Dropdown khi click vào chuông
    if (notifyBox && notifyDropdown) {
        notifyBox.addEventListener('click', function(e) {
            // Chỉ toggle nếu click vào icon/badge, không phải click vào list bên trong
            if (e.target.closest('.notify-dropdown')) return;
            
            e.stopPropagation();
            notifyDropdown.classList.toggle('show');
        });

        // Click ra ngoài thì đóng
        document.addEventListener('click', function(e) {
            if (!notifyBox.contains(e.target)) {
                notifyDropdown.classList.remove('show');
            }
        });
    }

    // 2. Xử lý AJAX: Đánh dấu đã đọc khi click vào 1 tin
    notifyItems.forEach(item => {
        item.addEventListener('click', function() {
            const notifyId = this.getAttribute('data-id');
            const isUnread = this.classList.contains('unread');

            if (notifyId && isUnread) {
                // Gửi request ngầm lên server
                fetch('../../public/chutro/mark_read.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + notifyId
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        // Bỏ class unread (đổi màu ngay lập tức)
                        this.classList.remove('unread');
                        // Ẩn dấu chấm xanh nếu có
                        const dot = this.querySelector('.dot');
                        if (dot) dot.style.display = 'none';
                    }
                })
                .catch(err => console.error('Lỗi Ajax:', err));
            }
        });
    });
});