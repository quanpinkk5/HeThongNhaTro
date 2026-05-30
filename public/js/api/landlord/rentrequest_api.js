const API_URL = '../../controllers/landlord/RentRequestController.php';

const RentRequestAPI = {
    /** Lấy danh sách yêu cầu thuê phòng có lọc theo từ khóa và trạng thái */
    getAll: async (keyword, status) => {
        const response = await fetch(`${API_URL}?action=get_all&keyword=${encodeURIComponent(keyword)}&status=${status}`);
        return response.json();
    },

    /** Gửi yêu cầu Duyệt hoặc Hủy/Từ chối */
    submitAction: async (action, reqId, reason = '') => {
        const fd = new FormData();
        fd.append('action', action);
        fd.append('request_id', reqId);
        if (reason) fd.append('reason', reason);

        const response = await fetch(API_URL, { method: 'POST', body: fd });
        return response.json();
    }
};