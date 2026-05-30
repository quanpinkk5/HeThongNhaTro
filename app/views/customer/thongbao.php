<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/customer/thongbao.css">
</head>
<body>
<header class="clean-header">
    <div class="container-header">
        <a href="index.php" class="logo-minimal">
            <i class="fas fa-house-chimney-window"></i> Hostel<span>Pro</span>
        </a>
        <div class="page-indicator">
            <i class="fas fa-bell"></i> THÔNG BÁO
        </div>
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</header>
    <main class="noti-container">
        <header class="noti-header">
            <h2>Thông báo của bạn</h2>
            <button id="btnMarkAll" class="btn-mark-all">Đánh dấu tất cả là đã đọc</button>
        </header>

        <div id="notificationList" class="noti-list">
            <div class="loading" style="padding: 20px; text-align: center; color: #64748b;">Đang tải thông báo...</div>
        </div>
    </main>

    <script src="../../../public/js/customer/notification.js"></script>
</body>
</html>