document.addEventListener("DOMContentLoaded", () => {
    loadInvoices();
});

function loadInvoices() {
    const container = document.getElementById('invoice-list-container');
    if (!container) return;

    fetch('/public/js/api/customer/get_invoices.php')
    .then(res => res.json())
    .then(data => {
        if (!data || data.length === 0 || data.error) {
            container.innerHTML = `
                <div class="empty-state-container">
                    <div class="empty-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h2>Chưa có hóa đơn nào</h2>
                    <p>Các hóa đơn tiền phòng, điện, nước hàng tháng sẽ xuất hiện tại đây sau khi chủ nhà khởi tạo.</p>
                    <a href="phongdangthue.php" class="btn-explore-now">
                        KIỂM TRA PHÒNG ĐANG THUÊ <i class="fas fa-arrow-right"></i>
                    </a>
                </div>`;
            return;
        }

        let html = '';
        data.forEach(inv => {
            const status = inv.status.toUpperCase();
            const badgeClass = (status === 'PAID') ? 'badge-paid' : 'badge-unpaid';
            const statusText = (status === 'PAID') ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN';
            
            const total = Number(inv.total).toLocaleString('vi-VN');
            const roomPrice = Number(inv.room_price || 0).toLocaleString('vi-VN');
            const electric = Number(inv.electric || 0).toLocaleString('vi-VN');
            const water = Number(inv.water || 0).toLocaleString('vi-VN');
            const createdAt = new Date(inv.created_at).toLocaleDateString('vi-VN');

            html += `
                <div class="inv-card">
                    <div class="inv-top">
                        <span class="inv-tag">MÃ HĐ: #INV-${inv.id}</span>
                        <span class="badge ${badgeClass}">${statusText}</span>
                    </div>

                    <div class="inv-body">
                        <div class="inv-main-info">
                            <div class="inv-icon"><i class="fas fa-file-invoice"></i></div>
                            <div class="inv-title">
                                <h3>Hóa đơn Tháng ${inv.month}/${inv.year}</h3>
                                <div class="total-price">${total} <span>VNĐ</span></div>
                            </div>
                        </div>

                        <div class="inv-details-grid">
                            <div class="detail-item">
                                <label>Tiền phòng</label>
                                <span>${roomPrice}đ</span>
                            </div>
                            <div class="detail-item">
                                <label>Điện</label>
                                <span>${electric}đ</span>
                            </div>
                            <div class="detail-item">
                                <label>Nước</label>
                                <span>${water}đ</span>
                            </div>
                            <div class="detail-item">
                                <label>Ngày tạo</label>
                                <span class="date">${createdAt}</span>
                            </div>
                        </div>

                        <div class="inv-action">
                            <a href="chitiet_hoadon.php?id=${inv.id}" class="btn-detail" style="text-decoration: none; display: inline-block;">Chi tiết</a>
                        </div>
                    </div>
                </div>`;
        });
        container.innerHTML = html;
    })
    .catch(err => {
        console.error("Lỗi tải hóa đơn:", err);
        container.innerHTML = `<div style="text-align:center; color:red; padding:50px;">Lỗi hệ thống khi tải hóa đơn.</div>`;
    });
}