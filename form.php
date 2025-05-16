<?php
// Conexion con la base de datos
$servername = "192.168.33.11"; 
$username = "root";        
$password = "agl123";            
$dbname = "preitv";          
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Comprobar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar'])) { 
    // Recibir los datos del formulario
    $nombre = isset($_REQUEST['nombre']) ? $conn->real_escape_string($_REQUEST['nombre']) : null;
    $apellidos = isset($_REQUEST['apellidos']) ? $conn->real_escape_string($_REQUEST['apellidos']) : null;
    $matricula = isset($_REQUEST['matricula']) ? $conn->real_escape_string($_REQUEST['matricula']) : null;
    $servicio = isset($_REQUEST['servicio']) ? $conn->real_escape_string($_REQUEST['servicio']) : null;
    $marca = isset($_REQUEST['marca']) ? $conn->real_escape_string($_REQUEST['marca']) : null;
    $modelo = isset($_REQUEST['modelo']) ? $conn->real_escape_string($_REQUEST['modelo']) : null;
    $fecha = isset($_REQUEST['fecha']) ? $conn->real_escape_string($_REQUEST['fecha']) : null;
    $hora = isset($_REQUEST['hora']) ? $conn->real_escape_string($_REQUEST['hora']) : null;

    if ($nombre && $apellidos && $matricula && $servicio && $marca && $modelo && $fecha && $hora) {
        $conn->begin_transaction();

        try {
            // Comprobar si la matrícula ya tiene una cita
            $sql_check_user = "SELECT COUNT(*) AS total FROM citas WHERE Matricula = '$matricula'";
            $result_user = $conn->query($sql_check_user);
            if ($result_user->fetch_assoc()['total'] > 0) {
                throw new Exception("La matrícula $matricula ya tiene una cita agendada.");
            }

            // Verificar si la fecha y hora existen en fechas_disponibles
            $sql_check_fecha = "SELECT idfecha FROM fechas_disponibles WHERE fecha = '$fecha' AND hora = '$hora'";
            $result_fecha = $conn->query($sql_check_fecha);
            if ($result_fecha->num_rows > 0) {
                $id_fecha_hora = $result_fecha->fetch_assoc()['idfecha'];
            } else {
                // Insertar nueva fecha y hora
                $sql_insert_fecha = "INSERT INTO fechas_disponibles (fecha, hora) VALUES ('$fecha', '$hora')";
                if (!$conn->query($sql_insert_fecha)) {
                    throw new Exception("Error al insertar la nueva fecha y hora: " . $conn->error);
                }
                $id_fecha_hora = $conn->insert_id;
            }

            // Obtener ID de marca
            $sql_marca = "SELECT idMarca FROM marcas WHERE Nombre_marca = '$marca'";
            $result_marca = $conn->query($sql_marca);
            if ($result_marca->num_rows > 0) {
                $idmarca = $result_marca->fetch_assoc()['idMarca'];
            } else {
                throw new Exception("La marca $marca no existe.");
            }

            // Obtener ID de modelo
            $sql_modelo = "SELECT idModelo FROM modelos WHERE Nombre = '$modelo' AND idMarca = '$idmarca'";
            $result_modelo = $conn->query($sql_modelo);
            if ($result_modelo->num_rows > 0) {
                $idmodelo = $result_modelo->fetch_assoc()['idModelo'];
            } else {
                throw new Exception("El modelo $modelo no existe para la marca seleccionada.");
            }

            // Insertar en clientes si no existe
            $sql_cliente = "SELECT idcliente FROM clientes WHERE Matricula = '$matricula'";
            $result_cliente = $conn->query($sql_cliente);
            if ($result_cliente->num_rows > 0) {
                $idcliente = $result_cliente->fetch_assoc()['idcliente'];
            } else {
                $sql_insert_cliente = "INSERT INTO clientes (Nombre, Apellidos, Matricula) VALUES ('$nombre', '$apellidos', '$matricula')";
                if (!$conn->query($sql_insert_cliente)) {
                    throw new Exception("Error al insertar cliente: " . $conn->error);
                }
                $idcliente = $conn->insert_id;
            }

           // Generar ID manual para la tabla citas
            $sql_max_id = "SELECT MAX(id_cita) AS max_id FROM citas";
            $result_max_id = $conn->query($sql_max_id);
            $next_id = 1; // Valor inicial en caso de que no existan registros
            if ($result_max_id && $result_max_id->num_rows > 0) {
                $row = $result_max_id->fetch_assoc();
                $max_id = (int)($row['max_id'] ?? 0); // Asegura que max_id sea un número entero
                $next_id = $max_id + 1;
            }
            // Insertar en citas
            $sql_insert_cita = "INSERT INTO citas (id_cita, Matricula, IdMarca, IdModelo, Id_fecha_hora) VALUES ($next_id, '$matricula', '$idmarca', '$idmodelo', '$id_fecha_hora')";
            if (!$conn->query($sql_insert_cita)) {
                throw new Exception("Error al insertar cita: " . $conn->error);
            }

            $conn->commit();
            echo "<p>Cita registrada exitosamente.</p>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Todos los campos son obligatorios.</p>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=/css/FormularioReserva.css>
    <title>Datos recopilados</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, Helvetica, sans-serif;
            color: black;
            text-align: center;
        }
        .button-bar {
            background-color:  #0056b3;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
            margin: 0 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .button-bar button {
            background-color: #f5a000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button-bar button:hover {
            background-color: #f5a000;
        }
        .data-container {
            margin: 20px auto;
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 8px rgba(0, 0, 0, 0.2);
            width: 60%;
            border:solid, 1px;
            border-color: #f5a000;
        }
        .data-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .data-container p {
            margin: 10px 0;
            font-size: 25px;
            text-align: justify;
        }
        #ITVimg {
        width: 100px;
        height: auto; /* Ajusta la proporción automáticamente */
        margin: 10px;
        }
        li {
        list-style: none;
        }
    </style>
</head>
<body>
    <section class="button-bar">
        <button onclick="window.history.back();">Volver</button>
        <li><a id="ITVimg" href="index.html"><img src="Imagenes/PreITVlogo.jpg" id="ITVimg"></a></li>
        <form method="POST">
            <button type="submit" name="finalizar">Finalizar</button>
        </form>
    </section>        
    <section class="data-container">
        <h1>¿Son correctos estos datos?</h1>
        <p><strong>Nombre:</strong> <?php echo isset($_REQUEST['nombre']) ? htmlspecialchars($_REQUEST['nombre']) : 'No ingresado'; ?></p>
        <p><strong>Apellidos:</strong> <?php echo isset($_REQUEST['apellidos']) ? htmlspecialchars($_REQUEST['apellidos']) : 'No ingresado'; ?></p>
        <p><strong>Matrícula:</strong> <?php echo isset($_REQUEST['matricula']) ? htmlspecialchars($_REQUEST['matricula']) : 'No ingresado'; ?></p>
        <p><strong>Servicio:</strong> <?php echo isset($_REQUEST['servicio']) ? htmlspecialchars($_REQUEST['servicio']) : 'No ingresado'; ?></p>
        <p><strong>Marca:</strong> <?php echo isset($_REQUEST['marca']) ? htmlspecialchars($_REQUEST['marca']) : 'No ingresado'; ?></p>
        <p><strong>Modelo:</strong> <?php echo isset($_REQUEST['modelo']) ? htmlspecialchars($_REQUEST['modelo']) : 'No ingresado'; ?></p>
        <p><strong>Fecha:</strong> <?php echo isset($_REQUEST['fecha']) ? htmlspecialchars($_REQUEST['fecha']) : 'No ingresada'; ?></p>
        <p><strong>Hora:</strong> <?php echo isset($_REQUEST['hora']) ? htmlspecialchars($_REQUEST['hora']) : 'No ingresada'; ?></p>
        
    </section>
</body>
</html>
