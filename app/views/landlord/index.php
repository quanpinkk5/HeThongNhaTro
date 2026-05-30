<?php $page = 'index'; ?>
<!DOCTYPE html>
<html lang="vi">
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../layout/landlord/head.php';
?>

<body>
    <?php include '../layout/landlord/sidebar.php'; ?>

    <div class="main-content">
        <?php include '../layout/landlord/header.php'; ?>

        <div class="page-container">
            <div class="stats-grid">
                <div class="stat-card border-blue">
                    <div class="stat-icon"><i class="fa-solid fa-house"></i></div>
                    <div class="stat-info">
                        <h3>Tổng số phòng</h3>
                        <div class="number" id="stat-total-rooms">...</div>
                    </div>
                </div>
                <div class="stat-card border-green">
                    <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
                    <div class="stat-info">
                        <h3>Đang thuê</h3>
                        <div class="number" id="stat-rented-rooms">...</div>
                    </div>
                </div>
                <div class="stat-card border-orange">
                    <div class="stat-icon"><i class="fa-solid fa-door-open"></i></div>
                    <div class="stat-info">
                        <h3>Phòng trống</h3>
                        <div class="number" id="stat-empty-rooms">...</div>
                    </div>
                </div>
                <div class="stat-card border-red">
                    <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3>Tổng nợ</h3>
                        <div class="number" id="stat-total-debt">...</div>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-box">
                    <div class="box-header">
                        <div class="box-title">Doanh thu 6 tháng gần nhất</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="chart-box">
                    <div class="box-header">
                        <div class="box-title">Tình trạng lấp đầy</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="occupancyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="lists-grid">
                <div class="list-box">
                    <div class="box-header">
                        <div class="box-title" style="color: #dd4b39;"><i class="fa-solid fa-file-invoice-dollar"></i> Hóa đơn chưa thu</div>
                        <a href="invoice.php?status=unpaid" class="view-all">Xem tất cả</a>
                    </div>
                    <div class="list-content" id="unpaid-invoices-list">
                        <p class="empty-state">Đang tải dữ liệu...</p>
                    </div>
                </div>

                <div class="list-box">
                    <div class="box-header">
                        <div class="box-title" style="color: #f39c12;"><i class="fa-solid fa-clock"></i> Hợp đồng sắp hết hạn</div>
                        <a href="contract.php?status=warning" class="view-all">Xem tất cả</a>
                    </div>
                    <div class="list-content" id="expiring-contracts-list">
                        <p class="empty-state">Đang tải dữ liệu...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php include '../layout/landlord/footer.php'; ?>
</body>

</html>