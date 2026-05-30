<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết phòng trọ - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/customer/phongtro.css">
    <link rel="stylesheet" href="../../../public/css/customer/footer.css">
</head>
<body>
    <header class="clean-header">
        <div class="container-header">
            <a href="index.php" class="logo-minimal"><i class="fas fa-house-chimney-window"></i> Hostel<span>Pro</span></a>
            <div class="page-indicator"><span class="dot"></span> CHI TIẾT PHÒNG TRỌ</div>
            <a href="index.php" class="btn-back-home"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </header>

    <main class="detail-container">
        <div class="detail-grid" id="main-content" style="display:none;">
            <div class="left-col">
                <div class="gallery-card">
                    <div class="main-image"><img id="view_main" src=""></div>
            
                    <div class="thumbnails" id="thumb_list"></div>
                </div>

                <div class="info-card">
                    <div class="room-header">
                        <h2 class="room-title" id="room_title"></h2>
                        <p class="room-address"><i class="fas fa-map-marker-alt"></i> <span id="room_address"></span></p>
                    </div>
                    <div class="amenities-vertical">
                        <div class="amenity-row"><i class="fas fa-ruler-combined"></i> <span>Diện tích: <b id="room_area"></b> m²</span></div>
                        <div class="amenity-row"><i class="fas fa-bolt"></i> <span>Điện: <b id="price_dien"></b>đ</span></div>
                        <div class="amenity-row"><i class="fas fa-tint"></i> <span>Nước: <b id="price_nuoc"></b>đ</span></div>
                    </div>
                    <div class="amenities-grid" id="other_services_list"></div>
                    <div class="section-divider"></div>
                    <div class="description-section">
                        <h3><i class="fas fa-align-left"></i> Mô tả chi tiết</h3>
                        <div class="text-content" id="room_desc"></div>
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="sticky-sidebar">
                    <div class="booking-card">
                        <div class="price-wrapper">
                            <span class="label">Giá thuê niêm yết</span>
                            <div class="main-price"><span id="room_price"></span><span>/tháng</span></div>
                        </div>
                        <div id="status_badge" class="room-status"></div>
                        <div id="booking_action"></div>
                    </div>
                    <div class="landlord-card">
                        <h3>Thông tin chủ sở hữu</h3>
                        <div class="landlord-profile">
                            <div class="avatar-wrap"><i class="fas fa-user-tie"></i></div>
                            <div class="landlord-meta">
                                <strong id="owner_name"></strong>
                                <span>Chủ nhà đã xác minh</span>
                            </div>
                        </div>
                        <a id="owner_phone_link" href="" class="btn-call-landlord">
                            <i class="fas fa-phone-volume"></i> <span id="owner_phone"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div id="loading" style="text-align:center; padding:100px;">Đang tải dữ liệu...</div>
    </main>


    <script src="/public/js/customer/send_request.js"></script>
    <script src="/public/js/customer/room_detail.js"></script>
</body>
</html>