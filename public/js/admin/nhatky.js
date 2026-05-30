// document.addEventListener('DOMContentLoaded', function () {

//     const modal = document.getElementById('modalViewLog');
//     const closeBtns = document.querySelectorAll('.close-log');

//     document.querySelectorAll('.btn-view-log').forEach(btn => {
//         btn.addEventListener('click', () => {

//             document.getElementById('log_id').value = btn.dataset.id_log;
//             document.getElementById('log_admin').value = btn.dataset.admin;
//             document.getElementById('log_action').value = btn.dataset.action;
//             document.getElementById('log_target').value = btn.dataset.target;
//             document.getElementById('log_target_name').value = btn.dataset.target_name;
//             document.getElementById('log_target_type').value = btn.dataset.target_type;
//             document.getElementById('log_target_role').value = btn.dataset.target_role;
//             document.getElementById('log_ip').value = btn.dataset.ip;
//             document.getElementById('log_agent').value = btn.dataset.agent;
//             document.getElementById('log_time').value = btn.dataset.time;

//             modal.classList.add('show');
//         });
//     });

//     closeBtns.forEach(btn => {
//         btn.addEventListener('click', () => {
//             modal.classList.remove('show');
//         });
//     });

//     window.addEventListener('click', e => {
//         if (e.target === modal) {
//             modal.classList.remove('show');
//         }
//     });

// });
// /* === alter ====  */
// document.addEventListener('DOMContentLoaded', () => {
//     const alerts = document.querySelectorAll('.alert');

//     alerts.forEach(alert => {
//         // Đợi 3 giây
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
const API_URL = "/public/js/api/Admin/activity_log_api.php";
let currentPage = 1;
const limit = 6;
let currentKeyword = "";
let currentAction = "";
let currentRole = "";
let currentDateFrom = "";
let currentDateTo = "";

/* ================= LOAD DATA ================= */
async function loadLogs(page = 1) {
  currentPage = page;

  const res = await fetch(
    `${API_URL}?page=${page}&limit=${limit}` +
      `&keyword=${encodeURIComponent(currentKeyword)}` +
      `&action=${encodeURIComponent(currentAction)}` +
      `&target_role=${encodeURIComponent(currentRole)}` +
      `&date_from=${encodeURIComponent(currentDateFrom)}` +
      `&date_to=${encodeURIComponent(currentDateTo)}`,
  );

  const result = await res.json();
  renderTable(result.data);
  renderPagination(result.total);
}

/* ================= RENDER TABLE ================= */
function renderTable(logs) {
  const tbody = document.getElementById("logTable");
  tbody.innerHTML = "";

  if (!logs || logs.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center">
                Không có dữ liệu
             </td></tr>`;
    return;
  }

  logs.forEach((log) => {
    let actionClass = "";
    if (log.action.includes("LOGIN")) actionClass = "login";
    else if (log.action.includes("LOGOUT")) actionClass = "logout";
    else if (log.action.includes("RESET")) actionClass = "reset";
    else if (log.action.includes("LOCK")) actionClass = "lock";

    tbody.innerHTML += `
            <tr>
                <td>${log.admin_name}</td>
                <td class="log-action ${actionClass}">
                    ${log.action}
                </td>
                <td>
                    ${log.target_name ?? ""}
                    ${
                      log.target_role
                        ? `<span class="role-badge role-${log.target_role.toLowerCase()}">
                            ${log.target_role}
                           </span>`
                        : ""
                    }
                </td>
                <td>${log.ip_address ?? ""}</td>
                <td>${formatDate(log.created_at)}</td>
                <td>
                    <button class="btn-view-log" onclick="openModal(${log.id})">
                        <i class="fa fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;

    // Gán sự kiện modal sau khi render
    // setTimeout(() => {
    //     const buttons = document.querySelectorAll(".btn-view-log");
    //     const lastBtn = buttons[buttons.length - 1];
    //     lastBtn.addEventListener("click", () => openModal(log));
    // }, 0);
  });
}

/* ================= PAGINATION ================= */
function renderPagination(total) {
  const totalPages = Math.ceil(total / limit);
  const div = document.querySelector(".pagination");
  div.innerHTML = "";

  if (totalPages <= 1) return;

  if (currentPage > 1) {
    div.innerHTML += `<a onclick="loadLogs(${currentPage - 1})">« Trước</a>`;
  }

  for (let i = 1; i <= totalPages; i++) {
    div.innerHTML += `
            <a onclick="loadLogs(${i})"
               class="${i === currentPage ? "active" : ""}">
               ${i}
            </a>
        `;
  }

  if (currentPage < totalPages) {
    div.innerHTML += `<a onclick="loadLogs(${currentPage + 1})">Sau »</a>`;
  }
}

/* ================= FORMAT DATE ================= */
function formatDate(dateStr) {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleString("vi-VN");
}

/* ================= MODAL ================= */
async function openModal(id) {
  const res = await fetch(`${API_URL}?&id=${id}`);
  const log = await res.json();

  document.getElementById("log_id").value = log.id ?? "";
  document.getElementById("log_admin").value = log.admin_name ?? "";
  document.getElementById("log_action").value = log.action ?? "";
  document.getElementById("log_target").value = log.target_id ?? "";
  document.getElementById("log_target_type").value = log.target_type ?? "";
  document.getElementById("log_target_name").value = log.target_name ?? "";
  document.getElementById("log_target_role").value = log.target_role ?? "";
  document.getElementById("log_ip").value = log.ip_address ?? "";
  document.getElementById("log_agent").value = log.user_agent ?? "";
  document.getElementById("log_time").value = formatDate(log.created_at);

  document.getElementById("modalViewLog").style.display = "flex";
}

function closeModal() {
  document.getElementById("modalViewLog").style.display = "none";
}
const modalViewLog = document.getElementById("modalViewLog");
window.addEventListener("click", function (e) {
  if (e.target == modalViewLog) {
    closeModal();
  }
});

/* ================= FILTER ================= */
// document.addEventListener("DOMContentLoaded", function () {

//     // Submit filter
//     const form = document.querySelector(".filter-form");
//     if (form) {
//         form.addEventListener("submit", function (e) {
//             e.preventDefault();
//             loadLogs(1);
//         });
//     }

//     // Close modal
//     document.querySelectorAll(".close-log").forEach(btn => {
//         btn.addEventListener("click", closeModal);
//     });

//     // Load lần đầu
//     loadLogs();
// });
document.getElementById("filterForm").addEventListener("submit", function (e) {
  e.preventDefault();

  currentKeyword = this.keyword.value;
  currentAction = this.action.value;
  currentRole = this.target_role.value;
  currentDateFrom = this.date_from.value;
  currentDateTo = this.date_to.value;

  loadLogs(1);
});

function resetFilter() {
  document.getElementById("filterForm").reset();

  currentKeyword = "";
  currentAction = "";
  currentRole = "";
  currentDateFrom = "";
  currentDateTo = "";

  loadLogs(1);
}
document.querySelectorAll(".close-log").forEach((btn) => {
  btn.addEventListener("click", closeModal);
});
document.addEventListener("DOMContentLoaded", () => {
  loadLogs();
});
let exportUrl = "";
function exportExcel() {
  if (!currentDateFrom || !currentDateTo) {
      alert("Vui lòng chọn khoảng thời gian trước khi xuất log");
      return;
  }
  //     const isConfirm = confirm("Bạn có chắc chắn muốn xuất log không?");

  // if (!isConfirm) {
  //     return; // người dùng bấm Hủy
  // }

  exportUrl =
    `/public/js/api/Admin/activity_log_api.php?export=1` +
    `&keyword=${encodeURIComponent(currentKeyword)}` +
    `&action=${encodeURIComponent(currentAction)}` +
    `&target_role=${encodeURIComponent(currentRole)}` +
    `&date_from=${encodeURIComponent(currentDateFrom)}` +
    `&date_to=${encodeURIComponent(currentDateTo)}`;

  document.getElementById("confirmText").innerHTML =
    `Bạn có chắc chắn muốn xuất log ?`;

  document.getElementById("confirmModal").style.display = "flex";
}
function closeConfirm() {
    document.getElementById("confirmModal").style.display = "none";
}

function confirmExport() {
    closeConfirm();
    window.location.href = exportUrl;
}