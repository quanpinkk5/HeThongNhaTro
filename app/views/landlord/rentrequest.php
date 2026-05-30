<?php $page = 'rentrequest'; ?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/landlord/head.php'; ?>

<body>
    <?php include '../layout/landlord/sidebar.php'; ?>

    <div class="main-content">
        <?php include '../layout/landlord/header.php'; ?>
        <div class="page-container">
            <div class="filter-bar">
                <div class="filter-item">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="filterKeyword" placeholder="Tìm tên, SĐT...">
                </div>
                <div class="filter-item">
                    <i class="fa-solid fa-filter"></i>
                    <select id="filterStatus">
                        <option value="">Tất cả trạng thái</option>
                        <option value="PENDING">Chờ duyệt</option>
                        <option value="APPROVED">Đã duyệt</option>
                        <option value="REJECTED">Đã hủy</option>
                    </select>
                </div>
            </div>

            <div class="request-layout">
                <div class="request-list-box">
                    <div class="box-header">
                        <h3>Danh sách yêu cầu (<span id="requestCount">0</span>)</h3>
                    </div>
                    <div class="list-wrapper">
                        <table class="request-table">
                            <thead>
                                <tr>
                                    <th>Ngày gửi</th>
                                    <th>Khách hàng</th>
                                    <th>Phòng</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="requestTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="request-detail-box">
                    <form id="actionForm" style="display: flex; flex-direction: column; height: 100%;">
                        <input type="hidden" id="formRequestId">

                        <div class="detail-header">
                            <h3 id="d_title">Chi tiết yêu cầu</h3>
                            <span id="d_status_badge" class="status-tag" style="display:none;"></span>
                        </div>

                        <div class="detail-body">
                            <div id="detail_content" style="display:none;">
                                <div class="info-section">
                                    <h4><i class="fa-solid fa-user"></i> Thông tin cá nhân</h4>
                                    <div class="info-grid">
                                        <div class="info-row"><span class="label">Họ tên:</span><span class="value" id="d_name">...</span></div>
                                        <div class="info-row"><span class="label">SĐT:</span><span class="value" id="d_phone">...</span></div>
                                        <div class="info-row"><span class="label">CCCD:</span><span class="value" id="d_cccd">...</span></div>
                                        <div class="info-row"><span class="label">Email:</span><span class="value" id="d_email">...</span></div>
                                    </div>
                                </div>

                                <div class="info-section">
                                    <h4><i class="fa-solid fa-house"></i> Thông tin đăng ký trọ</h4>
                                    <div class="info-grid">
                                        <div class="info-row"><span class="label">Tòa nhà:</span><span class="value" id="d_building">...</span></div>
                                        <div class="info-row"><span class="label">Phòng:</span><span class="value link-color" id="d_room">...</span></div>
                                        <div class="info-row"><span class="label">Giá thuê:</span><span class="value" id="d_price">...</span></div>
                                        <div class="info-row"><span class="label">Ngày gửi:</span><span class="value" id="d_date">...</span></div>
                                        <div class="info-row"><span class="label">Ghi chú:</span><span class="value" id="d_note">...</span></div>
                                    </div>
                                </div>
                            </div>

                            <div id="no_data_msg" style="text-align: center; padding-top: 50px; color: #999;">
                                <i class="fa-regular fa-folder-open" style="font-size: 40px; margin-bottom: 10px;"></i>
                                <p>Vui lòng chọn một yêu cầu để xem chi tiết.</p>
                            </div>
                        </div>

                        <div class="detail-actions" id="action_buttons" style="display:none;">
                            <button type="button" class="btn-action btn-reject" onclick="submitReject()"><i class="fa-solid fa-xmark"></i> Từ chối</button>
                            <button type="button" class="btn-action btn-approve" onclick="submitApprove()"><i class="fa-solid fa-check"></i> Duyệt yêu cầu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>