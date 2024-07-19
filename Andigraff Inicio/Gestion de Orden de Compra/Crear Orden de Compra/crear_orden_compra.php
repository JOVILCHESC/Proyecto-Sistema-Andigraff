<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $num_orden_compra = $_POST['num_orden_compra'];
    $id_proveedor = $_POST['id_proveedor'];
    $rut = $_SESSION['rut'];
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $costo_total = $_POST['costo_total'];
    $descripcion_orden = $_POST['descripcion_orden'];
    $cantidad_solicitada = $_POST['cantidad_solicitada'];
    $fecha_requerida = $_POST['fecha_requerida'];
    $estado_compra = isset($_POST['estado_compra']) ? $_POST['estado_compra'] : 'false';
    $fecha_promesa = $_POST['fecha_promesa'];
    $fecha_compra = $_POST['fecha_compra'];

    // Validar el valor del estado de compra
    $estado_compra = $estado_compra === 'true' ? 't' : 'f';

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL
    $sql = "INSERT INTO public.orden_compra (num_orden_compra, id_proveedor, rut, tipo_comprobante, costo_total, descripcion_orden, cantidad_solicitada, fecha_requerida, estado_compra, fecha_promesa, fecha_compra)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)";

    $params = array($num_orden_compra, $id_proveedor, $rut, $tipo_comprobante, $costo_total, $descripcion_orden, $cantidad_solicitada, $fecha_requerida, $estado_compra, $fecha_promesa, $fecha_compra);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);
    
    if ($result) {
        header("Location: ../Lista Orden de Compra/lista_orden_compra.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>

