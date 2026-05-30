<?php
$title = "HostelPro - Tìm Kiếm Phòng Trọ";
$current_page = 'index';

$extra_css = '<link rel="stylesheet" href="/public/css/customer/index.css">';
include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/customer/header.php';

?>
<main class="container">
    <section class="hero-section">
        <div class="hero-content">
            <h1 style="font-size: 36px; font-weight: 800; letter-spacing: -1px; margin-bottom: 15px;">
                🏠 Tìm kiếm không gian sống của bạn
            </h1>
            <p style="color: #64748b; font-size: 18px;">Hàng ngàn phòng trọ tiện nghi đang chờ đón bạn khám phá</p>
        </div>

        <form class="search-premium">
            <div class="search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Tên tòa nhà, đường hoặc loại phòng...">
            </div>
            <div class="search-select">
                <i class="fa-solid fa-location-crosshairs"></i>
                <select name="district">
                    <option value="">Tất cả khu vực</option>
                </select>
            </div>
            <button type="submit" class="btn-search-go">
                <i class="fa-solid fa-paper-plane" style="margin-right: 8px; color: white; font-size: 14px;"></i>TÌM KIẾM
            </button>
        </form>
    </section>

    <div class="room-grid" id="room-list"></div>
</main>

<script src="../../../public/js/customer/index.js"></script>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/customer/footer.php'; ?>
</body>

</html>