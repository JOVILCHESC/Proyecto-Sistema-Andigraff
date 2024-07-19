<?php
// Include the database connection
require_once(__DIR__ . '/../../config/config.php');


// Check if form data is provided
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Datos del formulario no válidos.');
}

$id = intval($_POST['id']);
$direccion_origen = $_POST['direccion_origen'];
$direccion_destino = $_POST['direccion_destino'];
$condicion_entrega = intval($_POST['condicion_entrega']);
$estado_despacho = intval($_POST['estado_despacho']);
$fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];

// Update query
$update_query = 'UPDATE guia_despacho SET direccion_origen = $1, direccion_destino = $2, condicion_entrega = $3, estado_despacho = $4, fecha_emicion_guia_despacho = $5 WHERE num_guia_despacho = $6';

$connection = getDBConnection();
$result = pg_query_params($connection, $update_query, [$direccion_origen, $direccion_destino, $condicion_entrega, $estado_despacho, $fecha_emicion_guia_despacho, $id]);

if ($result) {
    header("Location: ../Lista Guias de Despacho/lista_guia_despacho.php");
    exit();
} else {
    echo "Error al actualizar la guía de despacho.";
}

// Close the connection
pg_close($connection);
?>
