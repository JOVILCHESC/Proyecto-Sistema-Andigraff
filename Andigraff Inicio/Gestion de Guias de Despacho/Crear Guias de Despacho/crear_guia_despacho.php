<?php
require_once(__DIR__ . '/../../config/config.php');

session_start();

if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$rut_usuario = $_SESSION['rut'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la conexión a la base de datos
    $conn = getDBConnection();

    // Recoger y sanitizar datos del formulario
    $rut = $_POST['rut'];
    $direccion_origen = pg_escape_string($_POST['direccion_origen']);
    $direccion_destino = pg_escape_string($_POST['direccion_destino']);
    $condicion_entrega = isset($_POST['condicion_entrega']) ? (bool)$_POST['condicion_entrega'] : false;
    $estado_despacho = isset($_POST['estado_despacho']) ? (bool)$_POST['estado_despacho'] : false;
    $fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];
    $cod_establecimiento = (int)$_POST['cod_establecimiento'];

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Insertar guía de despacho
    $query = "INSERT INTO guia_despacho (rut, direccion_origen, direccion_destino, condicion_entrega, estado_despacho, fecha_emicion_guia_despacho) 
              VALUES ($1, $2, $3, $4, $5, $6) RETURNING num_guia_despacho";
    $result = pg_query_params($conn, $query, array($rut, $direccion_origen, $direccion_destino, $condicion_entrega, $estado_despacho, $fecha_emicion_guia_despacho));

    if ($result) {
        $row = pg_fetch_assoc($result);
        $num_guia_despacho = $row['num_guia_despacho'];

        // Insertar en la tabla intermedia tiene5
        $queryTiene5 = "INSERT INTO tiene5 (cod_establecimiento, num_guia_despacho) VALUES ($1, $2)";
        $resultTiene5 = pg_query_params($conn, $queryTiene5, array($cod_establecimiento, $num_guia_despacho));
        if (!$resultTiene5) {
            // Error en la inserción, deshacer transacción
            pg_query($conn, 'ROLLBACK');
            die("Error al registrar la guía de despacho en la tabla intermedia: " . pg_last_error($conn));
        }

        // Confirmar transacción
        pg_query($conn, 'COMMIT');
        echo "Guía de despacho registrada exitosamente.";
    } else {
        // Error en la inserción, deshacer transacción
        pg_query($conn, 'ROLLBACK');
        echo "Error al registrar la guía de despacho: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
