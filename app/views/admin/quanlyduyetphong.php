<?php
$page = 'duyetphong';
$page_title = 'Quản lý Duyệt phòng';
?>
<!DOCTYPE html>
<html lang="vi">
<?php include '../layout/admin/head.php'; ?>

<body>
    <?php include '../layout/admin/sidebar.php'; ?>
    <?php include '../layout/admin/header.php'; ?>

    <div class="main-content">

        <h2>🏠 Quản lý duyệt phòng</h2>

        <!-- ================= FILTER ================= -->
        <div class="action-bar">
            <form id="filterForm" class="filter-form">

                <input type="text"
                    name="keyword"
                    placeholder="Tìm theo tên phòng, chủ trọ...">

                <select name="status">
                    <option value="">📂 Trạng thái phòng</option>
                    <option value="PENDING">⏳ Chờ duyệt</option>
                    <option value="APPROVED">🟢 Đã duyệt</option>
                    <option value="REJECTED">🔴 Từ chối</option>
                </select>

                <button type="submit" class="btn-filter">Lọc</button>
                <button type="button" id="btnReset" class="btn-reset">Reset</button>

            </form>
        </div>

        <!-- ================= TABLE ================= -->
        <div class="table-wrapper">
            <table class="approval-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên phòng</th>
                        <th>Chủ trọ</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th style="width:20%;text-align:center;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="roomTable">
                    <!-- Render bằng JS -->
                </tbody>
            </table>

            <!-- ================= PAGINATION ================= -->
            <div id="pagination" class="pagination"></div>
        </div>
    </div>

    <!-- ================= MODAL VIEW ================= -->
    <div id="modalViewRoom" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Chi Tiết Phòng</h3>
                <span class="close-btn view-close">&times;</span>
            </div>

            <div class="modal-body">
                <div class="info-row">
                    <p><strong>Tên phòng:</strong> <span id="view_name"></span></p>
                    <p><strong>Chủ trọ:</strong> <span id="view_landlord"></span></p>
                </div>

                <div class="info-row">
                    <p><strong>Giá:</strong>
                        <span id="view_price"
                            style="color:#d35400;font-weight:bold"></span> VNĐ
                    </p>
                    <p><strong>Diện tích:</strong>
                        <span id="view_area"></span> m²
                    </p>
                </div>

                <div class="info-desc">
                    <p><strong>Mô tả:</strong></p>
                    <p id="view_desc"></p>
                </div>

                <div class="room-images" id="view_images"></div>
            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn-cancel view-close">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- ================= CONFIRM MODAL ================= -->
    <div id="confirmModal" class="modal">
        <div class="modal-content confirm-box">
            <h3 id="confirmTitle">Xác nhận</h3>
            <p id="confirmMessage"></p>

            <div class="confirm-actions">
                <button id="confirmYes"
                    class="btn-confirm yes">
                    ✔ Đồng ý
                </button>
                <button id="confirmNo"
                    class="btn-confirm no">
                    ✖ Hủy
                </button>
            </div>
        </div>
    </div>

    <!-- ================= REJECT MODAL ================= -->
    <div id="rejectModal" class="modal">
        <div class="modal-content confirm-box">
            <h3>❌ Từ chối phòng</h3>

            <textarea id="rejectReason"
                placeholder="Nhập lý do từ chối phòng..."
                rows="4"
                style="width:100%;margin:10px 0"></textarea>

            <div class="confirm-actions">
                <button id="rejectSubmit"
                    class="btn-confirm yes">
                    ✔ Xác nhận
                </button>
                <button id="rejectCancel"
                    class="btn-confirm no">
                    ✖ Hủy
                </button>
            </div>
        </div>
    </div>

    <?php include '../layout/admin/footer.php'; ?>