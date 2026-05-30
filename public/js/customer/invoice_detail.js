document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const invoiceId = urlParams.get('id');

    if (!invoiceId) {
        alert("Không tìm thấy mã hóa đơn!");
        return;
    }

    fetch(`../../../public/js/api/customer/invoice_detail_handler.php?id=${invoiceId}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                const invoice = res.data.invoice;
                const items = res.data.items;

                document.getElementById('inv_code').innerText = `#INV-${invoice.id.toString().padStart(4, '0')}`;
                document.getElementById('inv_period').innerText = `Tháng ${invoice.month}/${invoice.year}`;
                
                const statusBadge = document.getElementById('inv_status');
                statusBadge.innerText = invoice.status === 'PAID' ? 'Đã thanh toán' : 'Chưa thanh toán';
                statusBadge.className = 'status-badge ' + (invoice.status === 'PAID' ? 'paid' : 'unpaid');

                document.getElementById('tenant_name').innerText = invoice.tenant_name;
                document.getElementById('tenant_phone').innerText = invoice.tenant_phone;
                document.getElementById('tenant_email').innerText = invoice.tenant_email;
                document.getElementById('building_name').innerText = invoice.building_name;
                document.getElementById('room_name').innerText = invoice.room_name;
                document.getElementById('building_address').innerText = invoice.building_address;
                document.getElementById('created_at').innerText = new Date(invoice.created_at).toLocaleDateString('vi-VN');

                let itemsHtml = '';
                items.forEach((item, index) => {
                    itemsHtml += `
                        <tr>
                            <td style="text-align:center;">${index + 1}</td>
                            <td>${item.description}</td>
                            <td class="text-right">${new Intl.NumberFormat('vi-VN').format(item.unit_price)}đ</td>
                            <td class="text-right">${item.quantity}</td>
                            <td class="text-right highlight">${new Intl.NumberFormat('vi-VN').format(item.amount)}đ</td>
                        </tr>`;
                });
                
                document.getElementById('invoice_items_body').innerHTML = itemsHtml;
                document.getElementById('total_amount').innerText = new Intl.NumberFormat('vi-VN').format(invoice.total) + 'đ';
            } else {
                document.getElementById('invoice_items_body').innerHTML = `<tr><td colspan="5" style="text-align:center; color:red;">${res.message}</td></tr>`;
            }
        })
        .catch(err => {
            console.error("Lỗi kết nối API:", err);
            document.getElementById('invoice_items_body').innerHTML = `<tr><td colspan="5" style="text-align:center; color:red;">Lỗi kết nối hệ thống.</td></tr>`;
        });
});