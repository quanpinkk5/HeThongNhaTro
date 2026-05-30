<?php
$page = 'quanlytaikhoan';
$page_title = 'Quản lý tài khoản';
?>

<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/admin/head.php'; ?>
<!-- Modal xác nhận -->
<div id="confirmModal" class="modalconfirm">
    <div class="modalconfirm-content">
        <h3 id="modalTitle"></h3>
        <p id="modalText"></p>

        <div class="modal-buttons">
            <button id="btnCancel" class="btn-cancel">Hủy</button>
            <button id="btnConfirm" class="btn-confirm">Xác nhận</button>
        </div>
    </div>
</div>

<body>

    <?php include '../layout/admin/sidebar.php'; ?>
    <?php include '../layout/admin/header.php'; ?>

    <div class="main-content">

        <h2 class="page-title">
            <i class="fa-solid fa-users-gear"></i>
            Quản lý tài khoản
        </h2>
        <div id="globalAlert"></div>

        <div class="action-bar">
            <div class="filter-form">
                <input type="text" id="keyword" placeholder="Tìm kiếm...">
                <select id="role">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="ADMIN">Admin</option>
                    <option value="LANDLORD">Chủ trọ</option>
                    <option value="USER">Người thuê</option>
                </select>

                <select id="status">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="ACTIVE">Hoạt động</option>
                    <option value="BLOCKED">Đã khóa</option>
                </select>
                <button class="btn-filter" onclick="loadUsers()">Lọc</button>
                <a href="quanlytaikhoan.php" class="btn-reset">Reset</a>
                <button class="btn-add" onclick="openAddModal()">Thêm</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userTable"></tbody>
            </table>
        </div>

        <div id="pagination"></div>
    </div>


    <div id="modalAddAcc" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Thêm Tài Khoản</h3>
                <span onclick="closeAddModal()" class="close-btn add-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddAcc">

                    <div class="form-group">
                        <label>Tài khoản</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label>CCCD</label>
                        <input type="text" name="cccd" required>
                    </div>

                    <div class="form-group">
                        <label>Vai trò</label>
                        <select name="role" required>
                            <option value="">-- Chọn vai trò --</option>
                            <option value="LANDLORD">Chủ trọ</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" onclick="closeAddModal()" class="btn-cancel add-close">Hủy</button>
                        <button type="submit" class="btn-save">Lưu</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- Modal xem chi tiết -->
    <div id="modalViewUser" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Chi tiết tài khoản</h3>
                <span class="close-btn view-close">&times;</span>
            </div>

            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" id="view_id" readonly class="readonly">
                    </div>

                    <div class="form-group">
                        <label>Mã tài khoản</label>
                        <input type="text" id="view_code" readonly class="readonly">
                    </div>

                    <div class="form-group">
                        <label>Tài khoản</label>
                        <input type="text" id="view_username" readonly class="readonly">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" id="view_email" readonly class="readonly">
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" id="view_phone" readonly class="readonly">
                    </div>

                    <div class="form-group">
                        <label>CCCD</label>
                        <input type="text" id="view_cccd" readonly class="readonly">
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" id="view_role" readonly class="readonly role-highlight">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <input type="text" id="view_status" readonly class="readonly ">
                    </div>

                    <div class="form-group">
                        <label>Thời gian đăng ký</label>
                        <input type="text" id="view_time" readonly class="readonly">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel view-close">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="modalEditAcc" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Sửa Thông Tin Tài Khoản</h3>
                <span class="close-btn edit-close">&times;</span>
            </div>

            <div class="modal-body">
                <form id="formEditAcc">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group">
                        <label>Tài khoản</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" id="edit_phone" required>
                    </div>

                    <div class="form-group">
                        <label>CCCD</label>
                        <input type="text" name="cccd" id="edit_cccd" required>
                    </div>

                    <div class="form-group">
                        <label>Vai trò</label>
                        <select name="role" id="edit_role">
                            <option value="LANDLORD">Chủ trọ</option>
                            <option value="USER">Người thuê</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" id="edit_status">
                            <option value="ACTIVE">Hoạt động</option>
                            <option value="BLOCKED">Đã khóa</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel edit-close">Hủy</button>
                        <button type="submit" class="btn-save">Cập nhật</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/admin/footer.php'; ?>