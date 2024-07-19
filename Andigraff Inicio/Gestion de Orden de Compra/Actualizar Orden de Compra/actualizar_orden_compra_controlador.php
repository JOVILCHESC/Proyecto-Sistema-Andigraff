<?php
// Include the database connection
require_once(__DIR__ . '/../../config/config.php');

// Check if form data is provided
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Datos del formulario no válidos.');
}

$id = intval($_POST['id']);
$id_proveedor = $_POST['id_proveedor'];
$rut = $_POST['rut'];
$tipo_comprobante = $_POST['tipo_comprobante'];
$costo_total = $_POST['costo_total'];
$descripcion_orden = $_POST['descripcion_orden'];
$cantidad_solicitada = $_POST['cantidad_solicitada'];
$fecha_requerida = $_POST['fecha_requerida'];
$estado_compra = intval($_POST['estado_compra']);
$fecha_promesa = $_POST['fecha_promesa'];
$fecha_compra = $_POST['fecha_compra'];

// Update query
$update_query = 'UPDATE orden_compra SET id_proveedor = $1, rut = $2, tipo_comprobante = $3, costo_total = $4, descripcion_orden = $5, cantidad_solicitada = $6, fecha_requerida = $7, estado_compra = $8, fecha_promesa = $9, fecha_compra = $10 WHERE num_orden_compra = $11';

$connection = getDBConnection();
$result = pg_query_params($connection, $update_query, [$id_proveedor, $rut, $tipo_comprobante, $costo_total, $descripcion_orden, $cantidad_solicitada, $fecha_requerida, $estado_compra, $fecha_promesa, $fecha_compra, $id]);

if ($result) {
    header("Location: ../Lista Orden de Compra/lista_orden_compra.php");
    exit();
} else {
    echo "Error al actualizar la orden de compra.";
}

// Close the connection
pg_close($connection);
?>
