<?php
$title = "HostelPro - Tìm Kiếm Phòng Trọ";
$current_page = 'request';

$extra_css = '<link rel="stylesheet" href="/public/css/customer/yeucauthue.css">';
include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/customer/header.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<main class="main-content">
    <div class="container" id="rent-request-container">
        <div class="loading-state" style="text-align:center; padding: 50px;">
            <i class="fas fa-spinner fa-spin"></i> Đang tải yêu cầu...
        </div>
    </div>
</main>

<script src="/public/js/customer/rent_request.js"></script>
</body>

</html>