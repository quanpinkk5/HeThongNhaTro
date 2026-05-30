<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'HostelPro - Tìm Kiếm Phòng Trọ'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../../../public/css/customer/footer.css">
    
    <?php if(isset($extra_css)) echo $extra_css; ?>
</head>
<body>
    <header class="main-header">
        <div class="container header-flex">
            <a href="index.php" class="logo"><i class="fas fa-house-chimney-window"></i> Hostel<span>Pro</span></a>
            <nav class="main-nav">
                <ul>
                    <li class="<?= ($current_page == 'index') ? 'active' : '' ?>"><a href="index.php"><i class="fas fa-house"></i> TRANG CHỦ</a></li>
                    <li class="<?= ($current_page == 'favorite') ? 'active' : '' ?>"><a href="favorite.php"><i class="fas fa-heart"></i> PHÒNG YÊU THÍCH</a></li>
                    <li class="<?= ($current_page == 'request') ? 'active' : '' ?>"><a href="yeucauthue.php"><i class="fas fa-paper-plane"></i> YÊU CẦU THUÊ</a></li>
                    <li class="<?= ($current_page == 'renting') ? 'active' : '' ?>"><a href="phongdangthue.php"><i class="fas fa-house-user"></i> PHÒNG ĐANG THUÊ</a></li>
                    <li class="<?= ($current_page == 'invoice') ? 'active' : '' ?>"><a href="hoadon.php"><i class="fas fa-file-invoice-dollar"></i> HÓA ĐƠN</a></li>
                </ul>
            </nav>
            <div class="user-area" id="auth-section"></div>
        </div>
    </header>