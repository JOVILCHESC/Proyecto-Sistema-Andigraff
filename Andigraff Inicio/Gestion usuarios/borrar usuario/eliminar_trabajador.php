<?php
session_start();

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Verificar si el parámetro 'rut' está presente en la URL
if (!isset($_GET['rut'])) {
    die('RUT no especificado.');
}

// Obtener el RUT del trabajador a eliminar
$rut = $_GET['rut'];

// Obtener la conexión a la base de datos
$connection = getDBConnection();

// Verificar la conexión
if (!$connection) {
    die("Error en la conexión: " . pg_last_error());
}

// Iniciar una transacción
pg_query($connection, "BEGIN");

// Preparar la consulta SQL para eliminar el trabajador
$delete_trabajador_sql = "DELETE FROM trabajador WHERE rut = $1";

// Ejecutar la consulta de eliminación
$delete_trabajador_result = pg_query_params($connection, $delete_trabajador_sql, array($rut));

// Verificar si la eliminación fue exitosa
if ($delete_trabajador_result) {
    // Commit de la transacción si la eliminación es exitosa
    pg_query($connection, "COMMIT");
    // Redirigir al usuario a la lista de trabajadores
    header('Location: ../ver_trabajadores.php');
    exit;
} else {
    // Rollback en caso de error en la eliminación
    pg_query($connection, "ROLLBACK");
    echo "<p>Error al eliminar el trabajador: " . pg_last_error($connection) . "</p>";
}

// Cerrar la conexión a la base de datos
pg_close($connection);
?>
