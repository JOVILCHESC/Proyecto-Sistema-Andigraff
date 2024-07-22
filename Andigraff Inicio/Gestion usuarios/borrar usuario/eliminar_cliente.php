<?php
session_start();

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Verificar si el parámetro 'rut' está presente en la URL
if (!isset($_GET['rut'])) {
    die('RUT no especificado.');
}

// Obtener el RUT del cliente a eliminar
$rut = $_GET['rut'];

// Obtener la conexión a la base de datos
$connection = getDBConnection();

// Verificar la conexión
if (!$connection) {
    die("Error en la conexión: " . pg_last_error());
}

// Iniciar una transacción
pg_query($connection, "BEGIN");

// Preparar la consulta SQL para eliminar el cliente
$delete_cliente_sql = "DELETE FROM cliente WHERE rut = $1";

// Ejecutar la consulta de eliminación
$delete_cliente_result = pg_query_params($connection, $delete_cliente_sql, array($rut));

// Verificar si la eliminación fue exitosa
if ($delete_cliente_result) {
    // Commit de la transacción si la eliminación es exitosa
    pg_query($connection, "COMMIT");
    // Redirigir al usuario a la lista de clientes
    header('Location: ../ver_clientes.php');
    exit;
} else {
    // Rollback en caso de error en la eliminación
    pg_query($connection, "ROLLBACK");
    echo "<p>Error al eliminar el cliente: " . pg_last_error($connection) . "</p>";
}

// Cerrar la conexión a la base de datos
pg_close($connection);
?>
