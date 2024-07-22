<?php
require_once(__DIR__ . '/../../config/config.php'); // Asegúrate de que el archivo config.php tenga los detalles correctos de la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num_guia_despacho = intval($_POST['num_guia_despacho']);
    $direccion_origen = $_POST['direccion_origen'];
    $direccion_destino = $_POST['direccion_destino'];
    $condicion_entrega = $_POST['condicion_entrega'] == '1' ? true : false;
    $fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];

    $connection = getDBConnection();

    $query = 'UPDATE guia_despacho 
              SET direccion_origen = $1, direccion_destino = $2, condicion_entrega = $3, fecha_emicion_guia_despacho = $4 
              WHERE num_guia_despacho = $5';
    $result = pg_query_params($connection, $query, array($direccion_origen, $direccion_destino, $condicion_entrega, $fecha_emicion_guia_despacho, $num_guia_despacho));

    if ($result) {
        echo "Guía de despacho actualizada exitosamente.";
    } else {
        echo "Error al actualizar la guía de despacho.";
    }

    pg_close($connection);
} else {
    echo "Método no permitido.";
}
?>
