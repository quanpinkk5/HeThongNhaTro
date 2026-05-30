<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// require_once '../../config/database.php';

// /* ====== TÊN TRANG ====== */
// $pageNames = [
//     'quanlytaikhoan' => 'Quản lý tài khoản',
//     'duyetphong'     => 'Quản lý duyệt phòng',
//     'nhatky'         => 'Quản lý nhật ký',
//     'quanlikhuvuc'   => 'Quản lý khu vực',
//     'notifications'  => 'Thông Báo'
// ];
// $currentPage = $pageNames[$page] ?? 'Dashboard';


// $userName = $_SESSION['user_name'] ?? 'Unknown';
// $userRole = $_SESSION['user_role'] ?? 'Guest';


// $notifyCount = 0;
// $userId = $_SESSION['user_id'] ?? 5;
// if ($userId > 0) {
//     $stmt = $con->prepare("
//         SELECT COUNT(*) 
//         FROM notifications 
//         WHERE user_id = ? AND is_read = 0
//     ");
//     $stmt->bind_param("i", $userId);
//     $stmt->execute();
//     $stmt->bind_result($notifyCount);
//     $stmt->fetch();
//     $stmt->close();
// }
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ====== TÊN TRANG ====== */
$pageNames = [
    'quanlytaikhoan' => 'Quản lý tài khoản',
    'duyetphong'     => 'Quản lý duyệt phòng',
    'nhatky'         => 'Quản lý nhật ký',
    'quanlikhuvuc'   => 'Quản lý khu vực',
    'notifications'  => 'Thông Báo'
];

$currentPage = $pageNames[$page] ?? 'Dashboard';

$userName = $_SESSION['user_name'] ?? 'Unknown';
$userRole = $_SESSION['user_role'] ?? 'Guest';
?>


<header>
    <div class="header-left">
        <div class="breadcrumb">
            <span class="root-page">
                <i class="fa-solid fa-house"></i> Nhà trọ VIP
            </span>
            <span class="divider">/</span>
            <span class="current-page"><?= htmlspecialchars($currentPage) ?></span>
        </div>
    </div>

    <div class="header-right">


        <a href="notifications.php" class="notify-box">
            <i class="fa-solid fa-bell"></i>
            <span class="badge" id="notifyBadge" style="display:none">0</span>
        </a>


        <div class="user-info">
            <div class="text">
                <span class="name"><?= htmlspecialchars($userName) ?></span>
                <span class="role"><?= ucfirst($userRole) ?></span>
            </div>
            <i class="fa-solid fa-circle-user avatar"></i>
        </div>


        <form action="logout.php" method="POST">
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </button>
        </form>

    </div>
</header>
<script>
    document.addEventListener("DOMContentLoaded", loadNotifyCount);

    async function loadNotifyCount() {
        const res = await fetch("/public/js/api/Admin/notification_api.php?count=1");
        const data = await res.json();

        const badge = document.getElementById("notifyBadge");

        if (data.count > 0) {
            badge.style.display = "inline-block";
            badge.innerText = data.count;
        } else {
            badge.style.display = "none";
        }
    }
</script>