const API_URL = '../../controllers/landlord/InvoiceController.php';

const InvoiceAPI = {
    /** Lấy dữ liệu tổng hợp (Hóa đơn, Bộ lọc, Phân trang) */
    getAllData: async (building, month, status, keyword, page) => {
        const res = await fetch(`${API_URL}?action=get_all_data&building=${building}&month=${month}&status=${status}&keyword=${encodeURIComponent(keyword)}&page=${page}`);
        return res.json();
    },

    /** Lấy danh sách các phòng đang được thuê theo Tòa nhà */
    getRentedRooms: async (buildingId) => {
        const res = await fetch(`${API_URL}?action=get_rented_rooms&building_id=${buildingId}`);
        return res.json();
    },

    /** Lấy dịch vụ kèm theo của một Hợp đồng */
    getServices: async (contractId) => {
        const res = await fetch(`${API_URL}?action=get_services&contract_id=${contractId}`);
        return res.json();
    },

    /** Lấy chi tiết một Hóa đơn */
    getInvoiceDetails: async (invoiceId) => {
        const res = await fetch(`${API_URL}?action=get_invoice_details&id=${invoiceId}`);
        return res.json();
    },

    /** Gửi form dữ liệu (Thêm mới, Thanh toán, Xóa) */
    submitForm: async (formData) => {
        const res = await fetch(API_URL, { method: 'POST', body: formData });
        return res.json();
    }
};