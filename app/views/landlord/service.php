<?php $page = 'service'; ?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/landlord/head.php'; ?>
<body>
    <?php include '../layout/landlord/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../layout/landlord/header.php'; ?>
        <div class="page-container">
            <div class="toolbar-container">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchService" placeholder="Tìm tên dịch vụ...">
                </div>

                <div class="action-buttons">
                    <button class="btn btn-add" id="btnOpenAddService">
                        <i class="fa-solid fa-plus"></i> Thêm dịch vụ
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table" id="serviceTable">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Mã DV</th>
                            <th style="width: 25%;">Tên dịch vụ</th>
                            <th style="width: 15%;">Đơn giá</th>
                            <th style="width: 10%;">Đơn vị</th>
                            <th style="width: 15%;">Loại</th>
                            <th style="width: 15%;">Mô tả</th>
                            <th style="width: 10%; text-align: center;">Chức năng</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalAddService" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Thêm Dịch Vụ Mới</h3>
                <span class="close-btn add-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddService">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <div class="form-group"><label>Mã Dịch Vụ <span class="required">*</span></label><input type="text" name="code" placeholder="VD: DV01" required></div>
                        <div class="form-group" style="flex: 2;"><label>Tên Dịch Vụ <span class="required">*</span></label><input type="text" name="name" placeholder="VD: Điện sinh hoạt" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Đơn giá (VNĐ) <span class="required">*</span></label><input type="number" name="price" placeholder="VD: 3500" required></div>
                        <div class="form-group"><label>Đơn vị tính <span class="required">*</span></label><input type="text" name="unit" placeholder="VD: kWh" required></div>
                    </div>
                    <div class="form-group">
                        <label>Loại dịch vụ</label>
                        <select name="type" class="form-control">
                            <option value="variable">Theo chỉ số (Điện/Nước)</option>
                            <option value="fixed">Cố định (Theo tháng/người)</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Mô tả chi tiết</label><textarea name="description" rows="2" placeholder="Ví dụ: Áp dụng cho khối 1"></textarea></div>
                    <div class="modal-footer"><button type="button" class="btn-cancel add-close">Hủy bỏ</button><button type="submit" class="btn-save">Lưu lại</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditService" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cập Nhật Dịch Vụ</h3>
                <span class="close-btn edit-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formEditService">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-row">
                        <div class="form-group"><label>Mã DV (Không sửa)</label><input type="text" id="edit_code" disabled style="background: #eee;"></div>
                        <div class="form-group" style="flex: 2;"><label>Tên Dịch Vụ</label><input type="text" name="name" id="edit_name" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Đơn giá</label><input type="number" name="price" id="edit_price" required></div>
                        <div class="form-group"><label>Đơn vị</label><input type="text" name="unit" id="edit_unit" required></div>
                    </div>
                    <div class="form-group">
                        <label>Loại dịch vụ</label>
                        <select name="type" id="edit_type" class="form-control">
                            <option value="variable">Theo chỉ số (Điện/Nước)</option>
                            <option value="fixed">Cố định (Theo tháng/người)</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Mô tả chi tiết</label><textarea name="description" id="edit_description" rows="2"></textarea></div>
                    <div class="modal-footer"><button type="button" class="btn-cancel edit-close">Hủy bỏ</button><button type="submit" class="btn-save">Cập nhật</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalDeleteService" class="modal">
        <div class="modal-content" style="width: 400px; margin-top: 15%;">
            <div class="modal-header">
                <h3>Xác nhận xóa</h3><span class="close-btn delete-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formDeleteService">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <p>Bạn có chắc chắn muốn xóa dịch vụ: <strong id="delete_name_display">...</strong>?</p>
                    <div class="modal-footer"><button type="button" class="btn-cancel delete-close">Hủy bỏ</button><button type="submit" class="btn-save btn-delete">Đồng ý xóa</button></div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>