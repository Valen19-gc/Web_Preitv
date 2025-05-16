<?php
// citas.php

// 1. Conexión a la base de datos
$servername = "192.168.33.11";
$username   = "root";
$password   = "agl123";
$dbname     = "preitv";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 2. Consulta de citas con JOIN entre tablas relacionadas
$sql = <<<SQL
SELECT
    fd.fecha AS fecha,
    fd.hora AS hora,
    cl.Nombre AS nombre,
    cl.Apellidos AS apellidos,
    cl.Matricula AS matricula,
    mk.Nombre_marca AS marca,
    md.Nombre AS modelo
FROM citas ct
JOIN fechas_disponibles fd ON ct.Id_fecha_hora = fd.idfecha
JOIN clientes cl           ON ct.Matricula = cl.Matricula
JOIN marcas mk             ON ct.IdMarca = mk.idMarca
JOIN modelos md            ON ct.IdMarca = md.idMarca AND ct.IdModelo = md.idModelo
ORDER BY fd.fecha, fd.hora;
SQL;

$result = $conn->query($sql); $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas - Panel Admin PRE-ITV</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; }
        header { background: #004aad; color: #fff; padding: 10px 20px; display: flex; align-items: center; }
        header img { height: 50px; margin-right: 20px; }
        nav a { color: #fff; margin-right: 15px; text-decoration: none; font-weight: bold; }
        .container { max-width: 1200px; margin: 20px auto; background: #fff;
                     box-shadow: 0 2px 6px rgba(0,0,0,0.2); padding: 20px; }
        h1 { color: #004aad; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        tr:nth-child(even) { background: #fafafa; }
        .no-data { text-align: center; color: #777; }
    </style>
</head>
<body>
    <header>
        <img src="Imagenes\PreITVlogo.jpg" alt="Logo PRE-ITV">
        <nav>
            <a href="index.html">Inicio</a>
            <a href="admin_panel.php">Admin</a>
            <a href="formulario_preitv.html">Nuevo Informe</a>
        </nav>
    </header>
    <div class="container">
        <h1>Listado de Citas</h1>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Matrícula</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['fecha']) ?></td>
                            <td><?= htmlspecialchars($row['hora']) ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['apellidos']) ?></td>
                            <td><?= htmlspecialchars($row['matricula']) ?></td>
                            <td><?= htmlspecialchars($row['marca']) ?></td>
                            <td><?= htmlspecialchars($row['modelo']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td class="no-data" colspan="7">No hay citas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$conn->close();
?>
