<?php
// insertar_usuarios.php
$conn = new mysqli("localhost", "root", "", "PreITV");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$usuarios = [
    ['usuario' => 'vgutierrezc', 'password' => 'agl123_'],
    ['usuario' => 'sleuzau', 'password' => 'agl123_'],
    ['usuario' => 'dhayal', 'password' => 'agl123_'],
    // Añade más usuarios si es necesario
];

foreach ($usuarios as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $user['usuario'], $hash);
    $stmt->execute();
}

echo "Usuarios creados con contraseñas encriptadas!";
$conn->close();
?>