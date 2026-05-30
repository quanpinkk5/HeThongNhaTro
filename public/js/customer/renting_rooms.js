document.addEventListener("DOMContentLoaded", () => {
    loadRentingRooms();
});

function loadRentingRooms() {
    const container = document.getElementById('renting-rooms-content');
    if (!container) return;

    fetch('/public/js/api/customer/get_renting_rooms.php')
    .then(res => res.json())
    .then(data => {
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state-container">
                    <div class="empty-icon" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h2>Chưa có phòng đang thuê</h2>
                    <p>Hợp đồng của bạn sẽ xuất hiện tại đây sau khi chủ nhà duyệt yêu cầu.</p>
                    <a href="index.php" class="btn-explore-now">KHÁM PHÁ NGAY <i class="fas fa-arrow-right"></i></a>
                </div>`;
            return;
        }

        let html = `<div class="request-list">`;
        data.forEach(room => {
            const defaultImg = '/public/images/default.jpg';
            const roomImg = room.room_img ? `/public/images/${room.room_img}` : defaultImg;
            
            html += `
                <div class="req-card">
                    <div class="req-top">
                        <span class="req-tag">Mã hợp đồng: #HD-${room.id}</span>
                        <span class="badge badge-approved"><i class="fas fa-check-circle"></i> Đang cư trú</span>
                    </div>
                    <div class="req-middle">
                        <div class="img-wrapper">
                            <img src="${roomImg}" onerror="this.onerror=null; this.src='${defaultImg}';" alt="Room">
                        </div>
                        <div class="req-detail">
                            <h3>${room.title}</h3>
                            <p class="price">${Number(room.price).toLocaleString('vi-VN')}đ<span>/tháng</span></p>
                            <div class="info-grid">
                                <div class="info-item"><i class="fas fa-map-marker-alt"></i> <span><strong>Địa chỉ:</strong> ${room.address}</span></div>
                                <div class="info-item"><i class="far fa-calendar-check"></i> <span><strong>Bắt đầu:</strong> ${new Date(room.start_date).toLocaleDateString('vi-VN')}</span></div>
                                <div class="info-item"><i class="far fa-calendar-times"></i> <span><strong>Kết thúc:</strong> ${room.end_date ? new Date(room.end_date).toLocaleDateString('vi-VN') : 'Chưa xác định'}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="req-bottom">
                        <div class="btn-group">
                            <a href="chitiet_hopdong.php?id=${room.id}" class="btn-action btn-contract"><i class="fas fa-file-contract"></i> Xem hợp đồng</a>
                            <a href="giahan_hopdong.php?id=${room.id}" class="btn-action btn-extend"><i class="fas fa-sync-alt"></i> Gia hạn</a>
                            <a href="baocao_suco.php?id=${room.room_id}" class="btn-action btn-report"><i class="fas fa-exclamation-triangle"></i> Báo sự cố</a>
                        </div>
                    </div>
                </div>`;
        });
        html += `</div>`;
        container.innerHTML = html;
    })
    .catch(err => {
        console.error("Lỗi fetch:", err);
        container.innerHTML = `<div style="text-align:center; color:red; padding:20px;">Không thể tải dữ liệu phòng đang thuê.</div>`;
    });
}