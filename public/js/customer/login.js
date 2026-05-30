document.getElementById("loginForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const msgBox = document.getElementById("messageBox");

    try {
        const res = await fetch("../../../public/js/api/customer/api_login.php", {
            method: "POST",
            body: formData
        });
        const data = await res.json();

        if (data.status === "success") {
            switch(data.role) {
                case 'ADMIN':
                    window.location.href = "../../../app/views/admin/quanlytaikhoan.php";
                    break;
                case 'LANDLORD':
                    window.location.href = "../../../app/views/landlord/index.php";
                    break;
                default:
                    window.location.href = "../../../app/views/customer/index.php";
            }
        } else {
            msgBox.style.display = "block";
            msgBox.className = "alert error";
            msgBox.innerText = data.message;
        }
    } catch (err) {
        console.error("Lỗi:", err);
    }
});