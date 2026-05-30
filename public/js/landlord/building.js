let currentPage = 1;

document.addEventListener("DOMContentLoaded", () => {
    loadBuildings(currentPage);

    // Xử lý bật tắt Modal
    const modalAdd = document.getElementById("modalAddBuilding");
    const modalEdit = document.getElementById("modalEditBuilding");
    const modalDelete = document.getElementById("modalDeleteBuilding");

    const btnOpenAdd = document.getElementById("btnOpenModal");
    if(btnOpenAdd) btnOpenAdd.onclick = () => modalAdd.style.display = "block";

    document.querySelectorAll(".add-close").forEach(btn => btn.onclick = () => modalAdd.style.display = "none");
    document.querySelectorAll(".edit-close").forEach(btn => btn.onclick = () => modalEdit.style.display = "none");
    document.querySelectorAll(".delete-close").forEach(btn => btn.onclick = () => modalDelete.style.display = "none");

    window.onclick = function(e) {
        if (e.target == modalAdd) modalAdd.style.display = "none";
        if (e.target == modalEdit) modalEdit.style.display = "none";
        if (e.target == modalDelete) modalDelete.style.display = "none";
    }

    // Tìm kiếm (Client Side)
    const searchInput = document.getElementById("searchInput");
    if(searchInput) {
        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll("#buildingTableBody tr");
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? "" : "none";
            });
        });
    }

    // Submit Forms
    handleFormSubmit("formAddBuilding", "modalAddBuilding");
    handleFormSubmit("formEditBuilding", "modalEditBuilding");
    handleFormSubmit("formDeleteBuilding", "modalDeleteBuilding");
});

// ==== HÀM GỌI DỮ LIỆU ====
async function loadBuildings(page) {
    try {
        const res = await BuildingAPI.getAll(page);
        if (res.status === 'success') {
            renderTable(res.data);
            renderPagination(res.pagination);
        }
    } catch (err) {
        console.error("API Error:", err);
    }
}

// ==== RENDER UI ====
function renderTable(buildings) {
    const tbody = document.getElementById("buildingTableBody");
    tbody.innerHTML = '';

    if (buildings.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center" style="padding: 20px;">Chưa có dữ liệu tòa nhà.</td></tr>`;
        return;
    }

    buildings.forEach(b => {
        const empty = b.total_rooms - b.rented_rooms;
        const badge = empty > 0 ? `<span class="badge-red">${empty}</span>` : `<span class="badge-green">0</span>`;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${b.code}</strong></td>
            <td>${b.name}</td>
            <td>${b.address}</td>
            <td class="text-center">${b.floors}</td>
            <td class="text-center">${b.total_rooms}</td>
            <td class="text-center">${badge}</td>
            <td class="text-center">
                <button class="btn-icon btn-edit" title="Sửa" data-item='${JSON.stringify(b).replace(/'/g, "&#39;")}'>
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn-icon btn-delete" title="Xóa" data-id="${b.id}" data-name="${b.name}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    attachRowEvents();
}

function renderPagination(pageInfo) {
    const container = document.getElementById("paginationContainer");
    container.innerHTML = '';
    
    if (pageInfo.total_pages <= 1) return;

    if (pageInfo.current_page > 1) {
        container.innerHTML += `<a href="#" onclick="changePage(${pageInfo.current_page - 1}); return false;">« Trước</a>`;
    }

    for (let i = 1; i <= pageInfo.total_pages; i++) {
        const activeClass = (i === pageInfo.current_page) ? 'active' : '';
        container.innerHTML += `<a href="#" class="${activeClass}" onclick="changePage(${i}); return false;">${i}</a>`;
    }

    if (pageInfo.current_page < pageInfo.total_pages) {
        container.innerHTML += `<a href="#" onclick="changePage(${pageInfo.current_page + 1}); return false;">Sau »</a>`;
    }
}

function changePage(page) {
    currentPage = page;
    loadBuildings(page);
}

// ==== XỬ LÝ SỰ KIỆN GIAO DIỆN & SUBMIT ====
function attachRowEvents() {
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.onclick = function() {
            const d = JSON.parse(this.getAttribute("data-item"));
            document.getElementById("edit_id").value = d.id;
            document.getElementById("edit_code").value = d.code;
            document.getElementById("edit_name").value = d.name;
            document.getElementById("edit_address").value = d.address;
            document.getElementById("edit_floors").value = d.floors;
            document.getElementById("edit_rooms").value = d.total_rooms;
            
            // Ẩn thông báo lỗi cũ nếu có
            const errorMsg = document.querySelector("#formEditBuilding .error-message");
            if (errorMsg) errorMsg.style.display = "none";
            document.getElementById("modalEditBuilding").style.display = "block";
        }
    });

    document.querySelectorAll("#buildingTableBody .btn-delete").forEach(btn => {
        btn.onclick = function() {
            document.getElementById("delete_id").value = this.getAttribute("data-id");
            document.getElementById("delete_name_display").innerText = this.getAttribute("data-name");
            
            const errorMsg = document.querySelector("#formDeleteBuilding .error-message");
            if (errorMsg) errorMsg.style.display = "none";

            document.getElementById("modalDeleteBuilding").style.display = "block";
        }
    });
}

function handleFormSubmit(formId, modalId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        // Lấy thẻ hiển thị lỗi trong form (nếu có)
        const errorMsg = this.querySelector(".error-message");
        if (errorMsg) errorMsg.style.display = "none";

        try {
            const data = await BuildingAPI.submitForm(new FormData(this));
            
            if (data.status === 'success') {
                // Thành công: Ẩn modal, reset form, reload dữ liệu
                document.getElementById(modalId).style.display = "none";
                this.reset();
                loadBuildings(currentPage);
            } else {
                // BẮT LỖI TẠI ĐÂY: Ví dụ lỗi "đang chứa phòng trọ"
                if (errorMsg) {
                    errorMsg.innerText = "❌ " + data.message;
                    errorMsg.style.display = "block";
                } else {
                    alert(data.message);
                }
            }
        } catch (err) {
            console.error(err);
            if (errorMsg) {
                errorMsg.innerText = "❌ Lỗi kết nối API. Vui lòng thử lại.";
                errorMsg.style.display = "block";
            } else {
                alert("Lỗi kết nối API.");
            }
        }
    });
}