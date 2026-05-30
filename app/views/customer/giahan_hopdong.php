<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gia hạn hợp đồng - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/customer/giahan_hopdong.css">
</head>
<body>

<header class="clean-header">
    <div class="container">
        <a href="index.php" class="logo"><i class="fas fa-house-chimney-window"></i> Hostel<span>Pro</span></a>
        <a href="phongdangthue.php" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</header>

<main class="extend-container">
    <div class="extend-card">
        <div class="extend-header">
            <i class="fas fa-file-signature"></i>
            <h1>Gia hạn hợp đồng</h1>
            <p>Bạn đang yêu cầu gia hạn cho <strong id="room_name_display">Đang tải...</strong></p>
        </div>

        <div id="alert_container"></div>

        <div class="contract-brief">
            <div class="info-item">
                <span>Tòa nhà:</span>
                <strong id="building_name">...</strong>
            </div>
            <div class="info-item">
                <span>Ngày kết thúc hiện tại:</span>
                <strong id="end_date">...</strong>
            </div>
        </div>

        <form id="extensionForm">
            <div class="form-group">
                <label><i class="fas fa-calendar-plus"></i> Thời gian gia hạn thêm</label>
                <select name="months" id="months" required>
                    <option value="">-- Chọn thời gian --</option>
                    <option value="6">6 Tháng</option>
                    <option value="12">12 Tháng (1 Năm)</option>
                    <option value="24">24 Tháng (2 Năm)</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-comment-dots"></i> Ghi chú cho chủ trọ</label>
                <textarea name="note" id="note" rows="4" placeholder="Ví dụ: Tôi muốn gia hạn thêm để yên tâm công tác..."></textarea>
            </div>

            <div class="warning-box">
                <i class="fas fa-info-circle"></i>
                Lưu ý: Yêu cầu gia hạn cần được chủ trọ phê duyệt để chính thức có hiệu lực.
            </div>

            <button type="submit" class="btn-submit">GỬI YÊU CẦU GIA HẠN</button>
        </form>
    </div>
</main>

<script src="../../../public/js/customer/extension.js"></script>
</body>
</html>