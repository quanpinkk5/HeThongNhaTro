const API_URL = '../../controllers/landlord/DashboardController.php';

const DashboardAPI = {
    /** Lấy toàn bộ dữ liệu thống kê, danh sách và biểu đồ cho Dashboard */
    getDashboardData: async () => {
        const response = await fetch(`${API_URL}?action=get_dashboard_data`);
        return response.json();
    }
};