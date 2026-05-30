<?php
$page = 'nhatky';
$page_title = 'Nhật ký hoạt động';
?>

<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/admin/head.php'; ?>
<div id="confirmModal" class="modalconfirm">
    <div class="modalconfirm-content">
        <h3>Xác nhận xuất log</h3>
        <p id="confirmText"></p>

        <div class="modal-buttons">
            <button id="btnCancel" class="btn-cancel" onclick="closeConfirm()">Hủy</button>
            <button id="btnConfirm" class="btn-confirm" onclick="confirmExport()">Xác nhận</button>
        </div>
    </div>
</div>
<body>
    <?php include '../layout/admin/sidebar.php'; ?>
    <?php include '../layout/admin/header.php'; ?>

    <div class="main-content">

        <h2>📜 Nhật ký hoạt động</h2>

        <!-- ================= FILTER ================= -->
        <div class="action-bar">
            <form id="filterForm" class="filter-form">

                <input type="text" name="keyword"
                    placeholder="Tìm admin / hành động / đối tượng...">

                <input type="date" name="date_from">
                <input type="date" name="date_to">

                <select name="target_role">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="ADMIN">Admin</option>
                    <option value="LANDLORD">Chủ trọ</option>
                    <option value="USER">User</option>
                </select>

                <select name="action">
                    <option value="">-- Tất cả hành động --</option>
                    <option value="LOGIN">Đăng nhập</option>
                    <option value="CREATE ACCOUNT">Tạo tài khoản</option>
                    <option value="RESET PASSWORD">Reset mật khẩu</option>
                    <option value="SEND RESET PASSWORD EMAIL">Gửi email</option>
                    <option value="LOCK ACCOUNT">Khóa tài khoản</option>
                    <option value="UNLOCK ACCOUNT">Mở khóa</option>
                </select>

                <button type="submit" class="btn-filter">Lọc</button>
                <button type="button" class="btn-reset" onclick="resetFilter()">Reset</button>
                <button type="button" class="btn-export" onclick="exportExcel()">
                    Xuất Excel
                </button>
            </form>
        </div>

        <!-- ================= TABLE ================= -->
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>IP</th>
                        <th>Thời gian</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody id="logTable"></tbody>
            </table>

            <div id="pagination" class="pagination"></div>
        </div>
    </div>

    <!-- ================= MODAL ================= -->
    <div id="modalViewLog" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📜 Chi tiết nhật ký</h3>
                <span class="close-btn close-log">&times;</span>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>ID</label>
                    <input readonly id="log_id">
                </div>

                <div class="form-group">
                    <label>Admin</label>
                    <input readonly id="log_admin">
                </div>

                <div class="form-group">
                    <label>Hành động</label>
                    <input readonly id="log_action">
                </div>

                <div class="form-group">
                    <label>ID Đối tượng</label>
                    <input readonly id="log_target">
                </div>

                <div class="form-group">
                    <label>Loại đối tượng</label>
                    <input readonly id="log_target_type">
                </div>

                <div class="form-group">
                    <label>Tên đối tượng</label>
                    <input readonly id="log_target_name">
                </div>

                <div class="form-group">
                    <label>Role user</label>
                    <input readonly id="log_target_role">
                </div>

                <div class="form-group">
                    <label>IP</label>
                    <input readonly id="log_ip">
                </div>

                <div class="form-group">
                    <label>User Agent</label>
                    <textarea readonly id="log_agent"></textarea>
                </div>

                <div class="form-group">
                    <label>Thời gian</label>
                    <input readonly id="log_time">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel close-log">Đóng</button>
            </div>
        </div>
    </div>

    <?php include '../layout/admin/footer.php'; ?>