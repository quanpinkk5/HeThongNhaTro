const API_URL = '../../controllers/landlord/MaintenanceController.php';

const MaintenanceAPI = {
    /** Lấy danh sách toàn bộ yêu cầu bảo trì */
    getAll: async () => {
        const response = await fetch(`${API_URL}?action=get_all`);
        return response.json();
    },

    /** Lấy danh sách hình ảnh đính kèm của một yêu cầu */
    getImages: async (id) => {
        const response = await fetch(`${API_URL}?action=get_images&id=${id}`);
        return response.json();
    },

    /** Gửi Form dữ liệu (Sửa, Xóa) */
    submitForm: async (formData) => {
        const response = await fetch(API_URL, { method: 'POST', body: formData });
        return response.json();
    }
};