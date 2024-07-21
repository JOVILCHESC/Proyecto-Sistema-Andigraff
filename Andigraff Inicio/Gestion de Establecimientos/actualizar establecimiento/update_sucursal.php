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

// Conectar a la base de datos
$connection = getDBConnection();

// Iniciar una transacción
pg_query($connection, "BEGIN");

// Preparar la consulta SQL de actualización
$update_sql = "UPDATE sucursal SET
    nombre_establecimiento = $1,
    telefono = $2,
    numero_estableciimiento = $3,
    comuna_establecimiento = $4,
    calle_establecimiento = $5,
    ciudad_establecimiento = $6,
    tipo_sucursal = $7,
    cant_empleados = $8
    WHERE cod_establecimiento = $9";

$params = array($nombre, $telefono, $numero, $comuna, $calle, $ciudad, $tipo, $cant_empleados, $cod_establecimiento);

$update_result = pg_query_params($connection, $update_sql, $params);

if ($update_result) {
    pg_query($connection, "COMMIT");
    header("Location: ../lista establecimientos/lista_establecimientosucursal.php");
    exit();
} else {
    pg_query($connection, "ROLLBACK");
    die("Error al actualizar los datos: " . pg_last_error($connection));
}

pg_close($connection);
?>
