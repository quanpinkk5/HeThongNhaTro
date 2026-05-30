document.addEventListener('DOMContentLoaded', function() {
    const API_URL = '../../../public/js/api/customer/get_notifications.php';
    const listContainer = document.getElementById('notificationList');
    const btnMarkAll = document.getElementById('btnMarkAll');

    async function loadNotifications() {
        try {
            const res = await fetch(`${API_URL}?action=fetch`);
            
            const text = await res.text(); 
            console.log("Nội dung phản hồi thô:", text);
    
            const result = JSON.parse(text); 
            
            if (Array.isArray(result)) {
                renderUI(result);
            } else if (result.data) {
                renderUI(result.data);
            }
        } catch (err) {
            console.error("Lỗi Parse JSON:", err);
            listContainer.innerHTML = '<p style="text-align:center; padding:20px;">Lỗi cấu hình hệ thống.</p>';
        }
    }

    function renderUI(notifications) {
        if (notifications.length === 0) {
            listContainer.innerHTML = '<div class="empty-noti" style="text-align:center; padding:50px; color:#94a3b8;">Bạn chưa có thông báo nào.</div>';
            return;
        }

        listContainer.innerHTML = notifications.map(noti => {
            let iconClass = 'info'; 
            let faIcon = 'fa-info-circle';
            
            if (noti.type === 'INVOICE') { iconClass = 'alert'; faIcon = 'fa-file-invoice-dollar'; }
            else if (noti.type === 'REQUEST') { iconClass = 'success'; faIcon = 'fa-check-circle'; }
            else if (noti.type === 'MAINTENANCE') { iconClass = 'alert'; faIcon = 'fa-wrench'; }

            const unreadClass = noti.is_read == 0 ? 'unread' : '';
            const unreadDot = noti.is_read == 0 ? '<div class="unread-dot"></div>' : '';

            return `
                <div class="noti-item ${unreadClass}" 
                     onclick="handleReadAction(${noti.id})" 
                     style="cursor: pointer;">
                    <div class="noti-icon ${iconClass}">
                        <i class="fas ${faIcon}"></i>
                    </div>
                    <div class="noti-body">
                        <div class="noti-title">${escapeHtml(noti.title)}</div>
                        <div class="noti-text">${escapeHtml(noti.content)}</div>
                        <div class="noti-time">${formatTime(noti.created_at)}</div>
                    </div>
                    ${unreadDot}
                </div>
            `;
        }).join('');
    }

window.handleReadAction = async (id) => {
    try {
        const formData = new FormData();
        formData.append('read_id', id);
        
        // Gọi API với action=read
        const res = await fetch(`${API_URL}?action=read`, {
            method: 'POST',
            body: formData
        });
        const result = await res.json();
        loadNotifications();

    } catch (err) {
        console.error("Lỗi khi đọc thông báo:", err);
    }
};

if (btnMarkAll) {
    btnMarkAll.onclick = async () => {
        try {
            const res = await fetch(`${API_URL}?action=mark_all`, { 
                method: 'POST' 
            });
            const result = await res.json();
            
            if (result.status === 'success') {
                loadNotifications();
                if (typeof updateBadge === 'function') updateBadge(0);
            }
        } catch (err) {
            console.error("Lỗi:", err);
        }
    };
}

    function formatTime(datetime) {
        if (!datetime || datetime.startsWith('0000')) return "---";
        const d = new Date(datetime);
        const hours = d.getHours().toString().padStart(2, '0');
        const minutes = d.getMinutes().toString().padStart(2, '0');
        const day = d.getDate().toString().padStart(2, '0');
        const month = (d.getMonth() + 1).toString().padStart(2, '0');
        return `${hours}:${minutes} ${day}/${month}/${d.getFullYear()}`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    loadNotifications();
});
