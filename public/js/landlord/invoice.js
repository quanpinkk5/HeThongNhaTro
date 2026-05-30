let currentPage = 1;

document.addEventListener("DOMContentLoaded", () => {
    // Set default month cho filter và form add
    const now = new Date();
    const currentMonth = `${now.getFullYear()}-${("0" + (now.getMonth() + 1)).slice(-2)}`;
    document.getElementById("f_month").value = currentMonth;
    document.getElementById("add_month").value = currentMonth;

    loadMainData(currentPage);

    // Event Filters
    document.getElementById("f_building").addEventListener("change", () => loadMainData(1));
    document.getElementById("f_month").addEventListener("change", () => loadMainData(1));
    document.getElementById("f_status").addEventListener("change", () => loadMainData(1));
    document.getElementById("f_keyword").addEventListener("keyup", debounce(() => loadMainData(1), 400));

    // Form Submits
    handleFormSubmit("formAddInvoice", "invoiceModal");
    handleFormSubmit("formConfirmPay", "modalConfirmPay");
    handleFormSubmit("formConfirmDelete", "modalConfirmDelete");

    // Dynamic Select trong Modal Thêm
    const bSelect = document.getElementById("modalBuildingSelect");
    const cSelect = document.getElementById("modalContractSelect");

    bSelect.addEventListener("change", async function() {
        cSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        cSelect.disabled = true;
        resetInfo();

        if (this.value) {
            try {
                const res = await InvoiceAPI.getRentedRooms(this.value);
                cSelect.innerHTML = '<option value="">-- Chọn phòng --</option>';
                if (res.status === 'success' && res.data.length > 0) {
                    cSelect.disabled = false;
                    res.data.forEach(r => {
                        cSelect.innerHTML += `<option value="${r.contract_id}" data-price="${r.price}" data-user="${r.user_name}">${r.room_name} - ${r.user_name}</option>`;
                    });
                } else {
                    cSelect.innerHTML = '<option value="">Không có phòng thuê</option>';
                }
            } catch (error) {
                console.error("Lỗi khi tải danh sách phòng:", error);
                cSelect.innerHTML = '<option value="">Lỗi kết nối</option>';
            }
        }
    });

    cSelect.addEventListener("change", async function() {
        if (this.value) {
            const opt = this.options[this.selectedIndex];
            const price = parseFloat(opt.getAttribute("data-price"));
            document.getElementById("displayUser").innerText = opt.getAttribute("data-user");
            document.getElementById("displayRoomPrice").innerText = formatMoney(price);
            document.getElementById("rawRoomPrice").value = price;
            
            const servicesArea = document.getElementById("servicesArea");
            servicesArea.innerHTML = '<p class="text-center">Đang tải dịch vụ...</p>';
            
            try {
                const res = await InvoiceAPI.getServices(this.value);
                if (res.status === 'success') {
                    renderInputs(res.data);
                    calculateTotal();
                } else {
                    servicesArea.innerHTML = `<p style="color:red">${res.message}</p>`;
                }
            } catch (error) {
                console.error("Lỗi khi tải dịch vụ:", error);
                servicesArea.innerHTML = `<p style="color:red">Lỗi kết nối khi tải dịch vụ</p>`;
            }
        } else resetInfo();
    });

    setupModals();
});

// ==== HÀM TIỆN ÍCH ====
function debounce(func, timeout = 300){
    let timer; 
    return (...args) => { clearTimeout(timer); timer = setTimeout(() => { func.apply(this, args); }, timeout); };
}

function formatMoney(n) { return new Intl.NumberFormat('vi-VN').format(n); }

// ==== HÀM GỌI DỮ LIỆU CHÍNH ====
async function loadMainData(page) {
    currentPage = page;
    const b = document.getElementById("f_building").value;
    const m = document.getElementById("f_month").value;
    const s = document.getElementById("f_status").value;
    const kw = document.getElementById("f_keyword").value;

    try {
        const data = await InvoiceAPI.getAllData(b, m, s, kw, page);
        if(data.status === 'success') {
            renderBuildingFilter(data.buildings);
            renderInvoices(data.invoices);
            renderPagination(data.pagination);
        }
    } catch (error) {
        console.error("Lỗi khi tải dữ liệu chính:", error);
    }
}

// ==== RENDER UI ====
function renderBuildingFilter(buildings) {
    const fb = document.getElementById("f_building");
    const mb = document.getElementById("modalBuildingSelect");
    if(fb.options.length <= 1) {
        buildings.forEach(b => {
            fb.innerHTML += `<option value="${b.id}">${b.name}</option>`;
            mb.innerHTML += `<option value="${b.id}">${b.name}</option>`;
        });
    }
}

function renderInvoices(invoices) {
    const tbody = document.getElementById("invoiceTableBody");
    tbody.innerHTML = '';

    if(invoices.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center">Không có hóa đơn.</td></tr>`;
        return;
    }

    invoices.forEach(i => {
        const isPaid = (i.status === 'PAID');
        const badge = isPaid ? '<span class="badge paid">Đã thu</span>' : '<span class="badge unpaid">Chưa thu</span>';
        
        tbody.innerHTML += `
            <tr class="${isPaid ? 'row-paid' : ''}">
                <td><strong>#${i.id}</strong></td>
                <td>${i.building_name}</td>
                <td><span class="room-tag">${i.room_name}</span></td>
                <td>${i.user_name}</td>
                <td>Tháng ${i.month}/${i.year}</td>
                <td class="money-bold">${formatMoney(i.total)} đ</td>
                <td>${badge}</td>
                <td class="text-center">
                    <button class="btn-icon btn-view" onclick="openView(${i.id})"><i class="fa-solid fa-eye"></i></button>
                    <button class="btn-icon ${isPaid ? 'disabled' : 'btn-pay'}" ${isPaid ? 'disabled' : `onclick="openPay(${i.id})"`}><i class="fa-solid fa-hand-holding-dollar"></i></button>
                    <button class="btn-icon ${isPaid ? 'disabled' : 'btn-delete'}" ${isPaid ? 'disabled' : `onclick="openDel(${i.id})"`}><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
    });
}

function renderPagination(p) {
    const cont = document.getElementById("paginationContainer");
    cont.innerHTML = '';
    if(p.total_pages <= 1) return;

    if(p.current_page > 1) cont.innerHTML += `<a href="#" onclick="loadMainData(${p.current_page - 1}); return false;">« Trước</a>`;
    for(let i=1; i<=p.total_pages; i++) {
        cont.innerHTML += `<a href="#" class="${i === p.current_page ? 'active' : ''}" onclick="loadMainData(${i}); return false;">${i}</a>`;
    }
    if(p.current_page < p.total_pages) cont.innerHTML += `<a href="#" onclick="loadMainData(${p.current_page + 1}); return false;">Sau »</a>`;
}

// ==== THAO TÁC LẬP HÓA ĐƠN ====
function resetInfo() {
    document.getElementById("displayUser").innerText = "...";
    document.getElementById("displayRoomPrice").innerText = "0";
    document.getElementById("rawRoomPrice").value = 0;
    document.getElementById("servicesArea").innerHTML = '<p style="text-align:center; color:#888;">Vui lòng chọn phòng...</p>';
    document.getElementById("finalTotal").innerText = "0";
}

function renderInputs(services) {
    const area = document.getElementById("servicesArea");
    if (services.length === 0) { area.innerHTML = '<p style="color:green; text-align:center">Không có dịch vụ thêm.</p>'; return; }
    
    let html = '';
    services.forEach(s => {
        const isMetered = (s.type === 'METERED');
        const label = isMetered ? `<b>${s.name}</b> (${formatMoney(s.price)}/${s.unit})` : `<b>${s.name}</b> (Cố định: ${formatMoney(s.price)})`;
        const val = isMetered ? '' : '1';
        html += `<div class="svc-row"><div style="flex:2">${label}</div><div style="flex:1"><input type="number" name="svc_qty[${s.id}]" class="svc-input calc-input" value="${val}" placeholder="${isMetered ? 'Chỉ số' : 'SL'}" data-price="${s.price}" min="0" step="0.1" oninput="calculateTotal()"></div></div>`;
    });
    area.innerHTML = html;
}

window.calculateTotal = function() {
    let t = parseFloat(document.getElementById("rawRoomPrice").value) || 0;
    document.querySelectorAll('.calc-input').forEach(inp => { t += (parseFloat(inp.value) || 0) * parseFloat(inp.getAttribute('data-price')); });
    document.getElementById("finalTotal").innerText = formatMoney(t);
}

// ==== QUẢN LÝ CÁC MODAL ====
async function openView(id) {
    const vm = document.getElementById("viewInvoiceModal");
    document.getElementById("viewItemsBody").innerHTML = '<tr><td colspan="4" class="text-center">Đang tải...</td></tr>';
    vm.style.display = "block";

    try {
        const d = await InvoiceAPI.getInvoiceDetails(id);
        if(d.status === 'success') {
            const h = d.data.header;
            document.getElementById("viewInvId").innerText = h.id;
            document.getElementById("viewBuilding").innerText = h.building_name;
            document.getElementById("viewRoom").innerText = h.room_name;
            document.getElementById("viewMonth").innerText = `${h.month}/${h.year}`;
            document.getElementById("viewUser").innerText = h.user_name;
            document.getElementById("viewPhone").innerText = h.phone || 'N/A';
            document.getElementById("viewStatus").innerHTML = h.status === 'PAID' ? '<span class="badge paid">Đã thanh toán</span>' : '<span class="badge unpaid">Chưa thanh toán</span>';
            document.getElementById("viewTotal").innerText = formatMoney(h.total) + ' đ';

            let htm = '';
            d.data.items.forEach(i => htm += `<tr><td>${i.description}</td><td class="text-right">${formatMoney(i.unit_price)}</td><td class="text-center">${i.quantity}</td><td class="text-right">${formatMoney(i.amount)}</td></tr>`);
            document.getElementById("viewItemsBody").innerHTML = htm;
        }
    } catch (error) {
        console.error("Lỗi tải chi tiết hóa đơn:", error);
    }
}

function openPay(id) {
    document.getElementById("confPayDisplayId").innerText = id;
    document.getElementById("confPayInputId").value = id;
    document.getElementById("modalConfirmPay").style.display = "block";
}

function openDel(id) {
    document.getElementById("confDeleteDisplayId").innerText = id;
    document.getElementById("confDeleteInputId").value = id;
    document.getElementById("modalConfirmDelete").style.display = "block";
}

function handleFormSubmit(formId, modalId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        try {
            const data = await InvoiceAPI.submitForm(new FormData(this));
            alert(data.message);
            if(data.status === 'success') {
                document.getElementById(modalId).style.display = "none";
                this.reset();
                if(formId === 'formAddInvoice') resetInfo();
                loadMainData(currentPage);
            }
        } catch (error) {
            console.error("Lỗi khi gửi form:", error);
            alert("Có lỗi xảy ra khi kết nối máy chủ.");
        }
    });
}

function setupModals() {
    const mAdd = document.getElementById("invoiceModal");
    document.getElementById("btnOpenModal").onclick = () => mAdd.style.display = "block";
    document.querySelectorAll(".close-btn, .close-btn-btn").forEach(c => c.onclick = () => mAdd.style.display = "none");
    document.querySelectorAll(".close-view-btn").forEach(c => c.onclick = () => document.getElementById("viewInvoiceModal").style.display = "none");
    document.querySelectorAll(".close-pay-btn").forEach(c => c.onclick = () => document.getElementById("modalConfirmPay").style.display = "none");
    document.querySelectorAll(".close-delete-btn").forEach(c => c.onclick = () => document.getElementById("modalConfirmDelete").style.display = "none");
    
    window.onclick = (e) => {
        if(e.target == mAdd) mAdd.style.display = "none";
        if(e.target == document.getElementById("viewInvoiceModal")) document.getElementById("viewInvoiceModal").style.display = "none";
        if(e.target == document.getElementById("modalConfirmPay")) document.getElementById("modalConfirmPay").style.display = "none";
        if(e.target == document.getElementById("modalConfirmDelete")) document.getElementById("modalConfirmDelete").style.display = "none";
    }
}