const API_URL = '../../controllers/landlord/TenantController.php';

const TenantAPI = {
    /** Lấy danh sách khách thuê kèm phân trang và từ khóa tìm kiếm */
    getList: async (page, limit, keyword = '') => {
        const response = await fetch(`${API_URL}?action=get_list&page=${page}&limit=${limit}&keyword=${encodeURIComponent(keyword)}`);
        return response.json();
    }
};