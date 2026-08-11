function showPassword() {
    let password = document.getElementById("password");
    password.type = password.type === "password" ? "text" : "password";
}

function toggleMenu() {
    let menu = document.getElementById("menu");
    menu.classList.toggle("show");
}

function validateForm() {
    let inputs = document.querySelectorAll("input[required]");

    for (let input of inputs) {
        if (input.value.trim() === "") {
            alert("Please fill all required fields");
            input.focus();
            return false;
        }
    }

    return true;
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this?");
}

function showMessage(message) {
    alert(message);
}

function logout() {
    if (confirm("Do you want to logout?")) {
        window.location.href = "../logout.php";
    }
}