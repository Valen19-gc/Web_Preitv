<?php
// panel_admin.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - PRE-ITV</title>
    <style>
        :root {
            --font-family: Arial, sans-serif;
            --background-color: #ffffff;
            --text-color: #000000;
            --font-size-base: 16px;
            --spacing-small: 5px;
            --spacing-medium: 10px;
            --spacing-large: 20px;
            --border-radius: 4px;
            --secondary-color: #e08e00;
            --primary-color: #0056b3;
        }
        
        body { 
            margin: 0; 
            font-family: Arial, sans-serif; 
            background: #f4f4f4; 
        }
        
        header { 
            background: #0056b3; 
            color: #fff; 
            padding: 0; 
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }
        
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0 20px;
        }
        
        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            padding: 10px 15px;
        }
        
        nav a:hover {
            color: var(--secondary-color);
        }
        
        .logo {
            height: 40px;
            margin-right: 20px;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .admin-button {
            background-color: #FFA500;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }
        
        .admin-button:hover {
            background-color: #e08e00;
        }
        
        .container { 
            max-width: 1200px; 
            margin: 20px auto; 
            background: #fff; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.2); 
            padding: 20px; 
        }
        
        h1 { 
            color: #0056b3; 
        }
        
        .buttons { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            margin-top: 30px; 
        }
        
        .buttons button {
            flex: 1 1 calc(50% - 20px);
            padding: 20px;
            font-size: 1.1em;
            background: #0056b3;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .buttons button:hover { 
            background: #003080; 
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-links">
                <a href="index.html"><img src="Imagenes/PreITVlogo.jpg" class="logo" alt="PRE-ITV Logo"></a>
                <a href="index.html">Inicio</a>
                <a href="Form_reto.html">Cita Previa</a>
            </div>
            <div>
                <a href="login.html"><button class="admin-button">Admin</button></a>
            </div>
        </nav>
    </header>
    
    <div class="container">
        <h1>Panel de Administración</h1>
        <p>Bienvenido al panel de administración de PRE-ITV. Desde aquí podrás gestionar las principales funcionalidades de tu sitio.</p>
        <div class="buttons">
            <?php $hoy = date('Y-m-d'); ?>
            <button onclick="location.href='citas.php?fecha=<?php echo $hoy; ?>'">Ver citas del día</button>
            <button onclick="location.href='formulario_preitv1.html'">Nuevo informe ITV</button>
            <button onclick="location.href='listar_informes.php'">Listar informes</button>
            <button onclick="location.href='usuarios.php'">Gestión de usuarios</button>
        </div>
    </div>
</body>
</html>
