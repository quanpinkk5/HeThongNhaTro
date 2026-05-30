<?php
$page = 'notifications';
$page_title = 'Thông báo';
?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/admin/head.php'; ?>

<body>

<?php include '../layout/admin/sidebar.php'; ?>
<?php include '../layout/admin/header.php'; ?>

<div class="main-content">
    <h2>🔔 Thông báo hệ thống</h2>

    <div class="notify-actions">
        <label>
            <input type="checkbox" id="checkAll"> Chọn tất cả
        </label>

        <button type="button" 
                class="btn-delete-multi" 
                id="btnDeleteMulti">
            🗑️ Xóa đã chọn
        </button>
    </div>

    <div class="notification-list">
        <!-- JS render ở đây -->
    </div>
</div>

<!-- ===== MODAL CONFIRM ===== -->
<div id="confirmModal" class="modal">
    <div class="modal-content confirm-box">
        <h3 id="confirmTitle">Xác nhận</h3>
        <p id="confirmMessage"></p>

        <div class="confirm-actions">
            <button id="confirmYes" class="btn-confirm yes">✔ Đồng ý</button>
            <button id="confirmNo" class="btn-confirm no">✖ Hủy</button>
        </div>
    </div>
</div>

<?php include '../layout/admin/footer.php'; ?>

