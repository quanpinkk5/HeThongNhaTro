<?php $page = 'maintenance'; ?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/landlord/head.php'; ?>
<body>
    <?php include '../layout/landlord/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../layout/landlord/header.php'; ?>

        <div class="page-container">
            <div class="toolbar-container">
                <div class="status-tabs">
                    <button id="tab-all" class="tab-btn active" onclick="filterTable('all')">Tất cả</button>
                    <button id="tab-pending" class="tab-btn" onclick="filterTable('PENDING')">Chờ xử lý <span class="counter red" id="cnt_pending" style="display:none;">0</span></button>
                    <button id="tab-processing" class="tab-btn" onclick="filterTable('PROCESSING')">Đang xử lý <span class="counter blue" id="cnt_processing" style="display:none;">0</span></button>
                    <button id="tab-done" class="tab-btn" onclick="filterTable('DONE')">Hoàn thành</button>
                </div>

                <div class="action-buttons">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" placeholder="Tìm sự cố, tên phòng...">
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table" id="maintenanceTable">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Mã BT</th>
                            <th style="width: 15%;">Phòng / Tòa</th>
                            <th style="width: 15%;">Người báo</th>
                            <th style="width: 20%;">Nội dung sự cố</th>
                            <th style="width: 10%;">Mức độ</th>
                            <th style="width: 10%;">Ngày báo</th>
                            <th style="width: 10%;">Chi phí</th>
                            <th style="width: 12%;">Trạng thái</th>
                            <th style="width: 10%; text-align: center;">Chức năng</th>
                        </tr>
                    </thead>
                    <tbody id="maintenanceTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalEditMaintenance" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Xử Lý Sự Cố</h3><span class="close-btn edit-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formEditMaintenance">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-row">
                        <div class="form-group"><label>Phòng báo cáo</label><input type="text" id="edit_room" readonly style="background: #eee;"></div>
                        <div class="form-group"><label>Người báo</label><input type="text" id="edit_reporter" readonly style="background: #eee;"></div>
                    </div>
                    <div class="form-group"><label>Nội dung sự cố (Khách báo)</label><textarea id="edit_content" rows="3" class="form-control" readonly style="background: #eee;"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Đánh giá Mức độ</label><select name="level" id="edit_level" class="form-control">
                                <option value="LOW">Thấp</option>
                                <option value="MEDIUM">Bình thường</option>
                                <option value="HIGH">Khẩn cấp</option>
                            </select></div>
                        <div class="form-group"><label>Trạng thái xử lý</label><select name="status" id="edit_status" class="form-control">
                                <option value="PENDING">Chờ xử lý</option>
                                <option value="PROCESSING">Đang xử lý</option>
                                <option value="DONE">Hoàn thành</option>
                            </select></div>
                    </div>
                    <div class="form-group"><label>Chi phí sửa chữa thực tế (VNĐ)</label><input type="number" name="cost" id="edit_cost" placeholder="0" class="form-control"><small style="color: #666;">Nhập chi phí sau khi đã sửa xong để tính vào báo cáo.</small></div>
                    <div class="modal-footer"><button type="button" class="btn-cancel edit-close">Hủy bỏ</button><button type="submit" class="btn-save">Cập nhật</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalDeleteMaintenance" class="modal">
        <div class="modal-content" style="width: 400px; margin-top: 15%;">
            <div class="modal-header">
                <h3>Xác nhận xóa</h3><span class="close-btn delete-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formDeleteMaintenance"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" id="delete_id">
                    <p>Xóa bản ghi sự cố <strong id="delete_code_display">...</strong>?</p>
                    <p style="color: #666; font-size: 13px;">Chỉ nên xóa báo cáo sai hoặc spam.</p>
                    <div class="modal-footer"><button type="button" class="btn-cancel delete-close">Hủy bỏ</button><button type="submit" class="btn-save btn-delete">Đồng ý xóa</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalViewDetail" class="modal">
        <div class="modal-content" style="width: 700px;">
            <div class="modal-header">
                <h3>Chi Tiết Sự Cố</h3><span class="close-btn view-close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item"><strong>Phòng:</strong> <span id="view_room"></span></div>
                    <div class="detail-item"><strong>Người báo:</strong> <span id="view_reporter"></span></div>
                    <div class="detail-item"><strong>Ngày báo:</strong> <span id="view_date"></span></div>
                    <div class="detail-item"><strong>Mức độ:</strong> <span id="view_level"></span></div>
                    <div class="detail-item"><strong>Trạng thái:</strong> <span id="view_status"></span></div>
                    <div class="detail-item"><strong>Chi phí:</strong> <span id="view_cost" style="color:#d32f2f; font-weight:bold;"></span></div>
                </div>
                <div class="detail-content"><strong>Nội dung:</strong>
                    <p id="view_content" style="background:#f9f9f9; padding:10px; border-radius:4px; margin-top:5px;"></p>
                </div>
                <div class="detail-images"><strong>Hình ảnh đính kèm:</strong>
                    <div id="view_image_gallery" class="gallery-grid">
                        <div class="loading-spinner">Đang tải ảnh...</div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn-cancel view-close">Đóng</button></div>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>