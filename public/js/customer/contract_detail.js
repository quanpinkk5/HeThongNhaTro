document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('id');
    const container = document.getElementById('contract-content');

    if (!contractId) {
        container.innerHTML = "Mã hợp đồng không hợp lệ.";
        return;
    }

    fetch(`../../../public/js/api/customer/get_contract_detail.php?id=${contractId}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                const c = res.data.contract;
                const services = res.data.services;
                const template = document.getElementById('contract-template').content.cloneNode(true);

                // Điền dữ liệu
                template.querySelector('.contract-code').innerText = `Mã hợp đồng: #HD-${c.id}`;
                template.getElementById('landlord_name').innerHTML = `<strong>Họ tên:</strong> ${c.landlord_name}`;
                template.getElementById('landlord_phone').innerHTML = `<strong>SĐT:</strong> ${c.landlord_phone}`;
                template.getElementById('landlord_email').innerHTML = `<strong>Email:</strong> ${c.landlord_email}`;
                
                template.getElementById('tenant_name').innerHTML = `<strong>Họ tên:</strong> ${c.tenant_name}`;
                template.getElementById('tenant_phone').innerHTML = `<strong>SĐT:</strong> ${c.tenant_phone}`;
                template.getElementById('tenant_cccd').innerHTML = `<strong>CCCD:</strong> ${c.tenant_cccd}`;

                template.getElementById('room_name').innerHTML = `<strong>Phòng:</strong> ${c.room_name}`;
                template.getElementById('room_area').innerHTML = `<strong>Diện tích:</strong> ${c.area} m²`;
                template.getElementById('room_price').innerHTML = `<strong>Giá thuê:</strong> ${new Intl.NumberFormat('vi-VN').format(c.room_price)} đ/tháng`;
                template.getElementById('room_deposit').innerHTML = `<strong>Tiền cọc:</strong> ${new Intl.NumberFormat('vi-VN').format(c.deposit)} đ`;
                template.getElementById('room_address').innerHTML = `<strong>Địa chỉ:</strong> ${c.address}`;

                template.getElementById('contract_time').innerText = `Từ ngày ${new Date(c.start_date).toLocaleDateString('vi-VN')} đến ngày ${c.end_date ? new Date(c.end_date).toLocaleDateString('vi-VN') : 'khi có thông báo mới'}.`;

                let sHtml = '';
                services.forEach(s => {
                    let calc = s.calculation === 'PER_ROOM' ? 'Theo phòng' : s.calculation === 'PER_PERSON' ? 'Theo người' : 'Theo chỉ số';
                    sHtml += `<tr><td>${s.name}</td><td>${new Intl.NumberFormat('vi-VN').format(s.price)}đ / ${s.unit}</td><td>${calc}</td></tr>`;
                });
                template.getElementById('service-list').innerHTML = sHtml || '<tr><td colspan="3">Không có dịch vụ đi kèm</td></tr>';
                
                template.getElementById('sig_landlord').innerText = c.landlord_name;
                template.getElementById('sig_tenant').innerText = c.tenant_name;

                container.innerHTML = '';
                container.appendChild(template);
            } else {
                container.innerHTML = "Không tìm thấy thông tin hợp đồng.";
            }
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = "Lỗi hệ thống.";
        });
});