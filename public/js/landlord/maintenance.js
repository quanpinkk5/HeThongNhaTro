document.addEventListener("DOMContentLoaded", () => {
  loadMainData();

  // Tìm kiếm
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      const value = this.value.toLowerCase();
      document.querySelectorAll("#maintenanceTableBody tr").forEach((row) => {
        row.style.display = row.textContent.toLowerCase().includes(value)
          ? ""
          : "none";
      });
    });
  }

  // Form Submits
  handleFormSubmit("formEditMaintenance", "modalEditMaintenance");
  handleFormSubmit("formDeleteMaintenance", "modalDeleteMaintenance");

  // Init Modals setup
  setupModals();
});

// ==== HÀM GỌI DỮ LIỆU CHÍNH ====
async function loadMainData() {
  try {
    const data = await MaintenanceAPI.getAll();
    if (data.status === "success") {
      renderTable(data.data);
    }
  } catch (error) {
    console.error("Lỗi khi tải dữ liệu bảo trì:", error);
  }
}

// ==== RENDER UI & LỌC ====
// Lọc trạng thái (Chạy Local JS)
window.filterTable = function (status) {
  document.querySelectorAll(".tab-btn").forEach((tab) => {
    tab.classList.remove("active");
    if (tab.onclick.toString().includes(status)) tab.classList.add("active");
  });

  document.querySelectorAll("#maintenanceTableBody tr").forEach((row) => {
    const rowStatus = row.getAttribute("data-status");
    row.style.display = status === "all" || rowStatus === status ? "" : "none";
  });
};

function renderTable(data) {
  const tbody = document.getElementById("maintenanceTableBody");
  tbody.innerHTML = "";

  let cPending = 0,
    cProcessing = 0;

  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" class="text-center">Không có yêu cầu bảo trì nào.</td></tr>`;
    return;
  }

  data.forEach((r) => {
    let stClass = "",
      stText = "",
      lvlClass = "",
      lvlText = "";

    if (r.status === "PENDING") {
      stClass = "pending";
      stText = "Chờ xử lý";
      cPending++;
    } else if (r.status === "PROCESSING") {
      stClass = "processing";
      stText = "Đang xử lý";
      cProcessing++;
    } else if (r.status === "DONE") {
      stClass = "done";
      stText = "Hoàn thành";
    }

    if (r.level === "LOW") {
      lvlClass = "low";
      lvlText = "Thấp";
    } else if (r.level === "MEDIUM") {
      lvlClass = "medium";
      lvlText = "Bình thường";
    } else if (r.level === "HIGH") {
      lvlClass = "high";
      lvlText = "Khẩn cấp";
    }

    const safeData = JSON.stringify({ ...r, stText, lvlText }).replace(
      /'/g,
      "&#39;",
    );
    const dateStr = r.created_at.split(" ")[0].split("-").reverse().join("/");
    const costFmt =
      r.cost > 0 ? new Intl.NumberFormat("vi-VN").format(r.cost) : "-";

    tbody.innerHTML += `
            <tr data-status="${r.status}">
                <td><strong class="bt-code">#${r.id}</strong></td>
                <td><span class="room-tag bt-room">${r.room_name}</span><div style="font-size:11px; color:#666; margin-top:3px;">${r.building_name}</div></td>
                <td class="bt-reporter">${r.reporter_name}</td>
                <td class="bt-content">${r.content}</td>
                <td><span class="badge-level ${lvlClass}">${lvlText}</span></td>
                <td class="bt-date">${dateStr}</td>
                <td style="font-weight:bold; color:#d32f2f;">${costFmt}</td>
                <td><span class="badge-status ${stClass}">${stText}</span></td>
                <td class="text-center">
                    <button class="btn-icon btn-view" onclick='openView(${safeData})'><i class="fa-solid fa-eye" style="color: #3c8dbc;"></i></button>
                    <button class="btn-icon btn-edit" onclick='openEdit(${safeData})'><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn-icon btn-delete" onclick="openDelete(${r.id})"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
  });

  // Cập nhật counter
  const pBadge = document.getElementById("cnt_pending");
  const prBadge = document.getElementById("cnt_processing");
  if (cPending > 0) {
    pBadge.innerText = cPending;
    pBadge.style.display = "inline-block";
  } else pBadge.style.display = "none";
  if (cProcessing > 0) {
    prBadge.innerText = cProcessing;
    prBadge.style.display = "inline-block";
  } else prBadge.style.display = "none";
}

// ==== QUẢN LÝ MODALS CÁC CHỨC NĂNG ====
function openEdit(d) {
  document.getElementById("edit_id").value = d.id;
  document.getElementById("edit_room").value = d.room_name;
  document.getElementById("edit_reporter").value = d.reporter_name;
  document.getElementById("edit_content").value = d.content;
  document.getElementById("edit_level").value = d.level;
  document.getElementById("edit_status").value = d.status;
  document.getElementById("edit_cost").value = d.cost > 0 ? d.cost : "";
  document.getElementById("modalEditMaintenance").style.display = "block";
}

function openDelete(id) {
  document.getElementById("delete_id").value = id;
  document.getElementById("delete_code_display").innerText = "#" + id;
  document.getElementById("modalDeleteMaintenance").style.display = "block";
}

async function openView(d) {
  document.getElementById("view_room").innerText = d.room_name;
  document.getElementById("view_reporter").innerText = d.reporter_name;
  document.getElementById("view_date").innerText = d.created_at;
  document.getElementById("view_level").innerText = d.lvlText;
  document.getElementById("view_status").innerText = d.stText;
  document.getElementById("view_cost").innerText =
    (d.cost > 0 ? new Intl.NumberFormat("vi-VN").format(d.cost) : "0") + " đ";
  document.getElementById("view_content").innerText = d.content;

  const gallery = document.getElementById("view_image_gallery");
  gallery.innerHTML = '<div class="loading">Đang tải ảnh...</div>';
  document.getElementById("modalViewDetail").style.display = "block";

  try {
    const res = await MaintenanceAPI.getImages(d.id);
    gallery.innerHTML = "";

    if (res.status === "success" && res.data.length > 0) {
      res.data.forEach((imgUrl) => {
        let filename = imgUrl.replace(/^.*[\\\/]/, "");
        let src = `/public/uploads/maintenance/${filename}`;

        if (imgUrl.startsWith("http")) src = imgUrl;

        let div = document.createElement("div");
        div.className = "gallery-item";
        div.innerHTML = `<img src="${src}" onclick="window.open('${src}', '_blank')" onerror="this.src='../../../public/images/no-image.png'; this.onerror=null;">`;
        gallery.appendChild(div);
      });
    } else {
      gallery.innerHTML =
        '<div class="no-images">Không có hình ảnh đính kèm.</div>';
    }
  } catch (error) {
    console.error("Lỗi tải hình ảnh:", error);
    gallery.innerHTML =
      '<div class="no-images" style="color:red;">Lỗi kết nối khi tải hình ảnh.</div>';
  }
}

function handleFormSubmit(formId, modalId) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    try {
      const data = await MaintenanceAPI.submitForm(new FormData(this));
      alert(data.message);
      if (data.status === "success") {
        document.getElementById(modalId).style.display = "none";
        this.reset();
        loadMainData();
        filterTable("all");
      }
    } catch (error) {
      console.error("Lỗi khi gửi form:", error);
      alert("Có lỗi xảy ra khi thực hiện yêu cầu.");
    }
  });
}

function setupModals() {
  const mEdit = document.getElementById("modalEditMaintenance");
  const mDel = document.getElementById("modalDeleteMaintenance");
  const mView = document.getElementById("modalViewDetail");

  document
    .querySelectorAll(".edit-close")
    .forEach((c) => (c.onclick = () => (mEdit.style.display = "none")));
  document
    .querySelectorAll(".delete-close")
    .forEach((c) => (c.onclick = () => (mDel.style.display = "none")));
  document
    .querySelectorAll(".view-close")
    .forEach((c) => (c.onclick = () => (mView.style.display = "none")));

  window.onclick = function (e) {
    if (e.target == mEdit) mEdit.style.display = "none";
    if (e.target == mDel) mDel.style.display = "none";
    if (e.target == mView) mView.style.display = "none";
  };
}
