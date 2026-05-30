<?php
    $title = "HostelPro - Tìm Kiếm Phòng Trọ"; 
    $current_page = 'favorite'; 
    
    $extra_css = '<link rel="stylesheet" href="/public/css/customer/favorite.css">';
    include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/customer/header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<main class="main-content">
    <div class="container">
        <div id="favorite-content">
            <div style="text-align: center; padding: 50px;">Đang tải danh sách yêu thích...</div>
        </div>
    </div>
</main>

<script src="/public/js/customer/favorite.js"></script>
</body>
</html>