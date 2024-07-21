<?php
// Incluir la conexión a la base de datos
require_once(__DIR__ . '/../../config/config.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cod_establecimiento'])) {
    die('Datos del formulario no válidos.');
}

// Obtener y sanitizar los datos del formulario
$cod_establecimiento = pg_escape_string(getDBConnection(), $_POST['cod_establecimiento']);
$nombre = pg_escape_string(getDBConnection(), $_POST['nombre']);
$telefono = pg_escape_string(getDBConnection(), $_POST['telefono']);
$numero = pg_escape_string(getDBConnection(), $_POST['numero']);
$comuna = pg_escape_string(getDBConnection(), $_POST['comuna']);
$calle = pg_escape_string(getDBConnection(), $_POST['calle']);
$ciudad = pg_escape_string(getDBConnection(), $_POST['ciudad']);
$tipo = pg_escape_string(getDBConnection(), $_POST['tipo']);
$cant_empleados = (int) $_POST['cant_empleados'];
$capacidad = isset($_POST['capacidad']) ? pg_escape_string(getDBConnection(), $_POST['capacidad']) : '';
$tipo_almacenamiento = isset($_POST['tipo_almacenamiento']) ? pg_escape_string(getDBConnection(), $_POST['tipo_almacenamiento']) : '';
$estado_bodega = isset($_POST['estado_bodega']) ? 'true' : 'false';

// Conectar a la base de datos
$connection = getDBConnection();

// Iniciar una transacción
pg_query($connection, "BEGIN");

// Preparar la consulta SQL de actualización para la tabla sucursal
$update_sucursal_sql = "UPDATE sucursal SET
    nombre_establecimiento = $1,
    telefono = $2,
    numero_estableciimiento = $3,
    comuna_establecimiento = $4,
    calle_establecimiento = $5,
    ciudad_establecimiento = $6,
    tipo_sucursal = $7,
    cant_empleados = $8
    WHERE cod_establecimiento = $9";

// Parámetros para la consulta de la tabla sucursal
$sucursal_params = array($nombre, $telefono, $numero, $comuna, $calle, $ciudad, $tipo, $cant_empleados, $cod_establecimiento);

$update_sucursal_result = pg_query_params($connection, $update_sucursal_sql, $sucursal_params);

if (!$update_sucursal_result) {
    pg_query($connection, "ROLLBACK");
    die("Error al actualizar los datos de sucursal: " . pg_last_error($connection));
}

// Preparar la consulta SQL de actualización para la tabla establecimiento
$update_establecimiento_sql = "UPDATE establecimiento SET
    telefono = $1,
    numero_estableciimiento = $2,
    comuna_establecimiento = $3,
    calle_establecimiento = $4,
    ciudad_establecimiento = $5,
    nombre_establecimiento = $6,
    cant_empleados = $7
    WHERE cod_establecimiento = $8";

// Parámetros para la consulta de la tabla establecimiento
$establecimiento_params = array($telefono, $numero, $comuna, $calle, $ciudad, $nombre, $cant_empleados, $cod_establecimiento);

$update_establecimiento_result = pg_query_params($connection, $update_establecimiento_sql, $establecimiento_params);

if ($update_establecimiento_result) {
    pg_query($connection, "COMMIT");
    header("Location: ../lista establecimientos/lista_establecimientosucursal.php");
    exit();
} else {
    pg_query($connection, "ROLLBACK");
    die("Error al actualizar los datos de establecimiento: " . pg_last_error($connection));
}

pg_close($connection);
?>
