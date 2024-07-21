<?php
// Iniciar sesión
session_start();

// Verificar si se ha enviado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID de guía de despacho no proporcionado.');
}

// Obtener el ID de la guía de despacho a eliminar
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


// Preparar y ejecutar la consulta de actualización
$sql = "UPDATE public.guia_despacho SET estado_despacho = false WHERE num_guia_despacho = $1";
$params = array($id);

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    header("Location: ../Lista Guias de Despacho/lista_guia_despacho.php"); // Redirigir a la lista de guías de despacho
    exit();
} else {
    echo "Error al actualizar: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
