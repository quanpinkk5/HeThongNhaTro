<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết hóa đơn - HostelPro</title>
    <link rel="stylesheet" href="../../../public/css/customer/chitiet_hoadon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="brand">
                <h1>Hostel<span>Pro</span></h1>
                <p>Hệ thống quản lý phòng trọ thông minh</p>
            </div>
            <div class="invoice-meta">
                <h2>HÓA ĐƠN</h2>
                <p>Mã: <strong id="inv_code">...</strong></p>
                <p>Kỳ hóa đơn: <strong id="inv_period">...</strong></p>
                <span id="inv_status" class="status-badge">Đang tải...</span>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3><i class="fa fa-user"></i> Người thuê</h3>
                <div class="info-content">
                    <p><strong>Họ tên:</strong> <span id="tenant_name">...</span></p>
                    <p><strong>SĐT:</strong> <span id="tenant_phone">...</span></p>
                    <p><strong>Email:</strong> <span id="tenant_email">...</span></p>
                </div>
            </div>
            <div class="info-box">
                <h3><i class="fa fa-building"></i> Thông tin phòng</h3>
                <div class="info-content">
                    <p><strong>Tòa nhà:</strong> <span id="building_name">...</span></p>
                    <p><strong>Phòng:</strong> <span id="room_name">...</span></p>
                    <p><i class="fa fa-map-marker-alt"></i> <span id="building_address">...</span></p>
                </div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="50">STT</th>
                    <th>Nội dung thanh toán</th>
                    <th class="text-right">Đơn giá</th>
                    <th class="text-right">Số lượng</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody id="invoice_items_body">
                <tr><td colspan="5" style="text-align:center;">Đang tải dữ liệu...</td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="total-label">TỔNG CỘNG</td>
                    <td class="total-amount" id="total_amount">0đ</td>
                </tr>
            </tfoot>
        </table>

        <div class="invoice-footer">
            <div class="footer-note">
                <p>* Vui lòng thanh toán hóa đơn đúng hạn để tránh phát sinh phí trễ hạn.</p>
                <p>Ngày xuất hóa đơn: <span id="created_at">...</span></p>
            </div>
            <div class="actions no-print">
                <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> In hóa đơn</button>
                <a href="hoadon.php" class="btn-back">Quay lại</a>
            </div>
        </div>
    </div>

    <script src="../../../public/js/customer/invoice_detail.js"></script>
</body>
</html>