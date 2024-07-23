<?php
require_once(__DIR__ . '/../../config/config.php');
session_start();

if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

$tra_rut_usuario = $_SESSION['rut'];

// Obtener la conexión a la base de datos
$conn = getDBConnection();

if (!$conn) {
    die("Error en la conexión a la base de datos: " . pg_last_error());
}

// Verificar si se proporciona el parámetro 'num_cotizacion' en la URL
$num_cotizacion = isset($_GET['num_cotizacion']) ? $_GET['num_cotizacion'] : null;
if (!$num_cotizacion || !filter_var($num_cotizacion, FILTER_VALIDATE_INT)) {
    die("ID de cotización no proporcionado o no válido. URL: " . $_SERVER['REQUEST_URI']);
}

// Obtener datos de la cotización existente
$query = "SELECT * FROM cotizacion WHERE num_cotizacion = $1";
$result = pg_query_params($conn, $query, array($num_cotizacion));

if (!$result || pg_num_rows($result) == 0) {
    die("Cotización no encontrada: " . pg_last_error());
}

$cotizacion = pg_fetch_assoc($result);

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $tra_rut = $_POST['tra_rut'];
    $fecha_cotizacion = $_POST['fecha_cotizacion'];
    $monto_total = $_POST['monto_total'];
    $descripcion_cotizacion = $_POST['descripcion_cotizacion'];
    $estado_cotizacion = 'true'; // Estado de la cotización siempre será true

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Actualizar cotización
    $query = "UPDATE cotizacion SET rut = $1, tra_rut = $2, fecha_cotizacion = $3, monto_total = $4, descripcion_cotizacion = $5, estado_cotizacion = $6 WHERE num_cotizacion = $7";
    $result = pg_query_params($conn, $query, array($rut, $tra_rut, $fecha_cotizacion, $monto_total, $descripcion_cotizacion, $estado_cotizacion, $num_cotizacion));

    if ($result) {
        // Confirmar transacción
        pg_query($conn, 'COMMIT');
        // Redirigir a la lista de cotizaciones
        header("Location: ../Lista de Cotizaciones/ver_cotizaciones.php");
        exit();
    } else {
        // Error en la actualización, deshacer transacción
        pg_query($conn, 'ROLLBACK');
        echo "Error al actualizar la cotización: " . pg_last_error($conn);
    }
}

// Cerrar la conexión
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Cotización</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Actualizar Cotización</h2>
    <form method="post" action="">
        <label for="rut">RUT del Cliente:</label>
        <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($cotizacion['rut']); ?>" readonly>

        <label for="tra_rut">RUT del Trabajador:</label>
        <input type="text" id="tra_rut" name="tra_rut" value="<?php echo htmlspecialchars($cotizacion['tra_rut']); ?>" readonly>

        <label for="fecha_cotizacion">Fecha de Cotización:</label>
        <input type="date" id="fecha_cotizacion" name="fecha_cotizacion" value="<?php echo htmlspecialchars($cotizacion['fecha_cotizacion']); ?>" required>

        <label for="monto_total">Monto Total:</label>
        <input type="number" id="monto_total" name="monto_total" value="<?php echo htmlspecialchars($cotizacion['monto_total']); ?>" step="0.01" required>

        <label for="descripcion_cotizacion">Descripción:</label>
        <textarea id="descripcion_cotizacion" name="descripcion_cotizacion" required><?php echo htmlspecialchars($cotizacion['descripcion_cotizacion']); ?></textarea>

        <button type="submit">Actualizar Cotización</button>
    </form>
</body>
</html>

