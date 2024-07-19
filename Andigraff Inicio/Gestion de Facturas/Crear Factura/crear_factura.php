<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $rut = $_SESSION['rut'];
    $lugar_emision = $_POST['lugar_emision'];
    $fecha_emision = $_POST['fecha_emision'];
    $descripcion_operacion = $_POST['descripcion_operacion'];
    $direccion_entrega = $_POST['direccion_entrega'];

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL
    $sql = "INSERT INTO public.factura (rut, lugar_emision, fecha_emision_factura, descripcion_operacion_factura, direccion_entrega_factura)
            VALUES ($1, $2, $3, $4, $5)";

    $params = array($rut, $lugar_emision, $fecha_emision, $descripcion_operacion, $direccion_entrega);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);
    
    if ($result) {
        header("Location: ../Lista Facturas/lista_factura.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
