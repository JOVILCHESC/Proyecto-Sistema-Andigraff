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

if (!$num_cotizacion) {
    die("ID de cotización no proporcionado. URL: " . $_SERVER['REQUEST_URI']);
}

// Obtener datos de la cotización existente
$query = "SELECT * FROM cotizacion WHERE num_cotizacion = $1";
$result = pg_query_params($conn, $query, array($num_cotizacion));

if (!$result || pg_num_rows($result) == 0) {
    die("Cotización no encontrada: " . pg_last_error());
}

$cotizacion = pg_fetch_assoc($result);

// Obtener productos de la cotización
$queryProductos = "SELECT cod_producto FROM tiene2 WHERE num_cotizacion = $1";
$resultProductos = pg_query_params($conn, $queryProductos, array($num_cotizacion));

if (!$resultProductos) {
    die("Error al obtener productos de la cotización: " . pg_last_error());
}

$productos = [];
while ($row = pg_fetch_assoc($resultProductos)) {
    $productos[] = $row['cod_producto'];
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

    // Actualizar cotización
    $query = "UPDATE cotizacion SET rut = $1, tra_rut = $2, fecha_cotizacion = $3, monto_total = $4, cantidad = $5, descripcion_cotizacion = $6, estado_cotizacion = $7 WHERE num_cotizacion = $8";
    $result = pg_query_params($conn, $query, array($rut, $tra_rut, $fecha_cotizacion, $monto_total, $cantidad_total, $descripcion_cotizacion, $estado_cotizacion, $num_cotizacion));

    if ($result) {
        // Eliminar productos existentes en tiene2
        $queryDelete = "DELETE FROM tiene2 WHERE num_cotizacion = $1";
        $resultDelete = pg_query_params($conn, $queryDelete, array($num_cotizacion));

        if ($resultDelete) {
            // Insertar nuevos productos en tiene2
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
            header("Location: actualizar_cotizacion_form.php");
            exit();
        } else {
            // Error en la eliminación de productos, deshacer transacción
            pg_query($conn, 'ROLLBACK');
            echo "Error al eliminar productos de la cotización: " . pg_last_error($conn);
        }
    } else {
        // Error en la actualización, deshacer transacción
        pg_query($conn, 'ROLLBACK');
        echo "Error al actualizar la cotización: " . pg_last_error($conn);
    }
}

// Cerrar la conexión
pg_close($conn);
?>
