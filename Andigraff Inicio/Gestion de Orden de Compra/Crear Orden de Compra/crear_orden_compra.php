<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $id_proveedor = $_POST['id_proveedor'];
    $rut = $_SESSION['rut'];
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $costo_total = $_POST['costo_total'];
    $descripcion_orden = $_POST['descripcion_orden'];
    $cantidad_solicitada = $_POST['cantidad_solicitada'];
    $fecha_requerida = $_POST['fecha_requerida'];
    $fecha_promesa = $_POST['fecha_promesa'];
    $fecha_compra = $_POST['fecha_compra'];

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL, omitiendo num_orden_compra y estado_compra
    $sql = "INSERT INTO public.orden_compra (id_proveedor, rut, tipo_comprobante, costo_total, descripcion_orden, cantidad_solicitada, fecha_requerida, estado_compra, fecha_promesa, fecha_compra)
            VALUES ($1, $2, $3, $4, $5, $6, $7, 'true', $8, $9) RETURNING num_orden_compra";

    $params = array($id_proveedor, $rut, $tipo_comprobante, $costo_total, $descripcion_orden, $cantidad_solicitada, $fecha_requerida, $fecha_promesa, $fecha_compra);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
        // Obtener el número de orden de compra generado automáticamente
        $num_orden_compra = pg_fetch_result($result, 0, 'num_orden_compra');
        header("Location: ../Lista Orden de Compra/lista_orden_compra.php?success=1&num_orden_compra=$num_orden_compra"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
