const API_URL = '/public/js/api/customer/api_profile.php';

document.getElementById('formUpdateName').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const res = await fetch(API_URL, { method: 'POST', body: formData });
        const data = await res.json();
        
        alert(data.message);
        if(data.status === 'success') {
            document.getElementById('display-name').innerText = data.new_name;
            document.querySelector('.avatar-text').innerText = data.new_name.charAt(0).toUpperCase();
        }
    } catch (err) { console.error("Lỗi:", err); }
});

document.getElementById('formChangePassword').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const res = await fetch(API_URL, { method: 'POST', body: formData });
        const data = await res.json();
        
        alert(data.message);
        if(data.status === 'success') this.reset();
    } catch (err) { console.error("Lỗi:", err); }
});