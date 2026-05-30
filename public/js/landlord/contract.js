document.addEventListener("DOMContentLoaded", () => {
  loadMainData();

  // Lắng nghe bộ lọc thay đổi
  document
    .getElementById("f_building")
    .addEventListener("change", loadMainData);
  document.getElementById("f_status").addEventListener("change", loadMainData);
  document
    .getElementById("f_keyword")
    .addEventListener("keyup", debounce(loadMainData, 400));

  // Form Submits
  handleFormSubmit("formAddContract", "modalAddContract");
  handleFormSubmit("formRenewContract", "modalRenewContract");
  handleFormSubmit("formCancelContract", "modalCancelContract");

  // Ajax Load Phòng khi đổi Tòa (Modal Thêm)
  document
    .getElementById("modal_building_select")
    .addEventListener("change", async function () {
      const rSelect = document.getElementById("modal_room_select");
      rSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
      rSelect.disabled = true;

      if (this.value) {
        try {
          const resp = await ContractAPI.getValidRooms(this.value);
          if (resp.status === "success" && resp.data.length > 0) {
            rSelect.disabled = false;
            rSelect.innerHTML = '<option value="">-- Chọn phòng --</option>';
            resp.data.forEach((r) => {
              let price = new Intl.NumberFormat("vi-VN").format(r.price);
              rSelect.innerHTML += `<option value="${r.id}">${r.title} - ${price} đ</option>`;
            });
          } else {
            rSelect.innerHTML = '<option value="">Hết phòng trống</option>';
          }
        } catch (error) {
          console.error("Lỗi khi tải danh sách phòng:", error);
          rSelect.innerHTML = '<option value="">Lỗi kết nối</option>';
        }
      }
    });

  // Bật tắt Modals
  setupModals();
});

// Utilities
function debounce(func, timeout = 300) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      func.apply(this, args);
    }, timeout);
  };
}

function calcEndDate() {
  const start = document.getElementById("startDate").value;
  const dur = parseInt(document.getElementById("duration").value);
  if (start) {
    let d = new Date(start);
    d.setMonth(d.getMonth() + dur);
    document.getElementById("endDate").value = d.toISOString().split("T")[0];
  }
}

// Gọi API lấy toàn bộ dữ liệu trang
async function loadMainData() {
  const build = document.getElementById("f_building").value;
  const status = document.getElementById("f_status").value;
  const kw = document.getElementById("f_keyword").value;

  try {
    const data = await ContractAPI.getAllData(build, status, kw);
    if (data.status === "success") {
      renderFilters(data.buildings);
      renderTenants(data.tenants);
      renderContracts(data.contracts);
      renderRequests(data.requests);
    }
  } catch (error) {
    console.error("Lỗi khi tải dữ liệu:", error);
  }
}

// UI Renders
function renderFilters(buildings) {
  const fb = document.getElementById("f_building");
  const mb = document.getElementById("modal_building_select");

  // Chỉ render list nếu chưa có (tránh reset filter đang chọn)
  if (fb.options.length <= 1) {
    buildings.forEach((b) => {
      fb.innerHTML += `<option value="${b.id}">${b.name}</option>`;
      mb.innerHTML += `<option value="${b.id}">${b.name}</option>`;
    });
  }
}

function renderTenants(tenants) {
  const sel = document.getElementById("modal_tenant_select");
  sel.innerHTML = '<option value="">-- Chọn khách thuê --</option>';
  tenants.forEach(
    (t) =>
      (sel.innerHTML += `<option value="${t.id}">${t.name} (${t.cccd})</option>`),
  );
}

function renderContracts(contracts) {
  const tbody = document.getElementById("contractTableBody");
  tbody.innerHTML = "";
  const today = new Date().toISOString().split("T")[0];

  if (contracts.length === 0) {
    tbody.innerHTML = `<tr><td colspan="8" class="text-center">Không tìm thấy hợp đồng.</td></tr>`;
    return;
  }

  contracts.forEach((c) => {
    let stClass = "active",
      stText = "Còn hiệu lực",
      isEnded = false;

    if (c.status === "ENDED") {
      stClass = "ended";
      stText = "Đã thanh lý";
      isEnded = true;
    } else if (c.end_date < today) {
      stClass = "expired";
      stText = "Quá hạn";
      isEnded = true;
    } else {
      let dEnd = new Date(c.end_date);
      let dWarn = new Date();
      dWarn.setDate(dWarn.getDate() + 30);
      if (dEnd <= dWarn) {
        stClass = "warning";
        stText = "Sắp hết hạn";
      }
    }

    const tr = document.createElement("tr");
    const safeData = JSON.stringify(c).replace(/'/g, "&#39;");

    tr.innerHTML = `
            <td><strong>#${c.id}</strong></td>
            <td>${c.building_name}</td>
            <td><span class="room-tag">${c.room_name}</span></td>
            <td>${c.user_name}</td>
            <td>${c.start_date.split("-").reverse().join("/")}</td>
            <td>${c.end_date.split("-").reverse().join("/")}</td>
            <td><span class="status-badge ${stClass}">${stText}</span></td>
            <td class="text-center">
                <button class="btn-icon btn-view" onclick='openView(${safeData}, "${stClass}", "${stText}")'><i class="fa-regular fa-eye"></i></button>
                <button class="btn-icon btn-renew" onclick='openRenew(${c.id})'><i class="fa-solid fa-clock-rotate-left"></i></button>
                <button class="btn-icon btn-cancel-contract" ${isEnded ? 'disabled style="opacity:0.3"' : `onclick="openCancel(${c.id})"`}><i class="fa-solid fa-power-off" style="color:${isEnded ? "grey" : "#d32f2f"}"></i></button>
            </td>
        `;
    tbody.appendChild(tr);
  });
}

function renderRequests(requests) {
  const btn = document.getElementById("btnOpenRenewalRequests");
  const count = document.getElementById("req_count");
  const tbody = document.getElementById("renewalTableBody");
  tbody.innerHTML = "";

  if (requests.length > 0) {
    btn.style.display = "inline-block";
    count.innerText = requests.length;
    requests.forEach((r) => {
      tbody.innerHTML += `
                <tr>
                    <td><strong>${r.room_name}</strong></td>
                    <td>${r.user_name}</td>
                    <td style="color:#28a745; font-weight:bold;">+${r.months} Tháng</td>
                    <td>${r.note || ""}</td>
                    <td class="text-center">
                        <button class="btn-approve-req" onclick="submitReq('approve_request', ${r.id})" style="margin-right:5px;"><i class="fa-solid fa-check"></i> Duyệt</button>
                        <button class="btn-reject-req" onclick="submitReq('reject_request', ${r.id})"><i class="fa-solid fa-xmark"></i> Từ chối</button>
                    </td>
                </tr>`;
    });
  } else {
    btn.style.display = "none";
    tbody.innerHTML = `<tr><td colspan="5" class="text-center">Không có yêu cầu nào.</td></tr>`;
  }
}

// Modal Actions
function openView(d, stClass, stText) {
  document.getElementById("v_code").innerText = "HĐ-" + d.id;
  document.getElementById("v_build").innerText = d.building_name;
  document.getElementById("v_room").innerText = d.room_name;
  document.getElementById("v_cus").innerText = d.user_name;
  document.getElementById("v_start").innerText = d.start_date;
  document.getElementById("v_end").innerText = d.end_date;
  document.getElementById("v_status").innerHTML =
    `<span class="status-badge ${stClass}">${stText}</span>`;
  document.getElementById("v_cccd").innerText = d.cccd;
  document.getElementById("v_phone").innerText = d.phone;
  document.getElementById("v_email").innerText = d.email || "";
  document.getElementById("v_addr").innerText = d.building_address;
  document.getElementById("modalViewContract").style.display = "block";
}

function openRenew(id) {
  document.getElementById("renew_id_input").value = id;
  document.getElementById("renew_code").innerText = "HĐ-" + id;
  document.getElementById("modalRenewContract").style.display = "block";
}

function openCancel(id) {
  document.getElementById("cancel_id").value = id;
  document.getElementById("cancel_code_display").innerText = "HĐ-" + id;
  document.getElementById("modalCancelContract").style.display = "block";
}

// Xử lý Gửi Form và Yêu cầu
function handleFormSubmit(formId, modalId) {
  const form = document.getElementById(formId);
  if (!form) return;
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    try {
      const data = await ContractAPI.submitForm(new FormData(this));
      alert(data.message);
      if (data.status === "success") {
        document.getElementById(modalId).style.display = "none";
        this.reset();
        loadMainData();
      }
    } catch (error) {
      console.error("Lỗi submit form:", error);
      alert("Có lỗi xảy ra khi thực hiện yêu cầu.");
    }
  });
}

async function submitReq(action, id) {
  if (!confirm("Xác nhận hành động này?")) return;
  try {
    const data = await ContractAPI.submitRequest(action, id);
    alert(data.message);
    loadMainData();
  } catch (error) {
    console.error("Lỗi gửi yêu cầu duyệt/từ chối:", error);
  }
}

function setupModals() {
  const btnAdd = document.getElementById("btnOpenAddContract");
  const mAdd = document.getElementById("modalAddContract");
  if (btnAdd)
    btnAdd.onclick = () => {
      mAdd.style.display = "block";
      document.getElementById("startDate").valueAsDate = new Date();
      calcEndDate();
    };

  const btnReq = document.getElementById("btnOpenRenewalRequests");
  const mReq = document.getElementById("modalRenewalRequests");
  if (btnReq) btnReq.onclick = () => (mReq.style.display = "block");

  document
    .querySelectorAll(".add-close")
    .forEach((c) => (c.onclick = () => (mAdd.style.display = "none")));
  document
    .querySelectorAll(".req-close")
    .forEach((c) => (c.onclick = () => (mReq.style.display = "none")));
  document
    .querySelectorAll(".view-close")
    .forEach(
      (c) =>
        (c.onclick = () =>
          (document.getElementById("modalViewContract").style.display =
            "none")),
    );
  document
    .querySelectorAll(".renew-close")
    .forEach(
      (c) =>
        (c.onclick = () =>
          (document.getElementById("modalRenewContract").style.display =
            "none")),
    );
  document
    .querySelectorAll(".cancel-close")
    .forEach(
      (c) =>
        (c.onclick = () =>
          (document.getElementById("modalCancelContract").style.display =
            "none")),
    );
}
