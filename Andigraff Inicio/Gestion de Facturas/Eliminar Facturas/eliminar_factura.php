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

// Recoger el ID de la factura a eliminar
$numero_factura = pg_escape_string($conn, $_GET['numero_factura']);

// Actualizar el estado de la factura para el borrado lógico
$query = "UPDATE factura SET estado_factura = FALSE WHERE numero_factura = $1";

$result = pg_query_params($conn, $query, array($numero_factura));

if ($result) {
    echo "Factura eliminada lógicamente con éxito.";
} else {
    echo "Error al eliminar la factura: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);

// Redireccionar a la lista de facturas (opcional)
header("Location: ../Lista de Facturas/ver_facturas.php");
exit();
?>
