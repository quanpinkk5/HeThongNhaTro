document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const roomId = urlParams.get("id");
  const API_URL = "/public/js/api/customer/room_detail_handler.php";

  if (!roomId) return;

  fetch(`${API_URL}?id=${roomId}`)
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        const d = res.data;
        const info = d.info;

        document.getElementById("room_title").innerText = info.title;
        document.getElementById("room_address").innerText = info.address;
        document.getElementById("room_area").innerText = info.area;
        document.getElementById("room_price").innerText =
          new Intl.NumberFormat("vi-VN").format(info.price) + "đ";
        document.getElementById("room_desc").innerHTML = (
          info.description || "Chưa có mô tả"
        ).replace(/\n/g, "<br>");
        document.getElementById("owner_name").innerText = info.owner_name;
        document.getElementById("owner_phone").innerText =
          info.owner_phone || "Chưa cập nhật";

        let priceDien = 0,
          priceNuoc = 0;
        const otherSvcContainer = document.getElementById(
          "other_services_list",
        );
        otherSvcContainer.innerHTML = "";

        d.services.forEach((svc) => {
          const name = svc.name.toLowerCase();
          if (name.includes("điện")) priceDien = svc.price;
          else if (name.includes("nước")) priceNuoc = svc.price;
          else {
            otherSvcContainer.innerHTML += `<div class="amenity"><i class="fas fa-check-circle"></i><span>${svc.name}</span></div>`;
          }
        });

        document.getElementById("price_dien").innerText = new Intl.NumberFormat(
          "vi-VN",
        ).format(priceDien);
        document.getElementById("price_nuoc").innerText = new Intl.NumberFormat(
          "vi-VN",
        ).format(priceNuoc);

        if (d.images && d.images.length > 0) {
          const mainImg = document.getElementById("view_main");
          mainImg.src = resolveImageUrl(d.images[0].image_url);
          mainImg.onerror = () => {
            mainImg.src = getImageFallback();
          };

          const thumbContainer = document.getElementById("thumb_list");
          thumbContainer.innerHTML = "";
          d.images.forEach((img) => {
            const thumb = document.createElement("div");
            thumb.className = "thumb-item";
            const thumbUrl = resolveImageUrl(img.image_url);
            thumb.innerHTML = `<img src="${thumbUrl}" onerror="this.src='${getImageFallback()}'">`;
            thumb.onclick = () => (mainImg.src = thumbUrl);
            thumbContainer.appendChild(thumb);
          });
        }

        const badge = document.getElementById("status_badge");
        const action = document.getElementById("booking_action");
        const bookingAction = document.getElementById("booking_action");
        const urlParams = new URLSearchParams(window.location.search);
        const currentRoomId = urlParams.get("id");

        bookingAction.innerHTML = `
                    <button class="btn-rent" onclick="sendRentRequest(${currentRoomId})">
                        <i class="fas fa-paper-plane"></i> Gửi yêu cầu thuê phòng
                    </button>
                `;
        if (info.status_room === "EMPTY") {
          badge.className = "room-status available";
          badge.innerHTML = `<i class="fas fa-check-circle"></i> Phòng đang còn trống`;
          action.innerHTML = `
                        <button type="button" class="btn-primary-action" onclick="sendRentRequest(${info.id})">
                            YÊU CẦU THUÊ PHÒNG NGAY
                        </button>
                    `;
        } else {
          badge.className = "room-status occupied";
          badge.innerHTML = `<i class="fas fa-times-circle"></i> Phòng đã được thuê`;
          action.innerHTML = "";
        }

        document.getElementById("loading").style.display = "none";
        document.getElementById("main-content").style.display = "grid";
      }
    })
    .catch((err) => {
      console.error("Lỗi:", err);
      document.getElementById("loading").innerText =
        "Không thể tải dữ liệu phòng.";
    });
});

function resolveImageUrl(imageUrl) {
  if (!imageUrl) return getImageFallback();
  if (/^https?:\/\//i.test(imageUrl) || imageUrl.startsWith("data:")) return imageUrl;
  if (imageUrl.startsWith("/")) return imageUrl;
  return `/public/images/${imageUrl}`;
}

function getImageFallback() {
  return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='1200' height='800'><rect width='100%25' height='100%25' fill='%23e2e8f0'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2364758b' font-size='40' font-family='Arial'>No Image</text></svg>";
}
