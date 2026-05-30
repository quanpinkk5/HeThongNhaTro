<?php $page = 'tenant'; ?>
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
                    <input type="text" id="searchInput" placeholder="Tìm tên khách, SĐT, CCCD...">
                </div>
            </div>

            <div class="table-container">
                <table class="data-table" id="customerTable">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Mã KH</th>
                            <th style="width: 25%;">Họ và Tên</th>
                            <th style="width: 15%;">CCCD</th>
                            <th style="width: 15%;">SĐT</th>
                            <th style="width: 20%;">Email</th>
                            <th style="width: 15%; text-align: center;">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                    </tbody>
                </table>

                <div class="pagination" id="paginationContainer">
                </div>
            </div>
        </div>
    </div>

    <div id="modalViewCustomer" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Thông Tin Khách Thuê</h3>
                <span class="close-btn view-close">&times;</span>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group"><label>Mã Khách Hàng</label><input type="text" id="view_code" readonly style="background: #f9f9f9;"></div>
                        <div class="form-group" style="flex: 2;"><label>Họ và Tên</label><input type="text" id="view_name" readonly style="background: #f9f9f9;"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>CCCD/CMND</label><input type="text" id="view_cccd" readonly style="background: #f9f9f9;"></div>
                        <div class="form-group"><label>Số điện thoại</label><input type="text" id="view_phone" readonly style="background: #f9f9f9;"></div>
                    </div>
                    <div class="form-group"><label>Email liên hệ</label><input type="email" id="view_email" readonly style="background: #f9f9f9;"></div>
                    <div class="form-group"><label>Ngày duyệt</label><input type="text" id="view_created" readonly style="background: #f9f9f9;"></div>
                    <div class="modal-footer"><button type="button" class="btn-cancel view-close">Đóng</button></div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>