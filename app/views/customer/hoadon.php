<?php
$title = "HostelPro - Tìm Kiếm Phòng Trọ"; 
$current_page = 'invoice'; 

$extra_css = '<link rel="stylesheet" href="/public/css/customer/hoadon.css">';
include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/customer/header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<main class="container">
    <section class="hero-section">
        <h1>📑 Hóa đơn thanh toán</h1>
        <p>Theo dõi các khoản phí lưu trú hàng tháng của bạn</p>
    </section>

    <div id="invoice-list-container" class="invoice-list">
        <div style="text-align:center; padding: 50px;">
            <i class="fas fa-spinner fa-spin"></i> Đang tải danh sách hóa đơn...
        </div>
    </div>
</main>

<script src="/public/js/customer/invoice.js"></script>
</body>
</html>