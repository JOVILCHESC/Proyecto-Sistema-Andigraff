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
    die("Error en la conexión a la base de datos");
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $tra_rut = $_POST['tra_rut'];
    $fecha_cotizacion = $_POST['fecha_cotizacion'];
    $monto_total = $_POST['monto_total'];
    $descripcion_cotizacion = $_POST['descripcion_cotizacion'];
    $estado_cotizacion = 'true'; // Estado de la cotización siempre será true
    $productos = $_POST['productos'];
    $cantidades = $_POST['cantidades'];

    // Calcular la cantidad total de productos
    $cantidad_total = array_sum($cantidades);

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Insertar cotización
    $query = "INSERT INTO cotizacion (rut, tra_rut, fecha_cotizacion, monto_total, cantidad, descripcion_cotizacion, estado_cotizacion) 
              VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING num_cotizacion";
    $result = pg_query_params($conn, $query, array($rut, $tra_rut, $fecha_cotizacion, $monto_total, $cantidad_total, $descripcion_cotizacion, $estado_cotizacion));

    if ($result) {
        $row = pg_fetch_assoc($result);
        $num_cotizacion = $row['num_cotizacion'];

        // Insertar productos en tiene2
        foreach ($productos as $index => $cod_producto) {
            $queryProducto = "INSERT INTO tiene2 (cod_producto, num_cotizacion) VALUES ($1, $2)";
            $resultProducto = pg_query_params($conn, $queryProducto, array($cod_producto, $num_cotizacion));
            if (!$resultProducto) {
                // Error en la inserción, deshacer transacción
                pg_query($conn, 'ROLLBACK');
                die("Error al registrar productos de la cotización: " . pg_last_error($conn));
            }
        }

        // Confirmar transacción
        pg_query($conn, 'COMMIT');
        echo "Cotización registrada exitosamente.";
    } else {
        // Error en la inserción, deshacer transacción
        pg_query($conn, 'ROLLBACK');
        echo "Error al registrar la cotización: " . pg_last_error($conn);
    }
}

// Cerrar la conexión
pg_close($conn);
?>
