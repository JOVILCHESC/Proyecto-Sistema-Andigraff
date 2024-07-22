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

// Obtener los datos del formulario
$cod_establecimiento = isset($_POST['cod_establecimiento']) ? pg_escape_string($conn, $_POST['cod_establecimiento']) : '';
$telefono = isset($_POST['telefono']) ? pg_escape_string($conn, $_POST['telefono']) : '';
$numero_estableciimiento = isset($_POST['numero_estableciimiento']) ? pg_escape_string($conn, $_POST['numero_estableciimiento']) : '';
$comuna_establecimiento = isset($_POST['comuna_establecimiento']) ? pg_escape_string($conn, $_POST['comuna_establecimiento']) : '';
$calle_establecimiento = isset($_POST['calle_establecimiento']) ? pg_escape_string($conn, $_POST['calle_establecimiento']) : '';
$ciudad_establecimiento = isset($_POST['ciudad_establecimiento']) ? pg_escape_string($conn, $_POST['ciudad_establecimiento']) : '';
$nombre_establecimiento = isset($_POST['nombre_establecimiento']) ? pg_escape_string($conn, $_POST['nombre_establecimiento']) : '';
$cant_empleados = isset($_POST['cant_empleados']) ? pg_escape_string($conn, $_POST['cant_empleados']) : '';
$capacidad = isset($_POST['capacidad']) ? pg_escape_string($conn, $_POST['capacidad']) : '';
$tipo_almacenamiento = isset($_POST['tipo_almacenamiento']) ? pg_escape_string($conn, $_POST['tipo_almacenamiento']) : '';
$estado_bodega = isset($_POST['estado_bodega']) ? 'true' : 'false';

// Iniciar una transacción
pg_query($conn, "BEGIN");

// Preparar la consulta SQL de actualización para la tabla bodega
$update_bodega_sql = "UPDATE bodega SET 
            capacidad = '$capacidad', 
            tipo_almacenamiento = '$tipo_almacenamiento', 
            estado_bodega = $estado_bodega 
        WHERE cod_establecimiento = '$cod_establecimiento'";

// Ejecutar la consulta de actualización
$update_bodega_result = pg_query($conn, $update_bodega_sql);

// Verificar el resultado y hacer commit o rollback
if ($update_bodega_result) {
    pg_query($conn, "COMMIT");
    echo "<p>Bodega actualizada correctamente. <a href='../lista establecimientos/listado_bodegas.php'>Volver al listado</a></p>";
} else {
    pg_query($conn, "ROLLBACK");
    echo "<p>Error al actualizar la bodega: " . pg_last_error($conn) . "</p>";
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
