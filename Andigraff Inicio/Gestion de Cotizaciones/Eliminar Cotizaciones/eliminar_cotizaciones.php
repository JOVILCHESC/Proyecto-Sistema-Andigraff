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
$dbname = "bsilvestre";
$user = "bsilvestre";
$password = "druIvAfaf4";

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

if (isset($_GET['num_cotizacion'])) {
    $num_cotizacion = pg_escape_string($conn, $_GET['num_cotizacion']);

    // Actualizar el estado de la cotización a 'false' (inactivo)
    $query = "UPDATE cotizacion SET estado_cotizacion = FALSE WHERE num_cotizacion = $1";
    $result = pg_query_params($conn, $query, array($num_cotizacion));

    if ($result) {
        echo "cotizacion eliminada lógicamente con éxito.";
    } else {
        echo "Error al eliminar la cotizacion: " . pg_last_error($conn);
    }
}

// Cerrar la conexión
pg_close($conn);
// Redireccionar a la lista de facturas (opcional)
header("Location: ../Lista de Cotizaciones/ver_cotizaciones.php");
exit();
?>
