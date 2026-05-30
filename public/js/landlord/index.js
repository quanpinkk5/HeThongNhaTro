document.addEventListener("DOMContentLoaded", async function () {
  console.log("Đang tải dữ liệu Dashboard...");

  try {
    const data = await DashboardAPI.getDashboardData();
    if (data.status === "success") {
      renderStats(data.stats);
      renderLists(data.lists);
      renderCharts(data.charts, data.stats);
    } else {
      console.error("Lỗi lấy dữ liệu:", data.message);
    }
  } catch (err) {
    console.error("Lỗi kết nối API Dashboard:", err);
  }
});

// ==== HÀM TIỆN ÍCH ====
function formatMoney(num) {
  return new Intl.NumberFormat("vi-VN").format(num);
}

// ==== RENDER GIAO DIỆN ====

// 1. Cập nhật 4 khối thống kê trên cùng
function renderStats(stats) {
  document.getElementById("stat-total-rooms").innerText = stats.total_rooms;
  document.getElementById("stat-rented-rooms").innerText = stats.rented_rooms;
  document.getElementById("stat-empty-rooms").innerText = stats.empty_rooms;
  document.getElementById("stat-total-debt").innerText =
    formatMoney(stats.total_debt) + " đ";
}

// 2. Cập nhật 2 danh sách bên dưới
function renderLists(lists) {
  // Hóa đơn nợ
  const unpaidContainer = document.getElementById("unpaid-invoices-list");
  if (lists.unpaid_invoices.length > 0) {
    let html = "<ul>";
    lists.unpaid_invoices.forEach((inv) => {
      html += `<li>
                        <span class="room-name">${inv.room_name}</span>
                        <span class="invoice-info">Tháng ${inv.month}/${inv.year}</span>
                        <strong class="amount">${formatMoney(inv.total)} đ</strong>
                    </li>`;
    });
    html += "</ul>";
    unpaidContainer.innerHTML = html;
  } else {
    unpaidContainer.innerHTML =
      '<p class="empty-state">Không có hóa đơn nợ.</p>';
  }

  // Hợp đồng sắp hết hạn
  const expiringContainer = document.getElementById("expiring-contracts-list");
  if (lists.expiring_contracts.length > 0) {
    let html = "<ul>";
    lists.expiring_contracts.forEach((con) => {
      html += `<li>
                        <span class="tenant-name">${con.tenant_name}</span>
                        <span class="room-name-small"> - ${con.room_name}</span>
                        <span class="days-left badge-warning">Còn ${con.days_left} ngày</span>
                    </li>`;
    });
    html += "</ul>";
    expiringContainer.innerHTML = html;
  } else {
    expiringContainer.innerHTML =
      '<p class="empty-state">Không có hợp đồng nào sắp hết hạn trong 30 ngày tới.</p>';
  }
}

// 3. Vẽ biểu đồ Chart.js
function renderCharts(charts, stats) {
  // Biểu đồ cột Doanh thu
  const ctxRevenue = document.getElementById("revenueChart").getContext("2d");
  new Chart(ctxRevenue, {
    type: "bar",
    data: {
      labels: charts.revenue.labels,
      datasets: [
        {
          label: "Doanh thu (VNĐ)",
          data: charts.revenue.values,
          backgroundColor: "rgba(60, 141, 188, 0.7)",
          borderColor: "rgba(60, 141, 188, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => formatMoney(value) + " đ",
          },
        },
      },
    },
  });

  // Biểu đồ tròn Lấp đầy
  const ctxOccupancy = document
    .getElementById("occupancyChart")
    .getContext("2d");
  new Chart(ctxOccupancy, {
    type: "doughnut",
    data: {
      labels: ["Đang thuê", "Phòng trống"],
      datasets: [
        {
          data: [stats.rented_rooms, stats.empty_rooms],
          backgroundColor: ["#00a65a", "#f39c12"],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: "bottom" },
      },
    },
  });
}
