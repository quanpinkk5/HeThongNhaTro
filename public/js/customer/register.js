document.getElementById("registerForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const msgBox = document.getElementById("messageBox");

    try {
        const res = await fetch("/public/js/api/customer/api_register.php", {
            method: "POST",
            body: formData
        });
        const data = await res.json();

        if (data.status === "success") {
            alert("Đăng ký thành công!");
            window.location.href = "login.php";
        } else {
            msgBox.style.display = "block";
            msgBox.className = "alert error";
            msgBox.innerText = data.message;
        }
    } catch (err) {
        console.error("Lỗi:", err);
    }
});