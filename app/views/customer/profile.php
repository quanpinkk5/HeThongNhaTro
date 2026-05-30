<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/customer/ProfileController.php';

$controller = new ProfileController($con);
$user = $controller->getUserData($_SESSION['user_id']);

$user_name = $user['name'] ?? 'Người dùng';
$user_email = $user['email'] ?? '';
$user_role = $user['role'] ?? 'USER';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/customer/profile.css">
</head>
<body>

<header class="clean-header">
    <div class="container-header">
        <a href="index.php" class="logo-minimal">
            <i class="fas fa-house-chimney-window"></i> Hostel<span>Pro</span>
        </a>
        <div class="page-indicator">TRANG CÁ NHÂN</div>
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</header>

<main class="profile-container">
    <div class="profile-grid">
        <aside class="profile-sidebar">
            <div class="user-info-card">
                <div class="avatar-large">
                    <div class="avatar-text" id="sideAvatar" style="background: #2563eb; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; margin: 0 auto 15px;">
                        <?= strtoupper(mb_substr($user_name, 0, 1, 'UTF-8')) ?>
                    </div>
                </div>
                <h3 id="sideUserName"><?= htmlspecialchars($user_name) ?></h3>
                <p style="color: #64748b; font-size: 14px; text-transform: uppercase; font-weight: 700;"><?= htmlspecialchars($user_role) ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="#info" class="active"><i class="fas fa-user-edit"></i> Thông tin cơ bản</a>
                <a href="#security"><i class="fas fa-shield-alt"></i> Bảo mật tài khoản</a>
                <a href="../../controllers/customer/LogoutController.php" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </nav>
        </aside>

        <section class="profile-content">
            <div id="alertBox" style="display: none; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;"></div>

            <div class="content-card" id="info">
                <div class="card-header">
                    <h3>Chỉnh sửa hồ sơ</h3>
                    <p>Cập nhật thông tin cá nhân của bạn</p>
                </div>
                <form class="profile-form" id="formUpdateName">
                    <div class="form-group">
                        <label>Email đăng ký (Không thể đổi)</label>
                        <input type="email" value="<?= htmlspecialchars($user_email) ?>" disabled class="disabled-input">
                    </div>
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="full_name" id="inputFullName" value="<?= htmlspecialchars($user_name) ?>" placeholder="Nhập tên mới của bạn..." required>
                    </div>
                    <button type="submit" class="btn-save">Cập nhật tên</button>
                </form>
            </div>

            <div class="content-card" id="security">
                <div class="card-header">
                    <h3>Đổi mật khẩu</h3>
                    <p>Nên sử dụng mật khẩu mạnh để bảo vệ tài khoản</p>
                </div>
                <form class="profile-form" id="formChangePassword">
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="new_password" placeholder="Tối thiểu 6 ký tự" required>
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                    <button type="submit" class="btn-save btn-security">Lưu mật khẩu mới</button>
                </form>
            </div>
        </section>
    </div>
</main>

<script src="../../../public/js/customer/profile.js"></script>

</body>
</html>