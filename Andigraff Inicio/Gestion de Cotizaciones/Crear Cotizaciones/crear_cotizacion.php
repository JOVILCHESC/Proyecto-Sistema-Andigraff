<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

$tra_rut_usuario = $_SESSION['rut'];

// Conectar a la base de datos
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $tra_rut = $_POST['tra_rut'];
    $fecha_cotizacion = $_POST['fecha_cotizacion'];
    $monto_total = $_POST['monto_total'];
    $cantidad = $_POST['cantidad'];
    $descripcion_cotizacion = $_POST['descripcion_cotizacion'];
    $estado_cotizacion = isset($_POST['estado_cotizacion']) ? 'true' : 'false';
    $productos = $_POST['productos'];

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Insertar cotización
    $query = "INSERT INTO cotizacion (rut, tra_rut, fecha_cotizacion, monto_total, cantidad, descripcion_cotizacion, estado_cotizacion) 
              VALUES ('$rut', '$tra_rut', '$fecha_cotizacion', $monto_total, $cantidad, '$descripcion_cotizacion', $estado_cotizacion) RETURNING num_cotizacion";
    $result = pg_query($conn, $query);

    if ($result) {
        $row = pg_fetch_assoc($result);
        $num_cotizacion = $row['num_cotizacion'];

        // Insertar productos en tiene2
        foreach ($productos as $cod_producto) {
            $queryProducto = "INSERT INTO tiene2 (cod_producto, num_cotizacion) VALUES ($cod_producto, $num_cotizacion)";
            $resultProducto = pg_query($conn, $queryProducto);
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
