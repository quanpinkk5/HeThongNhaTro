const BASE_IMG_URL = "/public/images";

function buildImageUrl(fileName) {
  if (!fileName) return "";
  const cleaned = String(fileName).replace(/^\/+/, "");
  return `${BASE_IMG_URL}/${cleaned}`;
}

document.addEventListener("DOMContentLoaded", () => {
  loadRoomsData();

  // Khởi tạo Event Modals
  const modalAdd = document.getElementById("modalAddRoom");
  const modalEdit = document.getElementById("modalEditRoom");
  const modalDelete = document.getElementById("modalDeleteRoom");

  document.getElementById("btnOpenAddRoom").onclick = () => {
    document.getElementById("formAddRoom").reset();
    modalAdd.style.display = "block";
  };

  document
    .querySelectorAll(".add-close")
    .forEach((b) => (b.onclick = () => (modalAdd.style.display = "none")));
  document
    .querySelectorAll(".edit-close")
    .forEach((b) => (b.onclick = () => (modalEdit.style.display = "none")));
  document
    .querySelectorAll(".delete-close")
    .forEach((b) => (b.onclick = () => (modalDelete.style.display = "none")));

  window.onclick = (e) => {
    if (e.target == modalAdd) modalAdd.style.display = "none";
    if (e.target == modalEdit) modalEdit.style.display = "none";
    if (e.target == modalDelete) modalDelete.style.display = "none";
  };

  // Tìm kiếm nội bộ
  const searchInput = document.getElementById("searchRoom");
  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      const val = this.value.toLowerCase();
      document.querySelectorAll(".room-card").forEach((card) => {
        const name = card.getAttribute("data-name");
        card.style.display = name.indexOf(val) > -1 ? "" : "none";
      });
    });
  }

  // Submit Forms
  handleFormSubmit("formAddRoom", "modalAddRoom");
  handleFormSubmit("formEditRoom", "modalEditRoom");
  handleFormSubmit("formDeleteRoom", "modalDeleteRoom");
});

// ==== HÀM GỌI DỮ LIỆU ====
async function loadRoomsData() {
  try {
    const res = await RoomAPI.getAll();
    if (res.status === "success") {
      renderSelectAndCheckboxes(res.buildings, res.services);
      renderRooms(res.rooms);
    }
  } catch (err) {
    console.error("Lỗi khi tải dữ liệu phòng:", err);
  }
}

// ==== RENDER UI ====
function renderSelectAndCheckboxes(buildings, services) {
  // Tòa nhà
  const bSelect = document.getElementById("modalAddBuildingSelect");
  bSelect.innerHTML = "";
  buildings.forEach(
    (b) => (bSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`),
  );

  // Dịch vụ Add
  const sAdd = document.getElementById("modalAddServicesContainer");
  sAdd.innerHTML = "";
  services.forEach(
    (s) =>
      (sAdd.innerHTML += `<label class="checkbox-item"><input type="checkbox" name="services[]" value="${s.id}"><span>${s.name}</span></label>`),
  );

  // Dịch vụ Edit
  const sEdit = document.getElementById("modalEditServicesContainer");
  sEdit.innerHTML = "";
  services.forEach(
    (s) =>
      (sEdit.innerHTML += `<label class="checkbox-item"><input type="checkbox" name="services[]" value="${s.id}" id="edit_svc_${s.id}" class="edit-svc-checkbox"><span>${s.name}</span></label>`),
  );
}

function renderRooms(rooms) {
  const grid = document.getElementById("roomGridBody");
  grid.innerHTML = "";

  if (rooms.length === 0) {
    grid.innerHTML =
      '<p style="grid-column: 1/-1; text-align: center; color: #777;">Chưa có phòng trọ nào.</p>';
    return;
  }

  rooms.forEach((r) => {
    const imgSrc = r.thumbnail
      ? buildImageUrl(r.thumbnail)
      : "https://via.placeholder.com/300x200?text=No+Image";
    const priceFmt = parseInt(r.price).toLocaleString("vi-VN");

    // 1. Trạng thái phòng
    const stRmClass = r.status_room === "RENTED" ? "rented" : "empty";
    const stRmText = r.status_room === "RENTED" ? "Đã cho thuê" : "Phòng trống";

    // 2. Trạng thái phê duyệt
    let stAppClass = "",
      stAppText = "";
    if (r.status === "PENDING") {
      stAppClass = "pending";
      stAppText = "Chờ duyệt";
    } else if (r.status === "APPROVED") {
      stAppClass = "approved";
      stAppText = "Đã duyệt";
    } else if (r.status === "REJECTED") {
      stAppClass = "rejected";
      stAppText = "Từ chối";
    }

    // 3. Nút Đăng tin
    let postBtn = "";
    if (r.status === "APPROVED") {
      postBtn = `<button class="btn-card disabled" title="Đã đăng"><i class="fa-solid fa-check-circle" style="color: #28a745;"></i> Đã đăng</button>`;
    } else if (r.status === "PENDING") {
      postBtn = `<button class="btn-card disabled" title="Chờ duyệt"><i class="fa-solid fa-clock" style="color: #f39c12;"></i> Chờ duyệt</button>`;
    } else {
      if (r.status_room === "EMPTY") {
        postBtn = `<button class="btn-card btn-post" onclick="submitRequestPost(${r.id})"><i class="fa-solid fa-upload"></i> Đăng tin</button>`;
      } else {
        postBtn = `<button class="btn-card disabled" title="Đang thuê không thể đăng"><i class="fa-solid fa-ban"></i> Đăng tin</button>`;
      }
    }

    const safeData = JSON.stringify(r).replace(/'/g, "&#39;");

    const card = document.createElement("div");
    card.className = "room-card";
    card.setAttribute("data-name", r.title.toLowerCase());

    card.innerHTML = `
            <div class="room-image">
                <img src="${imgSrc}" alt="Ảnh phòng">
                <div class="badges-container">
                    <span class="status-badge ${stRmClass}">${stRmText}</span>
                    <span class="approve-badge ${stAppClass}">${stAppText}</span>
                </div>
            </div>
            <div class="room-info">
                <h3 class="room-name">${r.title} <small>(${r.building_name})</small></h3>
                <p class="room-address"><i class="fa-solid fa-location-dot"></i> ${r.building_address}</p>
                <p class="room-price">${priceFmt} đ <span>/ ${r.area} m²</span></p>
                <div class="room-services-tag"><i class="fa-solid fa-tags"></i> ${r.service_details || "Không có dịch vụ"}</div>
            </div>
            <div class="card-actions">
                ${postBtn}
                <button class="btn-card btn-edit" onclick='openEditModal(${safeData}, "${imgSrc}")'><i class="fa-solid fa-pen"></i> Sửa</button>
                <button class="btn-card ${r.status_room === "EMPTY" ? "btn-delete" : "disabled"}" ${r.status_room === "EMPTY" ? `onclick="openDeleteModal(${r.id}, '${r.title}')"` : ""}><i class="fa-solid fa-trash"></i> Xóa</button>
            </div>
        `;
    grid.appendChild(card);
  });
}

// ==== QUẢN LÝ MODALS CÁC CHỨC NĂNG ====
function openEditModal(d, imgSrc) {
  document.getElementById("edit_id").value = d.id;
  document.getElementById("edit_title").value = d.title;
  document.getElementById("edit_price").value = d.price;
  document.getElementById("edit_area").value = d.area;
  document.getElementById("edit_desc").value = d.description || "";

  const imgPreview = document.getElementById("preview_img");
  if (imgSrc && !imgSrc.includes("No+Image")) {
    imgPreview.src = imgSrc;
    imgPreview.style.display = "block";
  } else {
    imgPreview.style.display = "none";
  }

  document
    .querySelectorAll(".edit-svc-checkbox")
    .forEach((cb) => (cb.checked = false));
  if (d.service_ids) {
    d.service_ids.split(",").forEach((svcId) => {
      const cb = document.getElementById("edit_svc_" + svcId);
      if (cb) cb.checked = true;
    });
  }
  document.getElementById("modalEditRoom").style.display = "block";
}

function openDeleteModal(id, title) {
  document.getElementById("delete_id").value = id;
  document.getElementById("delete_room_name").innerText = title;
  document.getElementById("modalDeleteRoom").style.display = "block";
}

function handleFormSubmit(formId, modalId) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    try {
      const data = await RoomAPI.submitForm(new FormData(this));
      alert(data.message);
      if (data.status === "success") {
        document.getElementById(modalId).style.display = "none";
        this.reset();
        loadRoomsData();
      }
    } catch (error) {
      console.error("Lỗi khi gửi form:", error);
      alert("Đã xảy ra lỗi kết nối với máy chủ.");
    }
  });
}

window.submitRequestPost = async function (id) {
  if (confirm("Bạn có chắc chắn muốn gửi yêu cầu đăng tin phòng này không?")) {
    try {
      const data = await RoomAPI.requestPost(id);
      alert(data.message);
      loadRoomsData();
    } catch (error) {
      console.error("Lỗi khi gửi yêu cầu đăng tin:", error);
      alert("Đã xảy ra lỗi kết nối với máy chủ.");
    }
  }
};
