// document.querySelectorAll(".btn-action.view").forEach((btn) => {
//   btn.addEventListener("click", function () {
//     const roomId = this.dataset.id;

//     fetch(`ajax_get_room_detail.php?room_id=${roomId}`)
//       .then((res) => res.json())
//       .then((data) => {
//         if (data.error) {
//           alert(data.error);
//           return;
//         }

//         document.getElementById("view_name").innerText = data.title;
//         document.getElementById("view_landlord").innerText = data.landlord_name;
//         document.getElementById("view_price").innerText = data.price;
//         document.getElementById("view_area").innerText = data.area ?? "—";
//         document.getElementById("view_desc").innerText = data.description ?? "";

//         /* Ảnh */
//         const imgBox = document.getElementById("view_images");
//         imgBox.innerHTML = "";

//         if (!Array.isArray(data.images) || data.images.length === 0) {
//           imgBox.innerHTML = '<p style="color:#999">Không có ảnh</p>';
//         } else {
//           data.images.forEach((img) => {
//             imgBox.innerHTML += `
//                             <img src="../assets/images/chutro/imgphongtro/${img}"
//                                  class="room-thumb" alt="Phòng">

//                         `;
//             // style="width:120px;height:90px;object-fit:cover;border-radius:6px"
//           });
//         }

//         document.getElementById("modalViewRoom").style.display = "flex";
//       });
//   });
// });

// /* Đóng modal */
// document.querySelectorAll(".view-close").forEach((btn) => {
//   btn.addEventListener("click", () => {
//     document.getElementById("modalViewRoom").style.display = "none";
//   });
// });

// // alter
// document.addEventListener("DOMContentLoaded", () => {
//   const alerts = document.querySelectorAll(".alert");

//   alerts.forEach((alert) => {
//     // Đợi 3 giây
//     setTimeout(() => {
//       alert.style.opacity = "0";
//       alert.style.transform = "translateY(-10px)";

//       // Sau hiệu ứng fade thì remove
//       setTimeout(() => {
//         alert.remove();
//       }, 500);
//     }, 3000);
//   });
// });

// // confirm
// document.addEventListener("DOMContentLoaded", () => {
//   let selectedForm = null;
//   const modal = document.getElementById("confirmModal");
//   const title = document.getElementById("confirmTitle");
//   const message = document.getElementById("confirmMessage");

//   /* Click duyệt / từ chối */
//   document.querySelectorAll(".btn-confirm-trigger").forEach((btn) => {
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
// });

// document.addEventListener("DOMContentLoaded", () => {
//   let rejectForm = null;

//   const rejectModal = document.getElementById("rejectModal");
//   const rejectReason = document.getElementById("rejectReason");

//   /* Click nút TỪ CHỐI */
//   document.querySelectorAll(".btn-reject-trigger").forEach((btn) => {
//     btn.addEventListener("click", function () {
//       rejectForm = this.closest("form");
//       rejectReason.value = "";
//       rejectModal.style.display = "flex";
//     });
//   });

//   /* Hủy */
//   document.getElementById("rejectCancel").onclick = () => {
//     rejectModal.style.display = "none";
//     rejectForm = null;
//   };

//   /* Xác nhận */
//   document.getElementById("rejectSubmit").onclick = () => {
//     const reason = rejectReason.value.trim();

//     if (reason === "") {
//       alert("⚠️ Vui lòng nhập lý do từ chối!");
//       return;
//     }

//     /* Gắn reason vào form */
//     const input = document.createElement("input");
//     input.type = "hidden";
//     input.name = "reason";
//     input.value = reason;

//     rejectForm.appendChild(input);
//     rejectForm.submit();
//   };
//   window.addEventListener("click", (e) => {
//     if (e.target === rejectModal) {
//       rejectModal.style.display = "none";
//       selectedForm = null;
//     }
//   });
// });
const API_URL = "/public/js/api/Admin/rooms.php";

let currentPage = 1;
let currentKeyword = "";
let currentStatus = "";
const limit = 3;

/* ===============================
   LOAD DANH SÁCH
================================ */
function loadRooms(page = 1) {
  currentPage = page;

  fetch(
    `${API_URL}?page=${page}&limit=${limit}&keyword=${encodeURIComponent(
      currentKeyword,
    )}&status=${encodeURIComponent(currentStatus)}`,
  )
    .then((res) => res.json())
    .then((res) => {
      renderTable(res.data);
      renderPagination(res.total);
    });
}

/* ===============================
   RENDER TABLE
================================ */
function renderTable(data) {
  const tbody = document.getElementById("roomTable");
  tbody.innerHTML = "";

  if (!data || data.length === 0) {
    tbody.innerHTML =
      "<tr><td colspan='6' style='text-align:center'>Không có dữ liệu</td></tr>";
    return;
  }

  data.forEach((room) => {
    let badge = "";

    if (room.status === "PENDING") {
      badge = `<span class="badge pending">⏳ Chờ duyệt</span>`;
    } else if (room.status === "APPROVED") {
      badge = `<span class="badge approved">🟢 Đã duyệt</span>`;
    } else {
      badge = `<span class="badge rejected">🔴 Từ chối</span>`;
    }

    tbody.innerHTML += `
      <tr>
        <td>${room.id}</td>
        <td class="room-name">${room.title}</td>
        <td>${room.landlord_name}</td>
        <td class="price">${Number(room.price).toLocaleString()}đ</td>
        <td>${badge}</td>
        <td class="actions">
          <button class="btn-action view"
                  onclick="viewRoom(${room.id})">
              <i class="fa-solid fa-eye"></i>
          </button>

          ${
            room.status === "PENDING"
              ? `
            <button class="btn-action approve"
                    onclick="confirmApprove(${room.id})">
                <i class="fa-solid fa-check"></i>
            </button>

            <button class="btn-action reject"
                    onclick="openRejectModal(${room.id})">
                <i class="fa-solid fa-xmark"></i>
            </button>
          `
              : ""
          }
        </td>
      </tr>
    `;
  });
}

/* ===============================
   PHÂN TRANG
================================ */
function renderPagination(total) {
  const totalPages = Math.ceil(total / limit);
  const div = document.getElementById("pagination");
  div.innerHTML = "";

  if (totalPages <= 1) return;

  if (currentPage > 1) {
    div.innerHTML += `
      <a onclick="loadRooms(${currentPage - 1})">« Trước</a>
    `;
  }

  for (let i = 1; i <= totalPages; i++) {
    div.innerHTML += `
      <a onclick="loadRooms(${i})"
         class="${i === currentPage ? "active" : ""}">
         ${i}
      </a>
    `;
  }

  if (currentPage < totalPages) {
    div.innerHTML += `
      <a onclick="loadRooms(${currentPage + 1})">Sau »</a>
    `;
  }
}

/* ===============================
   FILTER
================================ */
document.getElementById("filterForm").addEventListener("submit", function (e) {
  e.preventDefault();
  currentKeyword = this.keyword.value;
  currentStatus = this.status.value;
  loadRooms(1);
});

document.getElementById("btnReset").addEventListener("click", () => {
  currentKeyword = "";
  currentStatus = "";
  document.getElementById("filterForm").reset();
  loadRooms(1);
});

/* ===============================
   VIEW DETAIL
================================ */
function viewRoom(id) {
  fetch(`${API_URL}?id=${id}`)
    .then((res) => res.json())
    .then((data) => {
      if (!data) return;

      document.getElementById("view_name").innerText = data.title;
      document.getElementById("view_landlord").innerText = data.landlord_name;
      document.getElementById("view_price").innerText = Number(
        data.price,
      ).toLocaleString();
      document.getElementById("view_area").innerText = data.area ?? "—";
      document.getElementById("view_desc").innerText = data.description ?? "";

      const imgBox = document.getElementById("view_images");
      imgBox.innerHTML = "";

      if (!data.images || data.images.length === 0) {
        imgBox.innerHTML = '<p style="color:#999">Không có ảnh</p>';
      } else {
        data.images.forEach((img) => {
          imgBox.innerHTML += `
            <img src="../../../public/images/${img}"
                 class="room-thumb"
                 alt="Phòng">
          `;
        });
      }

      document.getElementById("modalViewRoom").style.display = "flex";
    });
}

/* ===============================
   ĐÓNG MODAL VIEW
================================ */
document.querySelectorAll(".view-close").forEach((btn) => {
  btn.addEventListener("click", () => {
    document.getElementById("modalViewRoom").style.display = "none";
  });
});

/* ===============================
   APPROVE
================================ */
let selectedRoomId = null;

function confirmApprove(id) {
  selectedRoomId = id;

  document.getElementById("confirmTitle").innerText = "Duyệt phòng";
  document.getElementById("confirmMessage").innerText =
    "Bạn có chắc chắn muốn duyệt phòng này không?";

  document.getElementById("confirmModal").style.display = "flex";
}

document.getElementById("confirmYes").addEventListener("click", () => {
  fetch(API_URL, {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: selectedRoomId,
      action: "approve",
    }),
  }).then(() => {
    closeConfirmModal();
    loadRooms(currentPage);
  });
});

document
  .getElementById("confirmNo")
  .addEventListener("click", closeConfirmModal);

function closeConfirmModal() {
  document.getElementById("confirmModal").style.display = "none";
  selectedRoomId = null;
}

/* ===============================
   REJECT
================================ */
function openRejectModal(id) {
  selectedRoomId = id;
  document.getElementById("rejectReason").value = "";
  document.getElementById("rejectModal").style.display = "flex";
}

document.getElementById("rejectCancel").onclick = () => {
  document.getElementById("rejectModal").style.display = "none";
};

document.getElementById("rejectSubmit").onclick = () => {
  const reason = document.getElementById("rejectReason").value.trim();

  if (!reason) {
    alert("⚠️ Vui lòng nhập lý do từ chối!");
    return;
  }

  fetch(API_URL, {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id: selectedRoomId,
      action: "reject",
      reason: reason,
    }),
  }).then(() => {
    document.getElementById("rejectModal").style.display = "none";
    loadRooms(currentPage);
  });
};

/* ===============================
   LOAD BAN ĐẦU
================================ */

document.addEventListener("DOMContentLoaded", () => {
  loadRooms();
});
