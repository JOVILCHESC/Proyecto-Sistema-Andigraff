<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Obtener el código del establecimiento a eliminar
$cod_establecimiento = isset($_GET['cod_establecimiento']) ? pg_escape_string($conn, $_GET['cod_establecimiento']) : '';

// Verificar si se proporcionó un código de establecimiento
if ($cod_establecimiento) {
    // Eliminar la bodega de la base de datos
    $sql = "DELETE FROM bodega WHERE cod_establecimiento = '$cod_establecimiento'";
    $result = pg_query($conn, $sql);

    if ($result) {
        echo "<p>Bodega eliminada correctamente. <a href='../lista establecimientos/listado_bodegas.php'>Volver al listado</a></p>";
    } else {
        echo "<p>Error al eliminar la bodega: " . pg_last_error($conn) . "</p>";
    }
} else {
    echo "<p>No se proporcionó un código de establecimiento válido.</p>";
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
