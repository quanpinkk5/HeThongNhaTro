async function sendRentRequest(roomId) {
    if (!confirm("Bạn có chắc chắn muốn gửi yêu cầu thuê phòng này?")) return;

    const formData = new FormData();
    formData.append('room_id', roomId);

    try {
        const res = await fetch('/public/js/api/customer/api_send_request.php', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();
        
        if (result.status === 'success') {
            alert(result.message);
            window.location.reload(); 
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error("Lỗi:", err);
        alert("Không thể kết nối với hệ thống API.");
    }
}