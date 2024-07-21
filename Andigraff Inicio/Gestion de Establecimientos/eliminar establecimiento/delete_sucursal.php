<?php
// Incluir la conexión a la base de datos
require_once(__DIR__ . '/../../config/config.php');

// Verificar si se ha enviado el código del establecimiento
if (!isset($_GET['cod_establecimiento']) || empty($_GET['cod_establecimiento'])) {
    die('Código del establecimiento no válido.');
}

$cod_establecimiento = pg_escape_string(getDBConnection(), $_GET['cod_establecimiento']);

// Conectar a la base de datos
$conn = getDBConnection();

// Iniciar una transacción
pg_query($conn, "BEGIN");

// Preparar y ejecutar la consulta SQL de eliminación
$delete_sql = "DELETE FROM sucursal WHERE cod_establecimiento = $1";
$result = pg_query_params($conn, $delete_sql, array($cod_establecimiento));

if ($result) {
    // Confirmar la transacción
    pg_query($conn, "COMMIT");
    header("Location: ../lista establecimientos/lista_establecimientosucursal.php");
    exit();
} else {
    // Revertir la transacción en caso de error
    pg_query($conn, "ROLLBACK");
    echo "Error al eliminar la sucursal: " . pg_last_error($conn);
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
