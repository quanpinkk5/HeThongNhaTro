<?php $page = 'contract'; ?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/landlord/head.php'; ?>
<body>
    <?php include '../layout/landlord/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../layout/landlord/header.php'; ?>
        <div class="page-container">
            <div class="toolbar-container">
                <form id="filterForm" class="filter-group">
                    <div class="filter-item search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="f_keyword" placeholder="Tìm tên, phòng, mã HĐ...">
                    </div>
                    <div class="filter-item">
                        <select id="f_building">
                            <option value="">-- Tất cả tòa --</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <select id="f_status">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="active">Còn hiệu lực</option>
                            <option value="warning">Sắp hết hạn (30 ngày)</option>
                            <option value="expired">Quá hạn</option>
                            <option value="ended">Đã thanh lý</option>
                        </select>
                    </div>
                </form>

                <div class="action-buttons">
                    <button class="btn btn-warning" id="btnOpenRenewalRequests" style="display:none;">
                        <i class="fa-solid fa-bell"></i> Yêu cầu <span class="badge-count" id="req_count">0</span>
                    </button>
                    <button class="btn btn-add" id="btnOpenAddContract"><i class="fa-solid fa-plus"></i> Tạo hợp đồng</button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Tòa</th>
                            <th>Phòng</th>
                            <th>Khách thuê</th>
                            <th>Ngày BĐ</th>
                            <th>Ngày KT</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="contractTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalRenewalRequests" class="modal">
        <div class="modal-content" style="width: 750px;">
            <div class="modal-header">
                <h3>Danh Sách Yêu Cầu Gia Hạn</h3><span class="close-btn req-close">&times;</span>
            </div>
            <div class="modal-body">
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Phòng</th>
                            <th>Khách hàng</th>
                            <th>Muốn gia hạn</th>
                            <th>Lý do/Ghi chú</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="renewalTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalAddContract" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tạo Hợp Đồng Mới</h3><span class="close-btn add-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddContract">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Khách Thuê <span class="required">*</span></label>
                        <select name="user_id" id="modal_tenant_select" class="form-control" required></select>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Chọn Tòa nhà</label><select id="modal_building_select" class="form-control"></select></div>
                        <div class="form-group"><label>Chọn Phòng</label><select name="room_id" id="modal_room_select" class="form-control" disabled required>
                                <option value="">-- Vui lòng chọn tòa trước --</option>
                            </select></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Ngày bắt đầu</label><input type="date" name="start_date" id="startDate" class="form-control" required onchange="calcEndDate()"></div>
                        <div class="form-group"><label>Thời hạn</label><select name="duration" id="duration" class="form-control" onchange="calcEndDate()">
                                <option value="6">6 Tháng</option>
                                <option value="12">1 Năm</option>
                            </select></div>
                        <div class="form-group"><label>Kết thúc</label><input type="date" id="endDate" class="form-control" readonly></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn-save">Tạo hợp đồng</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalViewContract" class="modal">
        <div class="modal-content" style="width: 650px;">
            <div class="modal-header">
                <h3>Chi Tiết Hợp Đồng</h3><span class="close-btn view-close">&times;</span>
            </div>
            <div class="modal-body contract-detail-view">
                <div class="detail-section">
                    <h4><i class="fa-solid fa-file-contract"></i> Thông tin chung</h4>
                    <p><strong>Mã HĐ:</strong> <span id="v_code" class="highlight-text"></span></p>
                    <p><strong>Trạng thái:</strong> <span id="v_status"></span></p>
                </div>
                <hr>
                <div class="detail-section">
                    <h4><i class="fa-solid fa-user"></i> Khách thuê</h4>
                    <div class="info-grid">
                        <p><strong>Họ tên:</strong> <span id="v_cus"></span></p>
                        <p><strong>CCCD:</strong> <span id="v_cccd"></span></p>
                        <p><strong>SĐT:</strong> <span id="v_phone"></span></p>
                        <p><strong>Email:</strong> <span id="v_email"></span></p>
                    </div>
                </div>
                <hr>
                <div class="detail-section">
                    <h4><i class="fa-solid fa-house"></i> Phòng thuê</h4>
                    <p><strong>Tòa nhà:</strong> <span id="v_build"></span></p>
                    <p><strong>Phòng:</strong> <span id="v_room" class="highlight-text"></span></p>
                    <p><strong>Địa chỉ:</strong> <span id="v_addr"></span></p>
                </div>
                <hr>
                <div class="detail-section">
                    <h4><i class="fa-regular fa-calendar-days"></i> Thời hạn hợp đồng</h4>
                    <div class="date-row">
                        <p><strong>Ngày bắt đầu:</strong> <span id="v_start"></span></p>
                        <p><strong>Ngày kết thúc:</strong> <span id="v_end"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn-cancel view-close">Đóng</button></div>
        </div>
    </div>

    <div id="modalRenewContract" class="modal">
        <div class="modal-content" style="width:400px">
            <div class="modal-header">
                <h3>Gia Hạn</h3><span class="close-btn renew-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formRenewContract"><input type="hidden" name="action" value="renew"><input type="hidden" name="id" id="renew_id_input">
                    <p>HĐ: <strong id="renew_code"></strong></p><label>Thời gian:</label><select name="months" class="form-control">
                        <option value="6">6 Tháng</option>
                        <option value="12">1 Năm</option>
                    </select><br><button type="submit" class="btn-save" style="margin-top:10px;">Xác nhận</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalCancelContract" class="modal">
        <div class="modal-content" style="width:400px">
            <div class="modal-header">
                <h3>Hủy hợp đồng</h3><span class="close-btn cancel-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formCancelContract"><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" id="cancel_id">
                    <p>Bạn muốn hủy Hợp đồng <strong id="cancel_code_display"></strong>?</p><button type="submit" class="btn-save btn-delete" style="background:#d32f2f; margin-top:10px;">Đồng ý</button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>