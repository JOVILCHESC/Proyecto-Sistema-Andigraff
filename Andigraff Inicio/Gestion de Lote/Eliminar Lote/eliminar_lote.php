<?php
// Iniciar sesión
session_start();

// Verificar si se ha enviado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID de lote no proporcionado.');
}

// Obtener el ID del lote a eliminar
$id = intval($_GET['id']);

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Verificar conexión
if (!$conn) {
    die("Error en la conexión: " . pg_last_error());
}

// Preparar y ejecutar la consulta de eliminación
$sql = "DELETE FROM lote WHERE numero_lote = $1";
$params = array($id);

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    header("Location: ../Lista Lote/lista_lote.php"); // Redirigir a la lista de lotes
    exit();
} else {
    echo "Error al eliminar: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
