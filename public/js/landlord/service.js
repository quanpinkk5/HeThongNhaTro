document.addEventListener("DOMContentLoaded", () => {
    console.log("Trang Quản lý Dịch vụ (API Mode) đã tải");
    loadServices();

    // 1. Khởi tạo Modals
    const modalAdd = document.getElementById("modalAddService");
    const modalEdit = document.getElementById("modalEditService");
    const modalDelete = document.getElementById("modalDeleteService");

    const btnAdd = document.getElementById("btnOpenAddService");
    if (btnAdd) btnAdd.onclick = () => modalAdd.style.display = "block";

    document.querySelectorAll(".add-close").forEach(btn => btn.onclick = () => modalAdd.style.display = "none");
    document.querySelectorAll(".edit-close").forEach(btn => btn.onclick = () => modalEdit.style.display = "none");
    document.querySelectorAll(".delete-close").forEach(btn => btn.onclick = () => modalDelete.style.display = "none");

    window.onclick = function(e) {
        if (e.target == modalAdd) modalAdd.style.display = "none";
        if (e.target == modalEdit) modalEdit.style.display = "none";
        if (e.target == modalDelete) modalDelete.style.display = "none";
    }

    // 2. Gắn sự kiện submit Form API
    handleFormSubmit("formAddService", "modalAddService");
    handleFormSubmit("formEditService", "modalEditService");
    handleFormSubmit("formDeleteService", "modalDeleteService");

    // 3. Tìm kiếm nội bộ (Client-side search)
    const searchInput = document.getElementById("searchService");
    if (searchInput) {
        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll("#serviceTableBody tr");
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? "" : "none";
            });
        });
    }
});

// ==== HÀM TIỆN ÍCH ====
function getServiceIcon(name) {
    const n = name.toLowerCase();
    if (n.includes('điện')) return 'fa-bolt';
    if (n.includes('nước')) return 'fa-droplet';
    if (n.includes('net') || n.includes('wifi')) return 'fa-wifi';
    if (n.includes('vệ sinh') || n.includes('rác')) return 'fa-broom';
    if (n.includes('xe')) return 'fa-motorcycle';
    return 'fa-concierge-bell';
}

// ==== GỌI DỮ LIỆU ====
async function loadServices() {
    try {
        const res = await ServiceAPI.getAll();
        if (res.status === 'success') {
            renderTable(res.data);
        } else {
            alert("Lỗi tải dữ liệu: " + res.message);
        }
    } catch (err) {
        console.error("API Error:", err);
    }
}

// ==== RENDER GIAO DIỆN ====
function renderTable(services) {
    const tbody = document.getElementById("serviceTableBody");
    if (!tbody) return;
    tbody.innerHTML = '';

    if (services.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">Chưa có dịch vụ nào.</td></tr>`;
        return;
    }

    services.forEach(s => {
        const icon = getServiceIcon(s.name);
        const isFixed = s.type === 'FIXED';
        const typeClass = isFixed ? 'fixed' : 'variable';
        const typeName = isFixed ? 'Cố định' : 'Theo chỉ số';
        
        // Giới hạn độ dài mô tả
        let shortDesc = s.description || '';
        if (shortDesc.length > 30) shortDesc = shortDesc.substring(0, 30) + "...";

        // Định dạng tiền
        const priceFormatted = parseInt(s.price).toLocaleString('vi-VN');

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong class="svc-code">${s.code}</strong></td>
            <td><span class="service-name"><i class="fa-solid ${icon}"></i> ${s.name}</span></td>
            <td class="price-text">${priceFormatted}</td>
            <td class="svc-unit">${s.unit}</td>
            <td><span class="badge-type ${typeClass}">${typeName}</span></td>
            <td>${shortDesc}</td>
            <td class="text-center">
                <button class="btn-icon btn-edit" title="Sửa" data-item='${JSON.stringify(s).replace(/'/g, "&#39;")}'>
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn-icon btn-delete" title="Xóa" data-id="${s.id}" data-name="${s.name}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    attachRowEvents();
}

// ==== THAO TÁC NGƯỜI DÙNG ====
function attachRowEvents() {
    // Sửa
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.onclick = function() {
            const data = JSON.parse(this.getAttribute('data-item'));
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_code").value = data.code;
            document.getElementById("edit_name").value = data.name;
            document.getElementById("edit_price").value = data.price;
            document.getElementById("edit_unit").value = data.unit;
            document.getElementById("edit_type").value = data.type === 'FIXED' ? 'fixed' : 'variable';
            document.getElementById("edit_description").value = data.description || '';
            document.getElementById("modalEditService").style.display = "block";
        };
    });

    // Xóa
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.onclick = function() {
            document.getElementById("delete_id").value = this.getAttribute('data-id');
            document.getElementById("delete_name_display").innerText = this.getAttribute('data-name');
            document.getElementById("modalDeleteService").style.display = "block";
        };
    });
}

function handleFormSubmit(formId, modalId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const data = await ServiceAPI.submitForm(formData);
            alert(data.message);
            if (data.status === 'success') {
                document.getElementById(modalId).style.display = "none";
                this.reset();
                loadServices(); // Load lại ngay danh sách mới
            }
        } catch (err) {
            console.error("Lỗi:", err);
            alert("Đã xảy ra lỗi kết nối!");
        }
    });
}