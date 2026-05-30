const API_URL = '../../controllers/landlord/BuildingController.php';

const BuildingAPI = {
    /** Lấy danh sách tòa nhà kèm phân trang */
    getAll: async (page) => {
        const response = await fetch(`${API_URL}?action=get_all&page=${page}`);
        return response.json();
    },

    /** Gửi dữ liệu từ form (Thêm, Sửa, Xóa) */
    submitForm: async (formData) => {
        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });
        return response.json();
    }
};