document.addEventListener("DOMContentLoaded", () => {
    loadFavorites();
});

function loadFavorites() {
    const contentDiv = document.getElementById('favorite-content');
    if (!contentDiv) return;

    fetch('/public/js/api/customer/get_favorites.php')
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0) {
                contentDiv.innerHTML = `<div style="text-align:center; padding:50px;"><h2>Danh sách trống</h2><a href="index.php">Tìm phòng ngay</a></div>`;
                return;
            }

            let html = `<div class="hero-header-centered" style="text-align:center;"><h1>❤️ Yêu thích</h1></div><div class="room-grid">`;

            data.forEach(room => {
                let imgPath = '/public/images/default.jpg';
                
                if (room.main_image && room.main_image !== null) {
                    imgPath = `/public/images/${room.main_image}`;
                }
            
                html += `
                <div class="room-card" id="room-${room.id}">
                    <div class="room-banner">
                        <img src="${imgPath}" alt="Room" 
                             onerror="this.src='/public/images/default.jpg'; this.onerror=null;">
                        <button class="remove-fav-btn" onclick="removeFav(${room.id})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="room-details">
                        <h3>${room.title}</h3>
                        <p style="font-size: 0.9em; color: #666;">${room.building_name}</p>
                        <div class="price-tag">${Number(room.price).toLocaleString('vi-VN')}đ<span>/tháng</span></div>
                        <a href="phongtro.php?id=${room.id}" class="btn-detail">XEM CHI TIẾT</a>
                    </div>
                </div>`;
            });
            html += `</div>`;
            contentDiv.innerHTML = html;
        })
        .catch(err => console.error("Có lỗi xảy ra!", err));
}

function removeFav(roomId) {
    const formData = new URLSearchParams();
    formData.append('room_id', roomId);

    fetch('/public/js/api/customer/api_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'removed' || data.status === 'success') {
            const card = document.getElementById('room-' + roomId);
            if (card) {
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 300);
            }
        }
    });
}