document.addEventListener("DOMContentLoaded", () => {
    checkAuth();
    loadAreas();
    loadRooms();

    const searchForm = document.querySelector('.search-premium');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e){
            e.preventDefault();
            let formData = new FormData(this);
            let params = '?' + new URLSearchParams(formData).toString();
            loadRooms(params);
        });
    }
});

function checkAuth() {
    fetch('/public/js/api/customer/auth_handler.php')
        .then(res => res.json())
        .then(data => {
            const authSection = document.getElementById('auth-section');
            if (!data.is_logged_in) {
                authSection.innerHTML = `
                    <div class="auth-group">
                        <a href="login.php" class="btn-login-style">Đăng nhập</a>
                        <a href="register.php" class="btn-reg-style">Đăng ký</a>
                    </div>`;
            } else {
                const firstChar = data.user_name.charAt(0).toUpperCase();
                authSection.innerHTML = `
                    <div class="user-dropdown">
                        <div class="user-pill">
                            <div class="avatar-circle">${firstChar}</div>
                            <div class="user-info-text">
                                <span class="label">Tài khoản</span> <br>
                                <span class="name">${data.user_name}</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="profile.php"><i class="fas fa-user-circle"></i> Trang cá nhân</a>
                            <hr>
                            <a href="thongbao.php">
                                <i class="fa-solid fa-bell"></i> Thông báo
                                <span class="noti-badge">${data.unread_count}</span>
                            </a>
                            <hr>
                            <a href="../../controllers/customer/LogoutController.php" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        </div>
                    </div>`;
            }
        });
}

function loadRooms(params = '') {
    fetch('/public/js/api/customer/get_rooms.php' + params)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('room-list');
            if (data.length === 0) {
                container.innerHTML = `<div class="no-results-card"><h2>Không có phòng phù hợp</h2></div>`;
                return;
            }
            let html = '';
            data.forEach(room => {
                html += `
                <div class="room-card">
                    <div class="room-banner">
                        <img src="../../../public/images/${room.main_image || 'default.jpg'}" alt="Room">
                        <div class="badge-status">CÒN TRỐNG</div>
                        <button class="wishlist-heart-btn ${room.is_fav > 0 ? 'active' : ''}" onclick="toggleFav(this, ${room.id})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="room-details">
                        <h3>${room.title}</h3>
                        <div class="price-tag">${Number(room.price).toLocaleString('vi-VN')}đ</div>
                        <div class="room-info-meta">
                             <span><i class="fas fa-expand-arrows-alt"></i> ${room.area} m²</span>
                             <span><i class="fas fa-location-dot"></i> ${room.building_name}</span>
                        </div>
                        <a href="phongtro.php?id=${room.id}" class="btn-view-detail" style="text-decoration:none; display:block; text-align:center; margin-top:10px; padding:8px; background:#f1f5f9; border-radius:8px; color:#1e293b; font-weight:600;">Xem chi tiết</a>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        });
}

function loadAreas() {
    fetch('/public/js/api/customer/get_areas.php')
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">Tất cả khu vực</option>';
            data.forEach(area => { html += `<option value="${area.name}">${area.name}</option>`; });
            document.querySelector('select[name="district"]').innerHTML = html;
        });
}

function toggleFav(btn, roomId) {
    let formData = new URLSearchParams();
    formData.append('room_id', roomId);
    
    fetch('/public/js/api/customer/api_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.action === 'not_logged_in') {
            alert('Bạn vẫn chưa đăng nhập!');
            window.location.href = 'login.php'; 
            return;
        }

        if (data.action === 'added') {
            btn.classList.add('active');
        } else if (data.action === 'removed') {
            btn.classList.remove('active');
        }
    })
    .catch(err => console.error('Lỗi:', err));
}
