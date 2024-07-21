<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Bodega</title>
    <link rel="stylesheet" href="../styles/listado_sucursales.css"> 
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilo básico para el formulario de actualización */
        form {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input {
            width: calc(100% - 22px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Obtener el código de establecimiento de la URL
$cod_establecimiento = isset($_GET['cod_establecimiento']) ? pg_escape_string($conn, $_GET['cod_establecimiento']) : '';

// Obtener los datos de la bodega para el formulario
$sql = "SELECT * FROM bodega WHERE cod_establecimiento = '$cod_establecimiento'";
$result = pg_query($conn, $sql);
$bodega = pg_fetch_assoc($result);

// Cerrar la conexión a la base de datos
pg_close($conn);
?>

<h1>Actualizar Bodega</h1>

<form method="post" action="update_bodega.php">
    <input type="hidden" name="cod_establecimiento" value="<?php echo htmlspecialchars($bodega['cod_establecimiento']); ?>">

    <label for="telefono">Teléfono:</label>
    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($bodega['telefono']); ?>">

    <label for="numero_estableciimiento">Número Establecimiento:</label>
    <input type="text" id="numero_estableciimiento" name="numero_estableciimiento" value="<?php echo htmlspecialchars($bodega['numero_estableciimiento']); ?>">

    <label for="comuna_establecimiento">Comuna:</label>
    <input type="text" id="comuna_establecimiento" name="comuna_establecimiento" value="<?php echo htmlspecialchars($bodega['comuna_establecimiento']); ?>">

    <label for="calle_establecimiento">Calle:</label>
    <input type="text" id="calle_establecimiento" name="calle_establecimiento" value="<?php echo htmlspecialchars($bodega['calle_establecimiento']); ?>">

    <label for="ciudad_establecimiento">Ciudad:</label>
    <input type="text" id="ciudad_establecimiento" name="ciudad_establecimiento" value="<?php echo htmlspecialchars($bodega['ciudad_establecimiento']); ?>">

    <label for="nombre_establecimiento">Nombre:</label>
    <input type="text" id="nombre_establecimiento" name="nombre_establecimiento" value="<?php echo htmlspecialchars($bodega['nombre_establecimiento']); ?>">

    <label for="cant_empleados">Cantidad de Empleados:</label>
    <input type="text" id="cant_empleados" name="cant_empleados" value="<?php echo htmlspecialchars($bodega['cant_empleados']); ?>">

    <label for="capacidad">Capacidad:</label>
    <input type="text" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($bodega['capacidad']); ?>">

    <label for="tipo_almacenamiento">Tipo de Almacenamiento:</label>
    <input type="text" id="tipo_almacenamiento" name="tipo_almacenamiento" value="<?php echo htmlspecialchars($bodega['tipo_almacenamiento']); ?>">

    <label for="estado_bodega">Estado:</label>
    <input type="checkbox" id="estado_bodega" name="estado_bodega" <?php echo $bodega['estado_bodega'] ? 'checked' : ''; ?>>

    <button type="submit">Actualizar</button>
</form>
</body>
</html>