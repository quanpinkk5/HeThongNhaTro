<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - HostelPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/css/customer/auth.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Chào mừng quay lại</h2>
        <div id="messageBox" class="alert" style="display:none;"></div>
        <form id="loginForm">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>
        <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
    </div>
</div>
<script src="../../../public/js/customer/login.js"></script>
</body>
</html>