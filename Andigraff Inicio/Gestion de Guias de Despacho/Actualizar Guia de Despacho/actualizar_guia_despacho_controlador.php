<?php
// Include the database connection
require_once(__DIR__ . '/../../config/config.php');

// Check if the required POST parameters are provided
if (!isset($_POST['num_guia_despacho']) || 
    !isset($_POST['direccion_origen']) || 
    !isset($_POST['direccion_destino']) || 
    !isset($_POST['condicion_entrega']) || 
    !isset($_POST['estado_despacho']) || 
    !isset($_POST['fecha_emicion_guia_despacho'])) {
    die('Datos incompletos.');
}

// Sanitize the input
$num_guia_despacho = intval($_POST['num_guia_despacho']);
$direccion_origen = htmlspecialchars($_POST['direccion_origen']);
$direccion_destino = htmlspecialchars($_POST['direccion_destino']);
$condicion_entrega = intval($_POST['condicion_entrega']);
$estado_despacho = intval($_POST['estado_despacho']);
$fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];

// Function to update guide data
function updateGuide($num_guia_despacho, $direccion_origen, $direccion_destino, $condicion_entrega, $estado_despacho, $fecha_emicion_guia_despacho) {
    $connection = getDBConnection();
    $query = 'UPDATE guia_despacho SET direccion_origen = $1, direccion_destino = $2, condicion_entrega = $3, estado_despacho = $4, fecha_emicion_guia_despacho = $5 WHERE num_guia_despacho = $6';
    $params = [$direccion_origen, $direccion_destino, $condicion_entrega, $estado_despacho, $fecha_emicion_guia_despacho, $num_guia_despacho];
    $result = pg_query_params($connection, $query, $params);

    if (!$result) {
        echo "Error en la actualización.";
        return false;
    }

    return true;
}

// Update the guide
if (updateGuide($num_guia_despacho, $direccion_origen, $direccion_destino, $condicion_entrega, $estado_despacho, $fecha_emicion_guia_despacho)) {
    echo "Guía de despacho actualizada exitosamente.";
} else {
    echo "Error al actualizar la guía de despacho.";
}
?>
