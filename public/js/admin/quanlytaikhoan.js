// document.addEventListener('DOMContentLoaded', function () {

//     /* =========================
//        MODAL XEM TÀI KHOẢN
//     ========================== */

//     const modalView = document.getElementById('modalViewAcc');

//     document.querySelectorAll('.btn-icon.view').forEach(function (btn) {
//         btn.addEventListener('click', function () {

//             document.getElementById('view_id').value       = this.dataset.id;
//             document.getElementById('view_code').value     = this.dataset.ma;
//             document.getElementById('view_username').value = this.dataset.username;
//             document.getElementById('view_email').value    = this.dataset.email;
//             document.getElementById('view_phone').value    = this.dataset.phone;
//             document.getElementById('view_cccd').value     = this.dataset.cccd;
//             document.getElementById('view_role').value     = this.dataset.role;
//             document.getElementById('view_time').value     = this.dataset.time;

//             modalView.style.display = 'flex';
//         });
//     });

//     document.querySelectorAll('.view-close').forEach(function (btn) {
//         btn.addEventListener('click', function () {
//             modalView.style.display = 'none';
//         });
//     });

//     /* =========================
//        MODAL THÊM TÀI KHOẢN
//     ========================== */

//     const modalAdd = document.getElementById('modalAddAcc');
//     const btnOpenAdd = document.getElementById('btnOpenAddAcc');

//     if (btnOpenAdd) {
//         btnOpenAdd.addEventListener('click', function () {
//             modalAdd.style.display = 'flex';
//         });
//     }

//     document.querySelectorAll('.add-close').forEach(function (btn) {
//         btn.addEventListener('click', function () {
//             modalAdd.style.display = 'none';
//         });
//     });

//     /* =========================
//        ĐÓNG MODAL KHI CLICK RA NGOÀI
//     ========================== */

//     window.addEventListener('click', function (e) {
//         if (e.target === modalView) modalView.style.display = 'none';
//         if (e.target === modalAdd) modalAdd.style.display = 'none';
//     });

// });

// document.addEventListener("DOMContentLoaded", function () {
//     const alertBox = document.getElementById("autoHideAlert");
//     if (alertBox) {
//         setTimeout(() => {
//             alertBox.style.opacity = "0";
//             setTimeout(() => alertBox.remove(), 500);
//         }, 3000);
//     }
// });
// document.addEventListener('DOMContentLoaded', function () {

//     const modalAdd = document.getElementById('modalAddAcc');
//     const modalBody = modalAdd?.querySelector('.modal-body');

//     /* ===== CHỈ MỞ MODAL KHI LỖI ===== */
//     if (modalBody && modalBody.dataset.openModal === 'add') {
//         modalAdd.style.display = 'flex';
//     }

//     /* ===== AUTO HIDE ALERT ===== */
//     document.querySelectorAll('.auto-hide').forEach(alert => {
//         setTimeout(() => {
//             alert.style.display = 'none';
//         }, 3000);
//     });

// });

// document.querySelectorAll('.btn-icon.edit').forEach(btn => {
//     btn.addEventListener('click', () => {
//         document.getElementById('edit_id').value = btn.dataset.id;
//         document.getElementById('edit_name').value = btn.dataset.name;
//         document.getElementById('edit_email').value = btn.dataset.email;
//         document.getElementById('edit_phone').value = btn.dataset.phone;
//         document.getElementById('edit_cccd').value = btn.dataset.cccd;
//         document.getElementById('edit_role').value = btn.dataset.role;
//         document.getElementById('edit_status').value = btn.dataset.status;

//         document.getElementById('modalEditAcc').classList.add('show');
//     });
// });

// document.querySelectorAll('.edit-close').forEach(btn => {
//     btn.addEventListener('click', () => {
//         document.getElementById('modalEditAcc').classList.remove('show');
//     });
// });
let currentPage = 1;
let limit = 5;
let selectedId = null;
let selectedAction = null;

async function loadUsers(page = 1) {
  currentPage = page;

  const keyword = document.getElementById("keyword").value;
  const role = document.getElementById("role").value;
  const status = document.getElementById("status").value;

  const res = await fetch(
    `/public/js/api/Admin/users.php?page=${page}&limit=${limit}&keyword=${keyword}&role=${role}&status=${status}`,
  );

  const result = await res.json();

  renderTable(result.data);
  renderPagination(result.total);
}

function renderTable(users) {
  const tbody = document.getElementById("userTable");
  tbody.innerHTML = "";

  users.forEach((u) => {
    const isActive = u.status === "ACTIVE";

    let actionButtons = "";

    // 🔒 Nếu là ADMIN → chỉ được xem
    if (u.role === "ADMIN") {
      actionButtons = `
      <button class="btn-icon view" onclick="viewUser(${u.id})">
        <i class="fa-solid fa-eye"></i>
      </button>
    `;
    }
    // 👤 Nếu không phải ADMIN → full chức năng
    else {
      actionButtons = `
      <button class="btn-icon view" onclick="viewUser(${u.id})">
        <i class="fa-solid fa-eye"></i>
      </button>

      ${
        isActive
          ? `<button class="btn-icon lock"
                onclick="toggleStatus(${u.id}, 'lock')">
                <i class="fa fa-lock"></i>
             </button>`
          : `<button class="btn-icon unlock"
                onclick="toggleStatus(${u.id}, 'unlock')">
                <i class="fa fa-unlock"></i>
             </button>`
      }

      <button class="btn-icon unlock"
              title="Reset mật khẩu"
              onclick="resetPass(${u.id})">
        <i class="fa fa-key"></i>
      </button>

      <button class="btn-icon edit"
              title="Sửa tài khoản"
              onclick="editUser(${u.id})">
        <i class="fa fa-pen"></i>
      </button>

      ${
        u.can_send_mail == 1
          ? `<button class="btn-icon mail"
                onclick="sendMail(${u.id})">
                <i class="fa fa-envelope"></i>
             </button>`
          : ""
      }
    `;
    }

    tbody.innerHTML += `
    <tr>
      <td>${u.code}</td>
      <td>${u.name}</td>
      <td>${u.email}</td>
      <td>${u.phone}</td>
      <td>${u.role}</td>
      <td>
        <span class="status ${u.status.toLowerCase()}">
          ${u.status}
        </span>
      </td>
      <td>${actionButtons}</td>
    </tr>
  `;
  });
}

function renderPagination(total) {
  const totalPages = Math.ceil(total / limit);
  const div = document.getElementById("pagination");
  div.innerHTML = "";

  if (totalPages <= 1) return;

  // 🔹 Hiện nút Trước nếu không phải trang đầu
  if (currentPage > 1) {
    div.innerHTML += `
      <button onclick="loadUsers(${currentPage - 1})">
        « Trước
      </button>
    `;
  }

  // 🔹 Hiển thị số trang
  for (let i = 1; i <= totalPages; i++) {
    div.innerHTML += `
      <button 
        class="${i === currentPage ? "active" : ""}"
        onclick="loadUsers(${i})">
        ${i}
      </button>
    `;
  }

  // 🔹 Hiện nút Sau nếu chưa phải trang cuối
  if (currentPage < totalPages) {
    div.innerHTML += `
      <button onclick="loadUsers(${currentPage + 1})">
        Sau »
      </button>
    `;
  }
}

async function toggleStatus(id, action) {
  let confirmText =
    action === "lock"
      ? "Bạn có chắc muốn khóa tài khoản này?"
      : "Bạn có chắc muốn mở khóa tài khoản này?";

  if (!confirm(confirmText)) return;

  await fetch("/public/js/api/Admin/users.php", {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: id,
      action: action,
    }),
  });

  loadUsers(currentPage);
}
async function toggleStatus(id, action) {
  const isLock = action === "lock";

  const result = await Swal.fire({
    title: isLock ? "Khóa tài khoản?" : "Mở khóa tài khoản?",
    text: isLock
      ? "Tài khoản sẽ bị vô hiệu hóa!"
      : "Tài khoản sẽ được kích hoạt lại!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: isLock ? "#e74c3c" : "#2ecc71",
    cancelButtonColor: "#6c757d",
    confirmButtonText: isLock ? "Khóa ngay" : "Mở khóa",
    cancelButtonText: "Hủy",
  });

  if (!result.isConfirmed) return;

  await fetch("/public/js/api/Admin/users.php", {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: id,
      action: action,
    }),
  });

  await Swal.fire({
    title: "Thành công!",
    text: isLock ? "Tài khoản đã bị khóa." : "Tài khoản đã được mở khóa.",
    icon: "success",
    timer: 1500,
    showConfirmButton: false,
  });

  loadUsers(currentPage);
}
// function toggleStatus(id, action){

//     selectedId = id;
//     selectedAction = action;

//     const isLock = action === "lock";

//     document.getElementById("modalTitle").innerText =
//         isLock ? "Khóa tài khoản?" : "Mở khóa tài khoản?";

//     document.getElementById("modalText").innerText =
//         isLock
//         ? "Bạn có chắc chắn muốn khóa tài khoản này?"
//         : "Bạn có chắc chắn muốn mở khóa tài khoản này?";

//     document.getElementById("btnConfirm").style.background =
//         isLock ? "#e74c3c" : "#2ecc71";

//     document.getElementById("confirmModal").style.display = "flex";
// }
// document.getElementById("btnCancel").onclick = function(){
//     document.getElementById("confirmModal").style.display = "none";
// };
// document.getElementById("btnConfirm").onclick = function(){

//     fetch("/public/api/users.php", {
//         method:"PATCH",
//         headers:{"Content-Type":"application/json"},
//         body:JSON.stringify({
//             id:selectedId,
//             action:selectedAction
//         })
//     })
//     .then(res=>res.json())
//     .then(data=>{
//         document.getElementById("confirmModal").style.display = "none";
//         loadUsers(currentPage);
//     });

// };
// async function resetPass(id) {
//   const res = await fetch("/public/api/users.php", {
//     method: "PATCH",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify({ id: id, action: "reset" }),
//   });

//   const data = await res.json();
//   alert("Mật khẩu mới: " + data.data);

//   loadUsers(currentPage);
// }
async function resetPass(id) {
  openConfirmModal(
    "Xác nhận reset mật khẩu",
    "Bạn có chắc chắn muốn reset mật khẩu tài khoản này không?",
    async function () {
      try {
        const res = await fetch("/public/js/api/Admin/users.php", {
          method: "PATCH",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: id, action: "reset" }),
        });

        const data = await res.json();

        if (data.success) {
          const newPassword = data.data;

          showGlobalAlert(
            `
            Đã reset mật khẩu thành công.<br>
            🔑 Mật khẩu mới: 
            <strong class="new-pass">${newPassword}</strong><br>
            ⚠ Vui lòng gửi cho người dùng và yêu cầu đổi mật khẩu ngay.
            `,
            "success",
          );

          loadUsers(currentPage);
        } else {
          showGlobalAlert("Reset mật khẩu thất bại", "error");
        }
      } catch (err) {
        showGlobalAlert("Lỗi hệ thống", "error");
      }
    },
  );
}

document.addEventListener("DOMContentLoaded", () => {
  loadUsers();
});
/* =========================
       ADD USERS
    ========================== */
document
  .getElementById("formAddAcc")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // chặn reload trang

    const formData = new FormData(this);

    const data = {
      name: formData.get("name"),
      password: formData.get("password"),
      email: formData.get("email"),
      phone: formData.get("phone"),
      cccd: formData.get("cccd"),
      role: formData.get("role"),
    };

    try {
      const res = await fetch("/public/js/api/Admin/users.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await res.json();

      if (result.success) {
        showAlert("Thêm tài khoản thành công", "success");
        document.getElementById("formAddAcc").reset();
        loadUsers(currentPage);
      } else {
        showAlert(`Thêm thất bại: ${result.message}`, "error");
        document.getElementById("formAddAcc").reset();
      }
    } catch (err) {
      showAlert("Lỗi hệ thống", "error");
    }
  });
function showAlert(message, type) {
  const div = document.createElement("div");
  div.className = "alert " + type;
  div.innerHTML = (type === "success" ? "✅ " : "❌ ") + message;

  document.querySelector(".modal-body").prepend(div);

  setTimeout(() => {
    div.remove();
  }, 3000);
}
/* =========================
       notification all page
    ========================== */
function showGlobalAlert(message, type) {
  const div = document.createElement("div");
  div.className = "alert " + type;
  div.innerHTML = (type === "success" ? "✅ " : "❌ ") + message;

  const container = document.getElementById("globalAlert");
  container.innerHTML = "";
  container.appendChild(div);

  setTimeout(() => {
    div.remove();
  }, 3000);
}
function openAddModal() {
  document.getElementById("modalAddAcc").style.display = "flex";
}

function closeAddModal() {
  document.getElementById("modalAddAcc").style.display = "none";
}
const modalAdd = document.getElementById("modalAddAcc");
const modalView = document.getElementById("modalViewUser");
const modal = document.getElementById("modalEditAcc");
// Click ra ngoài content thì đóng
window.addEventListener("click", function (e) {
  if (e.target === modalAdd) {
    closeAddModal();
  }
  if (e.target === modalView) modalView.style.display = "none";
  if (e.target === modal) {
    modal.style.display = "none";
  }
});
/* =========================
       DETAIL USERS
    ========================== */
async function viewUser(id) {
  const res = await fetch(`/public/js/api/Admin/users.php?id=${id}`);
  const user = await res.json();
  document.getElementById("view_id").value = user.id;
  document.getElementById("view_code").value = user.code;
  document.getElementById("view_username").value = user.name;
  document.getElementById("view_email").value = user.email;
  document.getElementById("view_phone").value = user.phone;
  document.getElementById("view_cccd").value = user.cccd;
  document.getElementById("view_role").value = user.role;
  const statusInput = document.getElementById("view_status");

  statusInput.value = user.status;

  // Xóa class cũ trước
  statusInput.classList.remove("status-active", "status-blocked");

  // Gán class theo trạng thái
  if (user.status === "ACTIVE") {
    statusInput.classList.add("status-active");
  } else {
    statusInput.classList.add("status-blocked");
  }
  document.getElementById("view_time").value = user.created_at;

  document.getElementById("modalViewUser").style.display = "flex";
}
function closeViewModal() {
  document.getElementById("modalViewUser").style.display = "none";
}
document.querySelectorAll(".view-close").forEach((btn) => {
  btn.addEventListener("click", closeViewModal);
});
/* =========================
       EDIT USERS
    ========================== */
async function editUser(id) {
  const res = await fetch(`/public/js/api/Admin/users.php?id=${id}`);
  const user = await res.json();

  document.getElementById("edit_id").value = user.id;
  document.getElementById("edit_name").value = user.name;
  document.getElementById("edit_email").value = user.email;
  document.getElementById("edit_phone").value = user.phone;
  document.getElementById("edit_cccd").value = user.cccd;
  document.getElementById("edit_role").value = user.role;
  document.getElementById("edit_status").value = user.status;

  document.getElementById("modalEditAcc").style.display = "flex";
}

// document
//   .getElementById("formEditAcc")
//   .addEventListener("submit", async function (e) {
//     e.preventDefault();

//     const formData = new FormData(this);

//     const data = {
//       id: formData.get("id"),
//       name: formData.get("name"),
//       email: formData.get("email"),
//       phone: formData.get("phone"),
//       cccd: formData.get("CCCD"),
//       role: formData.get("role"),
//       status: formData.get("status"),
//     };

//     try {
//       const res = await fetch("/public/api/users.php", {
//         method: "PUT",
//         headers: {
//           "Content-Type": "application/json",
//         },
//         body: JSON.stringify(data),
//       });

//       const result = await res.json();

//       if (result.success) {
//         showAlert("Cập nhật thành công", "success");
//         loadUsers(currentPage);
//       } else {
//         showAlert("Cập nhật thất bại", "error");
//       }
//     } catch (err) {
//       showAlert("Lỗi hệ thống", "error");
//     }
//   });
document
  .getElementById("formEditAcc")
  .addEventListener("submit", async function (e) {
    e.preventDefault();
    // hiện xác nhận
    openConfirmModal(
      "Xác nhận cập nhật",
      "Bạn có chắc chắn muốn lưu thay đổi không?",
      async function () {
        const formData = new FormData(e.target);

        const data = {
          id: formData.get("id"),
          name: formData.get("name"),
          email: formData.get("email"),
          phone: formData.get("phone"),
          cccd: formData.get("cccd"),
          role: formData.get("role"),
          status: formData.get("status"),
        };

        try {
          const res = await fetch("/public/js/api/Admin/users.php", {
            method: "PUT",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
          });

          const result = await res.json();

          if (result.success) {
            closeEditModal();
            loadUsers(currentPage); // reload table
            showGlobalAlert("Cập nhật thành công", "success");
          } else {
            showGlobalAlert("Cập nhật thất bại", "error");
          }
        } catch (err) {
          showGlobalAlert("Lỗi hệ thống", "error");
        }
      },
    );
  });
function openConfirmModal(title, text, onConfirm) {
  const modal = document.getElementById("confirmModal");

  document.getElementById("modalTitle").innerText = title;
  document.getElementById("modalText").innerText = text;

  modal.style.display = "flex";

  const okBtn = document.getElementById("btnConfirm");
  const cancelBtn = document.getElementById("btnCancel");

  okBtn.onclick = function () {
    modal.style.display = "none";
    onConfirm();
  };

  cancelBtn.onclick = function () {
    modal.style.display = "none";
  };
}
function closeEditModal() {
  document.getElementById("modalEditAcc").style.display = "none";
}
document.querySelectorAll(".edit-close").forEach((btn) => {
  btn.addEventListener("click", closeEditModal);
});
/* =========================
       Sendmail USERS
    ========================== */
function sendMail(id) {
  openConfirmModal(
    "Xác nhận gửi mail",
    "Bạn có chắc chắn muốn gửi mail?",
    async function () {
      const res = await fetch("/public/js/api/Admin/users.php", {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: id,
          action: "send_mail",
        }),
      });

      const result = await res.json();

      if (result.success) {
        showGlobalAlert(
          "Đã gửi mail thành công<br>⚠ Yêu cầu người dùng đổi mật khẩu ngay.",
          "success",
        );

        loadUsers(currentPage);
      } else {
        showGlobalAlert("Gửi mail thất bại", "error");
      }
    },
  );
}
