<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/css/customer/auth.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card register-card">
        <div class="auth-header">
            <h2>Tạo tài khoản mới</h2>
            <p>Khám phá phòng trọ cùng HostelPro</p>
        </div>
        <div id="messageBox" class="alert" style="display:none;"></div>
        <form id="registerForm">
            <div class="form-group"><label>Họ và tên</label><input type="text" name="fullname" required></div>
            <div class="form-row">
                <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" required></div>
            </div>
            <div class="form-group"><label>CCCD</label><input type="text" name="cccd" required></div>
            <div class="form-row">
                <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" required></div>
                <div class="form-group"><label>Xác nhận</label><input type="password" name="confirm_password" required></div>
            </div>
            <button type="submit" class="btn-submit">ĐĂNG KÝ NGAY</button>
        </form>
        <div class="auth-switch">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
    </div>
</div>
<script src="../../../public/js/customer/register.js"></script>
</body>
</html>