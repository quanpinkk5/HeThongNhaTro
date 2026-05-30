let currentPage = 1;
const limit = 8;

document.addEventListener("DOMContentLoaded", () => {
    loadCustomers(currentPage);

    // Xử lý đóng Modal
    const modalView = document.getElementById("modalViewCustomer");
    document.querySelectorAll(".view-close").forEach(btn => btn.onclick = () => modalView.style.display = "none");
    window.onclick = (e) => { if (e.target == modalView) modalView.style.display = "none"; }

    // Tìm kiếm (Gọi API thay vì filter HTML để đúng với phân trang)
    const searchInput = document.getElementById("searchInput");
    if(searchInput) {
        searchInput.addEventListener("keyup", debounce(function() {
            currentPage = 1; // Reset về trang 1 khi tìm kiếm
            loadCustomers(currentPage, this.value);
        }, 400)); // Delay 400ms để tránh spam API
    }
});

// ==== HÀM TIỆN ÍCH ====
function debounce(func, timeout = 300){
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

// ==== GỌI DỮ LIỆU ====
async function loadCustomers(page, keyword = '') {
    try {
        const res = await TenantAPI.getList(page, limit, keyword);
        if (res.status === 'success') {
            renderTable(res.data);
            renderPagination(res.pagination, keyword);
        } else {
            alert("Lỗi tải dữ liệu: " + (res.message || "Không xác định"));
        }
    } catch (err) {
        console.error("Lỗi khi kết nối đến API khách thuê:", err);
    }
}

// ==== RENDER GIAO DIỆN ====
function renderTable(data) {
    const tbody = document.getElementById("customerTableBody");
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center" style="padding: 20px;">Không tìm thấy khách thuê.</td></tr>`;
        return;
    }

    data.forEach(u => {
        const firstLetter = u.name.charAt(0).toUpperCase();
        
        // Format Date
        const dateObj = new Date(u.approved_at);
        const dateStr = `${("0"+dateObj.getDate()).slice(-2)}/${("0"+(dateObj.getMonth()+1)).slice(-2)}/${dateObj.getFullYear()}`;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${u.code}</strong></td>
            <td>
                <div class="user-cell">
                    <div class="user-avatar-small">${firstLetter}</div>
                    <span class="user-name">${u.name}</span>
                </div>
            </td>
            <td class="user-cccd">${u.cccd}</td>
            <td class="user-phone">${u.phone}</td>
            <td class="user-email">${u.email || ''}</td>
            <td class="text-center">
                <button class="btn-icon btn-view" title="Xem chi tiết" 
                    onclick="openViewModal('${u.code}', '${u.name}', '${u.cccd}', '${u.phone}', '${u.email}', '${dateStr}')">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(pageInfo, keyword) {
    const container = document.getElementById("paginationContainer");
    container.innerHTML = '';
    
    if (pageInfo.total_pages <= 1) return;

    if (pageInfo.current_page > 1) {
        container.innerHTML += `<a href="#" onclick="changePage(${pageInfo.current_page - 1}, '${keyword}'); return false;">« Trước</a>`;
    }

    for (let i = 1; i <= pageInfo.total_pages; i++) {
        const activeClass = (i === pageInfo.current_page) ? 'active' : '';
        container.innerHTML += `<a href="#" class="${activeClass}" onclick="changePage(${i}, '${keyword}'); return false;">${i}</a>`;
    }

    if (pageInfo.current_page < pageInfo.total_pages) {
        container.innerHTML += `<a href="#" onclick="changePage(${pageInfo.current_page + 1}, '${keyword}'); return false;">Sau »</a>`;
    }
}

// ==== THAO TÁC NGƯỜI DÙNG ====
function changePage(page, keyword) {
    currentPage = page;
    loadCustomers(page, keyword);
}

function openViewModal(code, name, cccd, phone, email, created) {
    document.getElementById("view_code").value = code;
    document.getElementById("view_name").value = name;
    document.getElementById("view_cccd").value = cccd;
    document.getElementById("view_phone").value = phone;
    document.getElementById("view_email").value = email !== 'null' ? email : '';
    document.getElementById("view_created").value = created;
    document.getElementById("modalViewCustomer").style.display = "block";
}