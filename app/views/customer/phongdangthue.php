<?php
$title = "HostelPro - Tìm Kiếm Phòng Trọ"; 
$current_page = 'renting'; 

$extra_css = '<link rel="stylesheet" href="/public/css/customer/phongdangthue.css">';
include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/customer/header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
    <main class="main-content">
        <div class="container">
            <div class="hero-section">
                <h1>🏠 Phòng của tôi</h1>
                <p>Thông tin chi tiết về các hợp đồng thuê phòng hiện tại của bạn</p>
            </div>
            <div id="renting-rooms-content" class="content-wrapper">
                <div style="text-align:center; padding: 50px;"><i class="fas fa-spinner fa-spin"></i> Đang tải danh sách phòng...</div>
            </div>
        </div>
    </main>
    <script src="/public/js/customer/renting_rooms.js"></script>
</body>
</html>