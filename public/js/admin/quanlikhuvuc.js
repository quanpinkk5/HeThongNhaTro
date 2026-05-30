// const modal = document.getElementById('areaModal');
// const btnAdd = document.getElementById('btnAdd');
// const closeModal = document.getElementById('closeModal');

// const areaId = document.getElementById('area_id');
// const areaName = document.getElementById('area_name');
// const areaAction = document.getElementById('area_action');
// const modalTitle = document.getElementById('modalTitle');

// /* THÊM */
// btnAdd.onclick = () => {
//     modal.style.display = 'flex';
//     modalTitle.innerText = '➕ Thêm khu vực';
//     areaId.value = '';
//     areaName.value = '';
//     areaAction.value = 'add';
// };

// /* SỬA */
// document.querySelectorAll('.btn-action.edit').forEach(btn => {
//     btn.onclick = () => {
//         modal.style.display = 'flex';
//         modalTitle.innerText = '✏️ Sửa khu vực';
//         areaId.value = btn.dataset.id;
//         areaName.value = btn.dataset.name;
//         areaAction.value = 'edit';
//     };
// });

// closeModal.onclick = () => modal.style.display = 'none';
// window.onclick = e => {
//     if (e.target === modal) modal.style.display = 'none';
// };

// document.addEventListener('DOMContentLoaded', () => {
//     const alerts = document.querySelectorAll('.alert');

//     alerts.forEach(alert => {

//         setTimeout(() => {
//             alert.style.opacity = '0';
//             alert.style.transform = 'translateY(-10px)';

//             // Sau hiệu ứng fade thì remove
//             setTimeout(() => {
//                 alert.remove();
//             }, 500);
//         }, 3000);
//     });
// });
let currentKeyword = "";
let currentStatus = "";

document.addEventListener("DOMContentLoaded", () => {
  loadAreas();
});

/* =============================
   LOAD DATA
============================= */
function loadAreas() {
  fetch(
    `/public/js/api/Admin/area_api.php?keyword=${currentKeyword}&status=${currentStatus}`,
  )
    .then((res) => res.json())
    .then((res) => {
      const tbody = document.getElementById("areaTable");
      tbody.innerHTML = "";

      if (!res.data || res.data.length === 0) {
        tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align:center;">
                        Không có dữ liệu
                    </td>
                </tr>
            `;
        return;
      }

      res.data.forEach((area) => {
        tbody.innerHTML += `
                <tr>
                    <td>${area.id}</td>
                    <td>${area.name}</td>
                    <td>
                        ${
                          area.status === "ACTIVE"
                            ? '<span class="badge active">Hiển thị</span>'
                            : '<span class="badge hidden">Đã ẩn</span>'
                        }
                    </td>
                    <td>
                        <button class="btn-action edit"
                            onclick="openEditModal(${area.id}, \`${area.name}\`)">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button class="btn-action ${area.status === "ACTIVE" ? "lock" : "unlock"}"
                            onclick="toggleArea(${area.id})">
                            <i class="fa-solid ${area.status === "ACTIVE" ? "fa-eye-slash" : "fa-eye"}"></i>
                        </button>
                    </td>
                </tr>
            `;
      });
    });
}

/* =============================
   FILTER
============================= */
function applyFilter() {
  currentKeyword = document.getElementById("keyword").value;
  currentStatus = document.getElementById("status").value;
  loadAreas();
}

function resetFilter() {
  document.getElementById("keyword").value = "";
  document.getElementById("status").value = "";
  currentKeyword = "";
  currentStatus = "";
  loadAreas();
}

/* =============================
   MODAL
============================= */
function openAddModal() {
  document.getElementById("modalTitle").innerText = "Thêm khu vực";
  document.getElementById("area_action").value = "add";
  document.getElementById("area_id").value = "";
  document.getElementById("area_name").value = "";
  document.getElementById("areaModal").style.display = "flex";
}

function openEditModal(id, name) {
  document.getElementById("modalTitle").innerText = "Sửa khu vực";
  document.getElementById("area_action").value = "edit";
  document.getElementById("area_id").value = id;
  document.getElementById("area_name").value = name;
  document.getElementById("areaModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("areaModal").style.display = "none";
}

/* =============================
   SAVE (ADD / EDIT)
============================= */
function saveArea() {
  const action = document.getElementById("area_action").value;
  const id = document.getElementById("area_id").value;
  const name = document.getElementById("area_name").value.trim();

  if (name === "") {
    alert("Tên khu vực không được để trống!");
    return;
  }

  let message =
    action === "add"
      ? "Bạn có chắc chắn muốn thêm khu vực này không?"
      : "Bạn có chắc chắn muốn cập nhật khu vực này không?";

  if (!confirm(message)) return;

  fetch("/public/js/api/Admin/area_api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: action,
      id: id,
      name: name,
    }),
  })
    .then((res) => res.json())
    .then((result) => {
      if (result.success) {
        alert(
          action === "add"
            ? "Thêm khu vực thành công!"
            : "Cập nhật khu vực thành công!"
        );

        closeModal();
        loadAreas();
      } else {
        alert(result.message || "Tên khu vực đã tồn tại!");
      }
    });
}

/* =============================
   TOGGLE STATUS
============================= */
function toggleArea(id) {
  if (!confirm("Bạn có chắc muốn thay đổi trạng thái khu vực này?")) return;

  fetch("/public/js/api/Admin//area_api.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "toggle",
      id: id,
    }),
  })
    .then((res) => res.json())
    .then(() => loadAreas());
}
