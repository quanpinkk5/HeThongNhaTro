const API_URL = '../../controllers/landlord/ContractController.php';

const ContractAPI = {
    /**
     * Lấy danh sách phòng trống của một tòa nhà
     */
    getValidRooms: async (buildingId) => {
        const response = await fetch(`${API_URL}?action=get_valid_rooms&building_id=${buildingId}`);
        return response.json();
    },

    /**
     * Lấy toàn bộ dữ liệu chính (Hợp đồng, Yêu cầu, Bộ lọc)
     */
    getAllData: async (building, status, keyword) => {
        const response = await fetch(`${API_URL}?action=get_all_data&building=${building}&status=${status}&keyword=${encodeURIComponent(keyword)}`);
        return response.json();
    },

    /**
     * Xử lý submit cho tất cả các Form (Thêm, Gia hạn, Hủy)
     */
    submitForm: async (formData) => {
        const response = await fetch(API_URL, { method: 'POST', body: formData });
        return response.json();
    },

    /**
     * Xử lý duyệt hoặc từ chối yêu cầu gia hạn
     */
    submitRequest: async (action, id) => {
        const fd = new FormData();
        fd.append('action', action);
        fd.append('id', id);
        const response = await fetch(API_URL, { method: 'POST', body: fd });
        return response.json();
    }
};