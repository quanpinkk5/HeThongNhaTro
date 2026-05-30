// document.getElementById("checkAll")?.addEventListener("change", function () {
//   document
//     .querySelectorAll(".check-item")
//     .forEach((cb) => (cb.checked = this.checked));
// });
// document.addEventListener("DOMContentLoaded", () => {
//   let selectedForm = null;
//   const modal = document.getElementById("confirmModal");
//   const title = document.getElementById("confirmTitle");
//   const message = document.getElementById("confirmMessage");

//   /* Click duyệt / từ chối */
//   document.querySelectorAll(".btn-action.lock").forEach((btn) => {
//     btn.addEventListener("click", function () {
//       selectedForm = this.closest("form");

//       title.innerText = this.dataset.title;
//       message.innerText = this.dataset.message;

//       modal.style.display = "flex";
//     });
//   });

//   /* Đồng ý */
//   document.getElementById("confirmYes").addEventListener("click", () => {
//     if (selectedForm) selectedForm.submit();
//   });

//   /* Hủy */
//   document.getElementById("confirmNo").addEventListener("click", () => {
//     modal.style.display = "none";
//     selectedForm = null;
//   });

//   /* Click ra ngoài modal */
//   window.addEventListener("click", (e) => {
//     if (e.target === modal) {
//       modal.style.display = "none";
//       selectedForm = null;
//     }
//   });
//   const modal = document.getElementById("confirmModal");
//   const message = document.getElementById("confirmMessage");
//   const btnYes = document.getElementById("confirmYes");
//   const btnNo = document.getElementById("confirmNo");
// const form = document.getElementById("deleteForm");
//   const deleteOneInput = document.getElementById("deleteOneInput");

//   let actionType = null;

//   /* ===== XÓA 1 THÔNG BÁO ===== */
//   document.querySelectorAll(".btn-delete-one").forEach((btn) => {
//     btn.addEventListener("click", () => {
//       actionType = "one";
//       deleteOneInput.value = btn.dataset.id;

//       // Bỏ check tất cả checkbox
//       document.querySelectorAll(".check-item").forEach(cb => cb.checked = false);

//       message.innerText = "Bạn có chắc muốn xóa thông báo này không?";
//       modal.style.display = "flex";
//     });
//   });

//   /* ===== XÓA NHIỀU THÔNG BÁO ===== */
//   const btnDeleteMulti = document.querySelector(".btn-delete-multi");
//   if (btnDeleteMulti) {
//     btnDeleteMulti.addEventListener("click", () => {
//       const checked = document.querySelectorAll(".check-item:checked");

//       if (checked.length === 0) {
//         alert("Vui lòng chọn ít nhất 1 thông báo");
//         return;
//       }

//       actionType = "multi";
//       deleteOneInput.value = ""; // Xóa giá trị delete_one

//       message.innerText = `Bạn có chắc muốn xóa ${checked.length} thông báo đã chọn không?`;
//       modal.style.display = "flex";
//     });
//   }

//   /* ===== ĐỒNG Ý ===== */
//   btnYes.addEventListener("click", () => {
//    if (!form) return;
//   modal.style.display = "none";
//   form.submit();
//   });

//   /* ===== HỦY ===== */
//   btnNo.addEventListener("click", () => {
//     modal.style.display = "none";
//     actionType = null;
//   });

//   /* ===== ĐÓNG KHI CLICK NGOÀI MODAL ===== */
//   window.addEventListener("click", (e) => {
//     if (e.target === modal) {
//       modal.style.display = "none";
//       actionType = null;
//     }
//   });
// });
document.addEventListener("DOMContentLoaded", () => {
  loadNotifications();
});

/* ===========================
   LOAD NOTIFICATIONS
=========================== */
async function loadNotifications() {
  const res = await fetch("/public/js/api/Admin/notification_api.php");
  const result = await res.json();

  renderNotifications(result.data);
}

/* ===========================
   RENDER
=========================== */
function renderNotifications(data) {
  const container = document.querySelector(".notification-list");
  container.innerHTML = "";

  if (!data || data.length === 0) {
    container.innerHTML = `
            <div class="empty">
                <i class="fa-regular fa-bell-slash" style="font-size:48px;margin-bottom:10px"></i>
                <p>Bạn không có thông báo nào!</p>
            </div>
        `;
    return;
  }

  data.forEach((n) => {
    container.innerHTML += `
            <div class="notification-item ${n.is_read == 1 ? "read" : "unread"}">

                <input type="checkbox"
                       value="${n.id}"
                       class="check-item">

            <div class="notification-body"
                 style="cursor:pointer"
                 onclick="handleNotificationClick(${n.id}, '${n.link ?? ""}')">

                <div class="icon">
                    <i class="fa-solid fa-bell"></i>
                </div>

                <div class="content">
                    <div class="title">${escapeHtml(n.title)}</div>
                    <div class="text">${escapeHtml(n.content)}</div>
                    <div class="time">
                        ${formatDate(n.created_at)}
                    </div>
                </div>

            </div>

                <button type="button"
                        class="btn-action lock"
                        onclick="confirmDeleteOne(${n.id})">
                    <i class="fa-solid fa-trash"></i>
                </button>

            </div>
        `;
  });

  initCheckAll();
}

/* ===========================
   CHECK ALL
=========================== */
function initCheckAll() {
  const checkAll = document.getElementById("checkAll");
  const items = document.querySelectorAll(".check-item");

  checkAll.checked = false;

  checkAll.addEventListener("change", function () {
    items.forEach((i) => (i.checked = this.checked));
  });
}

/* ===========================
   FORMAT DATE
=========================== */
function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleString("vi-VN");
}

/* ===========================
   ESCAPE HTML
=========================== */
function escapeHtml(text) {
  const div = document.createElement("div");
  div.innerText = text;
  return div.innerHTML;
}

/* ===========================
   DELETE ONE
=========================== */
function confirmDeleteOne(id) {
  showConfirm(
    "Xóa thông báo?",
    "Bạn có chắc muốn xóa thông báo này?",
    async () => {
      await fetch("/public/js/api/Admin/notification_api.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
      });

      loadNotifications();
    },
  );
}

/* ===========================
   DELETE MULTIPLE
=========================== */
document.getElementById("btnDeleteMulti").addEventListener("click", () => {
  const checked = document.querySelectorAll(".check-item:checked");

  if (checked.length === 0) {
    showConfirm("Thông báo", "Vui lòng chọn ít nhất một thông báo.");
    return;
  }

  const ids = Array.from(checked).map((c) => parseInt(c.value));

  showConfirm(
    "Xóa các thông báo?",
    "Bạn có chắc muốn xóa các thông báo đã chọn?",
    async () => {
      await fetch("/public/js/api/Admin/notification_api.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids }),
      });

      loadNotifications();
    },
  );
});

async function handleNotificationClick(id, link) {
  // Gọi API cập nhật đã đọc
  await fetch("/public/js/api/Admin/notification_api.php", {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ mark_read: id }),
  });

  // Nếu có link → chuyển trang
  if (link && link.trim() !== "") {
    window.location.href = link;
  } else {
    // Nếu không có link → reload lại danh sách
    loadNotifyCount();
    loadNotifications();
  }
}

/* ===========================
   MODAL CONFIRM
=========================== */
function showConfirm(title, message, onYes = null) {
  const modal = document.getElementById("confirmModal");
  const confirmTitle = document.getElementById("confirmTitle");
  const confirmMessage = document.getElementById("confirmMessage");
  const confirmYes = document.getElementById("confirmYes");
  const confirmNo = document.getElementById("confirmNo");

  confirmTitle.innerText = title;
  confirmMessage.innerText = message;

  modal.style.display = "flex";

  confirmYes.onclick = () => {
    modal.style.display = "none";
    if (onYes) onYes();
  };

  confirmNo.onclick = () => {
    modal.style.display = "none";
  };
}
