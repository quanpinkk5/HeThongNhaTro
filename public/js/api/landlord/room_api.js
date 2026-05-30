const API_URL = '../../controllers/landlord/RoomController.php';

const RoomAPI = {
    /** Lấy toàn bộ dữ liệu phòng, tòa nhà và dịch vụ */
    getAll: async () => {
        const response = await fetch(`${API_URL}?action=get_all`);
        return response.json();
    },

    /** Xử lý submit cho các Form (Thêm, Sửa, Xóa) có chứa File */
    submitForm: async (formData) => {
        const response = await fetch(API_URL, { method: 'POST', body: formData });
        return response.json();
    },

    /** Gửi yêu cầu đăng tin phòng */
    requestPost: async (id) => {
        const fd = new FormData();
        fd.append("action", "request_post");
        fd.append("id", id);
        const response = await fetch(API_URL, { method: 'POST', body: fd });
        return response.json();
    }
};