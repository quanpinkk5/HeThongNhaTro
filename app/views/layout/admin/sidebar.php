<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-house-chimney"></i> HostelPro
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="quanlytaikhoan.php" class="menu-item <?php echo ($page == 'quanlytaikhoan') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users-gear"></i>Quản lý tài khoản
            </a>
        </li>
        <li>
            <a href="quanlyduyetphong.php" class="menu-item <?php echo ($page == 'duyetphong') ? 'active' : ''; ?>">
                <i class="fa-solid fa-clipboard-check"></i>Quản lý duyệt phòng
            </a>
        </li>
        <li>
            <a href="nhatkyhoatdong.php"
                class="menu-item <?= ($page == 'nhatky') ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i>Nhật ký hoạt động
            </a>
        </li>
        <li>
            <a href="quanlykhuvuc.php"
                class="menu-item <?= ($page == 'quanlikhuvuc') ? 'active' : ''; ?>">
                <i class="fa fa-layer-group"></i>Quản Lý Danh Mục
            </a>
        </li>
    </ul>
</div>