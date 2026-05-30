let requestDataList = []; // Lưu trữ cache list

document.addEventListener("DOMContentLoaded", () => {
    loadRequests();

    // Bắt sự kiện Lọc và Tìm kiếm gọi API lại ngay lập tức
    document.getElementById("filterKeyword").addEventListener("keyup", debounce(loadRequests, 300));
    document.getElementById("filterStatus").addEventListener("change", loadRequests);
});

// ==== HÀM TIỆN ÍCH ====
function debounce(func, timeout = 300){
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

// ==== GỌI DỮ LIỆU CHÍNH ====
async function loadRequests() {
    const keyword = document.getElementById("filterKeyword").value;
    const status = document.getElementById("filterStatus").value;

    try {
        const res = await RentRequestAPI.getAll(keyword, status);
        if (res.status === 'success') {
            requestDataList = res.data;
            renderTable(res.data);
        } else {
            console.error("Lỗi từ server:", res.message);
        }
    } catch (err) {
        console.error("Lỗi khi kết nối đến API lấy danh sách yêu cầu:", err);
    }
}

// ==== RENDER GIAO DIỆN ====
function renderTable(data) {
    const tbody = document.getElementById("requestTableBody");
    document.getElementById("requestCount").innerText = data.length;
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px;">Không tìm thấy yêu cầu nào.</td></tr>`;
        clearDetail();
        return;
    }

    data.forEach((r, index) => {
        let badgeClass = 'badge-pending';
        let statusText = 'Chờ duyệt';
        if (r.status === 'APPROVED') { badgeClass = 'badge-approved'; statusText = 'Đã duyệt'; }
        if (r.status === 'REJECTED') { badgeClass = 'badge-cancelled'; statusText = 'Đã hủy'; }

        // Format Date
        const dateObj = new Date(r.created_at);
        const dayMonth = `${("0"+dateObj.getDate()).slice(-2)}/${("0"+(dateObj.getMonth()+1)).slice(-2)}`;
        const fullDate = `${dayMonth}/${dateObj.getFullYear()} ${("0"+dateObj.getHours()).slice(-2)}:${("0"+dateObj.getMinutes()).slice(-2)}`;

        const tr = document.createElement("tr");
        if(index === 0) tr.classList.add("active-row");
        
        tr.innerHTML = `
            <td>${dayMonth}</td>
            <td><strong>${r.full_name}</strong><br><span style="font-size: 11px; color: #777;">${r.phone}</span></td>
            <td>${r.room_name}</td>
            <td><span class="${badgeClass}">${statusText}</span></td>
        `;

        // Gắn Data vào JSON để khi Click lấy ra hiển thị
        const safeData = { ...r, statusText, badgeClass, fullDate };
        tr.onclick = () => selectRow(tr, safeData);
        
        tbody.appendChild(tr);

        // Tự động load chi tiết dòng đầu tiên
        if(index === 0) selectRow(tr, safeData);
    });
}

// ==== THAO TÁC NGƯỜI DÙNG ====
function selectRow(rowElement, data) {
    document.querySelectorAll('.active-row').forEach(el => el.classList.remove('active-row'));
    rowElement.classList.add('active-row');

    document.getElementById('d_title').innerText = "Chi tiết yêu cầu: #" + data.id;
    
    const badge = document.getElementById('d_status_badge');
    badge.className = "status-tag " + data.badgeClass;
    badge.innerText = data.statusText;
    badge.style.display = 'inline-block';

    document.getElementById('d_name').innerText = data.full_name;
    document.getElementById('d_phone').innerText = data.phone;
    document.getElementById('d_email').innerText = data.email || 'N/A';
    document.getElementById('d_cccd').innerText = data.cccd || 'N/A';
    document.getElementById('d_building').innerText = data.building_name;
    document.getElementById('d_room').innerText = data.room_name;
    document.getElementById('d_price').innerText = parseInt(data.price).toLocaleString('vi-VN') + " đ";
    document.getElementById('d_date').innerText = data.fullDate;
    document.getElementById('d_note').innerText = data.note || "(Không có ghi chú)";

    document.getElementById('formRequestId').value = data.id;

    // Toggle Buttons
    const actionArea = document.getElementById('action_buttons');
    actionArea.style.display = (data.status === 'PENDING') ? 'flex' : 'none';

    document.getElementById('detail_content').style.display = 'block';
    document.getElementById('no_data_msg').style.display = 'none';
}

function clearDetail() {
    document.getElementById('detail_content').style.display = 'none';
    document.getElementById('action_buttons').style.display = 'none';
    document.getElementById('no_data_msg').style.display = 'block';
    document.getElementById('d_title').innerText = "Chi tiết yêu cầu";
    document.getElementById('d_status_badge').style.display = 'none';
}

function submitApprove() {
    if(!confirm("Xác nhận DUYỆT yêu cầu này?")) return;
    const req_id = document.getElementById('formRequestId').value;
    postAction('approve', req_id, '');
}

function submitReject() {
    let reason = prompt("Vui lòng nhập lý do từ chối/hủy:", "Thông tin không hợp lệ");
    if(reason === null) return; // Bấm Cancel
    
    const req_id = document.getElementById('formRequestId').value;
    postAction('reject', req_id, reason);
}

async function postAction(action, reqId, reason) {
    try {
        const data = await RentRequestAPI.submitAction(action, reqId, reason);
        alert(data.message);
        if(data.status === 'success') {
            loadRequests(); // Tải lại bảng để cập nhật màu Trạng thái lập tức
        }
    } catch (err) {
        console.error("API Error:", err);
        alert("Lỗi kết nối máy chủ.");
    }
}