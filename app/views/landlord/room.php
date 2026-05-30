<?php $page = 'room'; ?>
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
                    <input type="text" id="searchRoom" placeholder="Tìm kiếm phòng trọ...">
                </div>
                <div class="action-buttons">
                    <button class="btn btn-add" id="btnOpenAddRoom"><i class="fa-solid fa-plus"></i> Thêm phòng trọ</button>
                </div>
            </div>

            <div class="room-grid" id="roomGridBody">
            </div>
        </div>
    </div>

    <div id="modalAddRoom" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Thêm Phòng Trọ Mới</h3><span class="close-btn add-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddRoom" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <div class="form-group"><label>Số phòng (Tên)</label><input type="text" name="title" required></div>
                        <div class="form-group">
                            <label>Thuộc tòa nhà</label>
                            <select name="building_id" id="modalAddBuildingSelect" required style="width: 100%; padding: 8px; border: 1px solid #ddd;"></select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Giá thuê (VNĐ)</label><input type="number" name="price" required></div>
                        <div class="form-group"><label>Diện tích (m2)</label><input type="number" name="area"></div>
                    </div>
                    <div class="form-group">
                        <label>Chọn dịch vụ</label>
                        <div class="service-checkbox-container" id="modalAddServicesContainer"></div>
                    </div>
                    <div class="form-group"><label>Mô tả</label><textarea name="description" rows="2"></textarea></div>
                    <div class="form-group"><label>Ảnh đại diện</label><input type="file" name="image" accept="image/*"></div>
                    <div class="modal-footer"><button type="submit" class="btn-save">Lưu lại</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditRoom" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cập Nhật Phòng Trọ</h3><span class="close-btn edit-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formEditRoom" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-row">
                        <div class="form-group"><label>Tên phòng</label><input type="text" name="title" id="edit_title" required></div>
                        <div class="form-group"><label>Giá thuê</label><input type="number" name="price" id="edit_price" required></div>
                    </div>
                    <div class="form-group"><label>Diện tích (m2)</label><input type="number" name="area" id="edit_area"></div>

                    <div class="form-group">
                        <label>Cập nhật dịch vụ</label>
                        <div class="service-checkbox-container" id="modalEditServicesContainer"></div>
                    </div>

                    <div class="form-group"><label>Mô tả</label><textarea name="description" id="edit_desc" rows="3"></textarea></div>
                    <div class="form-group">
                        <label>Ảnh hiện tại:</label>
                        <div id="current_img_preview" style="margin-bottom: 10px;">
                            <img src="" id="preview_img" style="height: 100px; border: 1px solid #ddd; display: none;">
                        </div>
                        <label>Chọn ảnh mới (Nếu muốn đổi)</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn-save">Cập nhật</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalDeleteRoom" class="modal">
        <div class="modal-content" style="width: 400px; margin-top: 15%;">
            <div class="modal-header">
                <h3>Xác nhận xóa</h3><span class="close-btn delete-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formDeleteRoom">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <p>Xóa phòng: <strong id="delete_room_name">...</strong>?</p>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel delete-close">Hủy</button>
                        <button type="submit" class="btn-save btn-delete" style="background-color: #d32f2f;">Đồng ý</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>