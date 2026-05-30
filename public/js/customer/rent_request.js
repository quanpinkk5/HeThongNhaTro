document.addEventListener("DOMContentLoaded", () => {
    loadRentRequests();
});

function loadRentRequests() {
    const container = document.getElementById('rent-request-container');
    if (!container) return;

    const apiPath = '/public/js/api/customer/get_rent_requests.php';

    fetch(apiPath)
    .then(res => {
        if (!res.ok) throw new Error('Kết nối API thất bại');
        return res.json();
    })
    .then(data => {
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state" style="text-align:center; padding:100px 20px;">
                    <i class="fas fa-paper-plane" style="font-size:50px; color:#ccc;"></i>
                    <h2 style="margin-top:20px;">Bạn chưa gửi yêu cầu nào</h2>
                    <a href="index.php" style="color:#2563eb; text-decoration:none;">Tìm phòng ngay</a>
                </div>`;
            return;
        }

        let html = `
            <div class="hero-section" style="text-align:center; margin-bottom:30px;">
                <h1>📩 Yêu cầu thuê phòng</h1>
            </div>
            <div class="request-list">`;

        data.forEach(req => {
            const defaultImg = '/public/images/default.jpg';
            let imgPath = defaultImg;
            
            if (req.room_img && req.room_img !== null && req.room_img !== "") {
                imgPath = `/public/images/${req.room_img}`;
            }

            const status = req.status ? req.status.toUpperCase() : 'PENDING';
            const label = (status === 'PENDING') ? 'Chờ duyệt' : (status === 'APPROVED') ? 'Đã duyệt' : 'Từ chối';
            const badgeClass = status.toLowerCase();

            html += `
                <div class="req-card" style="margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:12px;">
                    <div class="req-top" style="display:flex; justify-content:space-between; margin-bottom:15px;">
                        <span class="req-tag">Mã đơn: #${req.id}</span>
                        <span class="badge badge-${badgeClass}">${label}</span>
                    </div>
                    <div class="req-middle" style="display:flex; gap:15px;">
                        <img src="${imgPath}" 
                             style="width:120px; height:90px; object-fit:cover; border-radius:8px;"
                             onerror="this.onerror=null; this.src='${defaultImg}';">
                        
                        <div class="req-detail">
                            <h3 style="margin:0 0 10px 0;">${req.title}</h3>
                            <p class="price" style="font-weight:bold; color:#2563eb; margin:0;">
                                ${Number(req.price).toLocaleString('vi-VN')}đ<span>/tháng</span>
                            </p>
                            <div class="req-meta" style="font-size:12px; color:#666; margin-top:10px;">
                                <span><i class="far fa-calendar-check"></i> ${req.created_at}</span>
                            </div>
                        </div>
                    </div>
                    <div class="req-bottom" style="margin-top:15px; text-align:right;">
                        <a href="phongtro.php?id=${req.room_id}" class="btn-sub" 
                           style="text-decoration:none; font-size:14px; color:#2563eb;">Xem chi tiết phòng</a>
                    </div>
                </div>`;
        });

        html += `</div>`;
        container.innerHTML = html;
    })
    .catch(err => {
        console.error("Lỗi RentRequest:", err);
        container.innerHTML = `<div style="text-align:center; padding:50px; color:red;">
            ⚠️ Không thể tải dữ liệu. Vui lòng kiểm tra cấu hình API.
        </div>`;
    });
}