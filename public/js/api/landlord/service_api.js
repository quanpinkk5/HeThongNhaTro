const API_URL = '../../controllers/landlord/ServiceController.php';

const ServiceAPI = {
    /** Lấy danh sách toàn bộ dịch vụ */
    getAll: async () => {
        const response = await fetch(`${API_URL}?action=get_all`);
        return response.json();
    },

    /** Gửi form dữ liệu (Thêm, Sửa, Xóa) */
    submitForm: async (formData) => {
        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });
        return response.json();
    }
};