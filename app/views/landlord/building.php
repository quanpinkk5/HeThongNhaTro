<?php $page = 'building'; ?>
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
                    <input type="text" id="searchInput" placeholder="Tìm kiếm tòa nhà...">
                </div>

                <div class="action-buttons">
                    <button class="btn btn-add" id="btnOpenModal"><i class="fa-solid fa-plus"></i> Thêm tòa nhà</button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table" id="buildingTable">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Mã</th>
                            <th style="width: 25%;">Tên tòa</th>
                            <th style="width: 30%;">Địa chỉ</th>
                            <th style="width: 10%; text-align: center;">Số tầng</th>
                            <th style="width: 10%; text-align: center;">Tổng phòng</th>
                            <th style="width: 10%; text-align: center;">Trống</th>
                            <th style="width: 10%; text-align: center;">Chức năng</th>
                        </tr>
                    </thead>
                    <tbody id="buildingTableBody">
                    </tbody>
                </table>

                <div class="pagination" id="paginationContainer">
                </div>
            </div>
        </div>
    </div>

    <div id="modalAddBuilding" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Thêm Tòa Nhà Mới</h3>
                <span class="close-btn add-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddBuilding">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group"><label>Mã tòa nhà <span class="required">*</span></label><input type="text" name="code" required></div>
                    <div class="form-group"><label>Tên tòa nhà <span class="required">*</span></label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Địa chỉ</label><textarea name="address" rows="2"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Số tầng</label><input type="number" name="floors" value="1" min="1" required></div>
                        <div class="form-group"><label>Tổng phòng</label><input type="number" disabled placeholder="0"></div>
                    </div>
                    <div class="error-message" style="color: #d9534f; font-size: 14px; margin-bottom: 10px; display: none;"></div>
                    <div class="modal-footer"><button type="button" class="btn-cancel add-close">Hủy</button><button type="submit" class="btn-save">Lưu</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditBuilding" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cập Nhật Thông Tin</h3>
                <span class="close-btn edit-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formEditBuilding">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group"><label>Mã tòa nhà (Không sửa)</label><input type="text" id="edit_code" disabled style="background: #eee;"></div>
                    <div class="form-group"><label>Tên tòa nhà</label><input type="text" name="name" id="edit_name" required></div>
                    <div class="form-group"><label>Địa chỉ</label><textarea name="address" id="edit_address" rows="2"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Số tầng</label><input type="number" name="floors" id="edit_floors" required></div>
                        <div class="form-group"><label>Tổng phòng</label><input type="number" id="edit_rooms" disabled style="background: #eee;"></div>
                    </div>
                    <div class="error-message" style="color: #d9534f; font-size: 14px; margin-bottom: 10px; display: none;"></div>
                    <div class="modal-footer"><button type="button" class="btn-cancel edit-close">Hủy</button><button type="submit" class="btn-save">Cập nhật</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalDeleteBuilding" class="modal">
    <div class="modal-content" style="width: 400px;">
        <div class="modal-header">
            <h3>Xác nhận xóa</h3>
            <span class="close-btn delete-close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="formDeleteBuilding">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <p>Bạn có chắc chắn muốn xóa tòa nhà <strong id="delete_name_display">...</strong> không?</p>
                <p style="color: #666; font-size: 13px;">Hành động này sẽ xóa dữ liệu khỏi hệ thống.</p>
                
                <div class="error-message" style="color: #d9534f; font-size: 14px; margin-top: 10px; display: none;"></div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel delete-close">Hủy bỏ</button>
                    <button type="submit" class="btn-save btn-submit-delete">Đồng ý xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>