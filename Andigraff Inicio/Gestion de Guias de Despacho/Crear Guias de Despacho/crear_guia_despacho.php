<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$rut_usuario = $_SESSION['rut'];

require_once(__DIR__ . '/../../config/config.php');

// Obtener la conexión a la base de datos
$conn = getDBConnection();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $direccion_origen = $_POST['direccion_origen'];
    $direccion_destino = $_POST['direccion_destino'];
    
    // Validar y convertir condicion_entrega a booleano
    $condicion_entrega_raw = isset($_POST['condicion_entrega']) ? $_POST['condicion_entrega'] : '0';
    $condicion_entrega = ($condicion_entrega_raw === '1') ? 'true' : 'false';
    
    $bodega = $_POST['bodega'];
    $fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];

    // Validar los datos
    if (empty($rut) || empty($direccion_origen) || empty($direccion_destino) || empty($fecha_emicion_guia_despacho)) {
        die("Todos los campos son obligatorios.");
    }

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Insertar guía de despacho
    $query = "INSERT INTO guia_despacho (rut, direccion_origen, direccion_destino, condicion_entrega, estado_despacho, fecha_emicion_guia_despacho) 
              VALUES ($1, $2, $3, $4, $5, $6) RETURNING num_guia_despacho";
    $result = pg_query_params($conn, $query, array($rut, $direccion_origen, $direccion_destino, $condicion_entrega, true, $fecha_emicion_guia_despacho));

    if ($result) {
        $row = pg_fetch_assoc($result);
        $num_guia_despacho = $row['num_guia_despacho'];

        // Insertar en la tabla tiene5
        $queryBodega = "INSERT INTO tiene5 (cod_establecimiento, num_guia_despacho) VALUES ($1, $2)";
        $resultBodega = pg_query_params($conn, $queryBodega, array($bodega, $num_guia_despacho));

        if (!$resultBodega) {
            // Error en la inserción, deshacer transacción
            pg_query($conn, 'ROLLBACK');
            die("Error al registrar la bodega de la guía de despacho: " . pg_last_error($conn));
        }

        // Confirmar transacción
        pg_query($conn, 'COMMIT');
        echo "Guía de despacho registrada exitosamente.";
    } else {
        // Error en la inserción, deshacer transacción
        pg_query($conn, 'ROLLBACK');
        die("Error al registrar la guía de despacho: " . pg_last_error($conn));
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
