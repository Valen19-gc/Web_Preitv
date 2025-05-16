<?php
session_start();

$conn = new mysqli("192.168.33.11", "root", "agl123", "preitv");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

// Consulta corregida sin 'contraseña'
$stmt = $conn->prepare("SELECT password_hash FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['usuario'] = $username;
        header("Location: admin_panel.php");
        exit();
    }
}

echo "Credenciales incorrectas";
$conn->close();
?>
