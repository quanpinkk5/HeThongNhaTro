<?php
session_start();
$page = 'quanlikhuvuc';
$page_title = 'Quản lý khu vực';
?>

<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/admin/head.php'; ?>

<body>

<?php include '../layout/admin/sidebar.php'; ?>
<?php include '../layout/admin/header.php'; ?>

<div class="main-content">
    <h2>📍 Quản lý khu vực</h2>

    <div class="action-bar">
        <div class="filter-form">
            <input type="text" id="keyword"
                   placeholder="Tìm kiếm theo tên khu vực...">

            <select id="status">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="ACTIVE">Hiển thị</option>
                <option value="HIDDEN">Đã ẩn</option>
            </select>

            <button class="btn-filter" onclick="applyFilter()">Lọc</button>
            <button class="btn-reset" onclick="resetFilter()">Reset</button>
        </div>

        <button class="btn-add" onclick="openAddModal()">➕ Thêm khu vực</button>
    </div>

    <table class="area-table">
        <thead>
            <tr>
                <th width="100">ID</th>
                <th>Tên khu vực</th>
                <th>Trạng thái</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody id="areaTable"></tbody>
    </table>
</div>


<!-- MODAL -->
<div class="modal" id="areaModal">
    <div class="modal-content">
        <h3 id="modalTitle">Thêm khu vực</h3>

        <input type="hidden" id="area_id">
        <input type="hidden" id="area_action" value="add">

        <label>Tên khu vực</label>
        <input type="text" id="area_name" required>

        <div class="modal-actions">
            <button class="btn-save" onclick="saveArea()">Lưu</button>
            <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
        </div>
    </div>
</div>

<?php include '../layout/admin/footer.php'; ?>


