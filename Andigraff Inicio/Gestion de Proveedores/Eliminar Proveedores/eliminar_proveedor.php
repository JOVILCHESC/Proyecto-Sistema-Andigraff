<?php
// Iniciar sesión
session_start();

// Verificar si se ha enviado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID de proveedor no proporcionado.');
}

// Obtener el ID del proveedor a eliminar
$id = $_GET['id'];

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Verificar sesión (si es necesario)
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Preparar y ejecutar la consulta de actualización para el borrado lógico
$sql = "UPDATE proveedor SET estado_proveedor = false WHERE id_proveedor = $1";
$params = array($id);

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    header("Location: ../Lista Proveedores/lista_proveedor.php"); // Redirigir a la lista de proveedores
    exit();
} else {
    echo "Error al actualizar: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
