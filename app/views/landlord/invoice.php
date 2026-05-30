<?php $page = 'invoice'; ?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/landlord/head.php'; ?>
<body>
    <?php include '../layout/landlord/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../layout/landlord/header.php'; ?>

        <div class="page-container">
            <div class="toolbar-container">
                <form id="filterForm" class="filter-form">
                    <div class="filter-item">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="f_keyword" placeholder="Tìm tên, phòng...">
                    </div>
                    <div class="filter-item">
                        <select id="f_building">
                            <option value="">-- Tất cả tòa --</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <span style="font-size: 13px; color: #555; margin-right: 5px;">Tháng:</span>
                        <input type="month" id="f_month">
                    </div>
                    <div class="filter-item">
                        <select id="f_status">
                            <option value="">-- Trạng thái --</option>
                            <option value="paid">Đã thanh toán</option>
                            <option value="unpaid">Chưa thanh toán</option>
                        </select>
                    </div>
                </form>
                <div class="action-buttons">
                    <button class="btn btn-add" id="btnOpenModal"><i class="fa-solid fa-plus"></i> Lập hóa đơn</button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Tòa nhà</th>
                            <th>Phòng</th>
                            <th>Khách hàng</th>
                            <th>Tháng Thu</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceTableBody">
                    </tbody>
                </table>
                <div class="pagination" id="paginationContainer"></div>
            </div>
        </div>
    </div>

    <div id="invoiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Lập Hóa Đơn</h3><span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddInvoice">
                    <input type="hidden" name="action" value="create">
                    <div class="form-row">
                        <div class="form-group"><label>Tháng thu tiền</label><input type="month" name="month" id="add_month" required style="width:100%; padding:8px;"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Chọn Tòa nhà</label><select id="modalBuildingSelect" class="form-control"></select></div>
                        <div class="form-group"><label>Chọn Phòng (Đang thuê)</label><select name="contract_id" id="modalContractSelect" class="form-control" disabled>
                                <option value="">-- Chọn tòa trước --</option>
                            </select></div>
                    </div>
                    <div class="info-box-static">
                        <p>Khách hàng: <strong id="displayUser">...</strong></p>
                        <p>Tiền phòng: <strong id="displayRoomPrice" style="color:#d32f2f">0</strong> đ</p>
                        <input type="hidden" id="rawRoomPrice" value="0">
                    </div>
                    <hr><label>Chi tiết dịch vụ:</label>
                    <div id="servicesArea" style="padding:10px; background:#f9f9f9; border:1px solid #eee; margin-bottom:10px;">
                        <p style="text-align:center; color:#888;">Vui lòng chọn phòng...</p>
                    </div>
                    <div style="text-align:right; font-size:18px; font-weight:bold; color:red;">Tổng: <span id="finalTotal">0</span> đ</div>
                    <div class="modal-footer"><button type="button" class="btn-cancel close-btn-btn">Hủy</button><button type="submit" class="btn-save">Lưu Hóa Đơn</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="viewInvoiceModal" class="modal">
        <div class="modal-content" style="width: 700px;">
            <div class="modal-header">
                <h3>Chi Tiết Hóa Đơn #<span id="viewInvId"></span></h3><span class="close-view-btn">&times;</span>
            </div>
            <div class="modal-body">
                <div class="invoice-header-info">
                    <div class="col-left">
                        <p><strong>Tòa nhà:</strong> <span id="viewBuilding"></span></p>
                        <p><strong>Phòng:</strong> <span id="viewRoom"></span></p>
                        <p><strong>Tháng:</strong> <span id="viewMonth"></span></p>
                    </div>
                    <div class="col-right">
                        <p><strong>Khách hàng:</strong> <span id="viewUser"></span></p>
                        <p><strong>SĐT:</strong> <span id="viewPhone"></span></p>
                        <p><strong>Trạng thái:</strong> <span id="viewStatus"></span></p>
                    </div>
                </div>
                <table class="invoice-detail-table">
                    <thead>
                        <tr>
                            <th>Nội dung</th>
                            <th class="text-right">Đơn giá</th>
                            <th class="text-center">SL</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="viewItemsBody"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                            <td class="text-right"><strong id="viewTotal" style="color:red; font-size:16px;"></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer"><button type="button" class="btn-cancel close-view-btn">Đóng</button></div>
        </div>
    </div>

    <div id="modalConfirmPay" class="modal">
        <div class="modal-content" style="width: 400px; margin-top: 15%;">
            <div class="modal-header" style="background-color: #28a745;">
                <h3>Xác nhận thu tiền</h3><span class="close-pay-btn" style="cursor:pointer; color:white; font-size:20px;">&times;</span>
            </div>
            <div class="modal-body" style="text-align: center; padding: 25px;">
                <div style="font-size: 50px; color: #28a745; margin-bottom: 15px;"><i class="fa-regular fa-circle-check"></i></div>
                <p>Xác nhận đã nhận đủ tiền hóa đơn <strong>#<span id="confPayDisplayId"></span></strong>?</p>
                <form id="formConfirmPay"><input type="hidden" name="action" value="pay"><input type="hidden" name="id" id="confPayInputId">
                    <div style="display: flex; justify-content: center; gap: 10px; margin-top:20px;"><button type="button" class="btn-cancel close-pay-btn">Hủy bỏ</button><button type="submit" class="btn-save" style="background-color: #28a745; border:none;">Đồng ý</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalConfirmDelete" class="modal">
        <div class="modal-content" style="width: 400px; margin-top: 15%;">
            <div class="modal-header" style="background-color: #dc3545;">
                <h3>Xác nhận xóa</h3><span class="close-delete-btn" style="cursor:pointer; color:white; font-size:20px;">&times;</span>
            </div>
            <div class="modal-body" style="text-align: center; padding: 25px;">
                <div style="font-size: 50px; color: #dc3545; margin-bottom: 15px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <p>Xóa hóa đơn <strong>#<span id="confDeleteDisplayId"></span></strong>?</p>
                <form id="formConfirmDelete"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" id="confDeleteInputId">
                    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;"><button type="button" class="btn-cancel close-delete-btn">Hủy bỏ</button><button type="submit" class="btn-save" style="background-color: #dc3545; border:none;">Xóa ngay</button></div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>