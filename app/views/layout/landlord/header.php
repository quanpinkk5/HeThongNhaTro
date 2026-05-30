<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Thêm dấu / vào __DIR__ cho chuẩn xác và gọi lệnh require_once để nhúng file
$dbPath = __DIR__ . '/../../../../config/database.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
}

/* ====== TÊN TRANG ====== */
$pageNames = [
    'index'       => 'Dashboard',
    'building'    => 'Tòa nhà',
    'room'        => 'Phòng trọ',
    'tenant'      => 'Khách thuê',
    'rentrequest' => 'Yêu cầu thuê phòng',
    'contract'    => 'Hợp đồng',
    'invoice'     => 'Hóa đơn',
    'service'     => 'Dịch vụ',
    'maintenance' => 'Bảo trì'
];
$page = $page ?? 'index';
$currentPage = $pageNames[$page] ?? 'Dashboard';

/* ====== USER INFO ====== */
$userId   = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['user_name'] ?? 'Admin';
$userRole = $_SESSION['user_role'] ?? 'Quản trị viên';

/* ====== LOGIC THÔNG BÁO (mysqli thuần) ====== */
$notifyCount = 0;
$notifications = [];

// Sử dụng biến $con thay vì $conn
if ($userId > 0 && isset($con)) {

    // 1. Đếm thông báo chưa đọc
    $sqlCount = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $userId AND is_read = 0";
    $resCount = mysqli_query($con, $sqlCount);
    if ($resCount) {
        $rowCount = mysqli_fetch_assoc($resCount);
        $notifyCount = $rowCount['total'];
    }

    // 2. Lấy 5 thông báo mới nhất
    $sqlList = "SELECT * FROM notifications WHERE user_id = $userId ORDER BY created_at DESC LIMIT 5";
    $resList = mysqli_query($con, $sqlList);
    if ($resList) {
        while ($row = mysqli_fetch_assoc($resList)) {
            $notifications[] = $row;
        }
    }
}
?>

<header class="main-header">
    <div class="header-left">
        <div class="breadcrumb">
            <span class="root-page">
                <i class="fa-solid fa-house"></i> Nhà trọ VIP
            </span>
            <span class="divider">/</span>
            <span class="current-page">
                <?php echo htmlspecialchars($currentPage); ?>
            </span>
        </div>
    </div>

    <div class="header-right">

        <div class="notify-box" id="notifyBox">
            <div class="notify-icon">
                <i class="fa-solid fa-bell"></i>
                <?php if ($notifyCount > 0): ?>
                    <span class="badge" id="notifyBadge"><?php echo $notifyCount; ?></span>
                <?php endif; ?>
            </div>

            <div class="notify-dropdown" id="notifyDropdown">
                <div class="notify-header">Thông báo mới nhất</div>
                <ul class="notify-list">
                    <?php if (empty($notifications)): ?>
                        <li class="empty-msg">Không có thông báo nào.</li>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <li class="notify-item <?php echo $notif['is_read'] == 0 ? 'unread' : ''; ?>"
                                data-id="<?php echo $notif['id']; ?>">
                                <a href="<?php echo $notif['link'] ? htmlspecialchars($notif['link']) : '#'; ?>">
                                    <div class="content">
                                        <div class="title"><?php echo htmlspecialchars($notif['title']); ?></div>
                                        <div class="time"><?php echo date('H:i d/m', strtotime($notif['created_at'])); ?></div>
                                    </div>
                                    <?php if ($notif['is_read'] == 0): ?>
                                        <span class="dot"></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <div class="notify-footer">
                    <a href="notification.php">Xem tất cả</a>
                </div>
            </div>
        </div>

        <div class="user-info">
            <div class="text">
                <span class="name"><?php echo htmlspecialchars($userName); ?></span>
                <span class="role"><?php echo htmlspecialchars($userRole); ?></span>
            </div>
            <i class="fa-solid fa-circle-user avatar"></i>
        </div>

        <form action="logout.php" method="post">
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </button>
        </form>
    </div>
</header>
<script src="../../../public/js/landlord/common.js"></script>