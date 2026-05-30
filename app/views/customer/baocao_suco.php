<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo sự cố - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/customer/baosuco.css">
</head>
<body>
<header class="clean-header">
    <div class="container">
        <a href="index.php" class="logo"><i class="fas fa-house-chimney-window"></i> Hostel<span>Pro</span></a>
        <a href="phongdangthue.php" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</header>

<main class="report-container">
    <div class="report-card">
        <div class="report-header">
            <i class="fas fa-tools"></i>
            <h1>Báo cáo sự cố & Bảo trì</h1>
        </div>

        <div id="alert_container"></div>

        <form id="maintenanceForm" enctype="multipart/form-data">
            <input type="hidden" name="room_id" id="room_id_input">
            
            <div class="form-group">
                <label>Mức độ khẩn cấp</label>
                <div class="level-selector">
                    <label class="level-item"><input type="radio" name="level" value="LOW" checked><span class="level-btn">Thường</span></label>
                    <label class="level-item"><input type="radio" name="level" value="MEDIUM"><span class="level-btn">Cần thiết</span></label>
                    <label class="level-item"><input type="radio" name="level" value="HIGH"><span class="level-btn">Khẩn cấp</span></label>
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả chi tiết sự cố</label>
                <textarea name="content" rows="5" placeholder="..." required></textarea>
            </div>

            <div class="form-group">
                <label>Hình ảnh minh chứng</label>
                <div class="upload-box">
                    <input type="file" name="images[]" id="file-input" multiple accept="image/*">
                    <label for="file-input"><i class="fas fa-cloud-upload-alt"></i><span>Nhấn tải ảnh lên</span></label>
                </div>
                <div id="preview-area" class="preview-container"></div>
            </div>
            <button type="submit" class="btn-submit">GỬI YÊU CẦU HỖ TRỢ</button>
        </form>
    </div>
</main>
<script src="../../../public/js/customer/maintenance.js"></script>
</body>
</html>