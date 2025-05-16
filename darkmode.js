// Detectar el botón y almacenar la preferencia
const toggleButton = document.getElementById("dark-mode-toggle");
const body = document.body;

// Cargar la preferencia del modo oscuro almacenado
if (localStorage.getItem("dark-mode") === "enabled") {
    body.classList.add("dark-mode");
    toggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Ícono de sol cuando está activado
} else {
    toggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Ícono de luna cuando está desactivado
}

// Alternar el modo oscuro
toggleButton.addEventListener("click", () => {
    body.classList.toggle("dark-mode");
    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("dark-mode", "enabled");
        toggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Cambio de ícono a sol
    } else {
        localStorage.setItem("dark-mode", "disabled");
        toggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Cambio de ícono a luna
    }
});
