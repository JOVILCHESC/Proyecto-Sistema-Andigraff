<?php
// Iniciar sesión
session_start();

// Verificar si se ha enviado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID del producto no proporcionado.');
}

// Obtener el ID del producto a eliminar
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
$sql = "UPDATE producto SET estado_producto = false WHERE cod_producto = $1";
$params = array($id);

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    header("Location: ../Lista Productos/lista_producto.php"); // Redirigir a la lista de productos
    exit();
} else {
    echo "Error al actualizar: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
