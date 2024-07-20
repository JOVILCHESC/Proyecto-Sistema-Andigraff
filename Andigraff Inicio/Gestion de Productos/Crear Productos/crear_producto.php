<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $numero_lote = $_POST['numero_lote'];
    $nombre_producto = $_POST['nombre_producto'];
    $precio_unitario = $_POST['precio_unitario'];
    $stock = $_POST['stock'];
    $tamano = $_POST['tamano'];
    $tipo_producto = $_POST['tipo_producto'];
    $peso_unitario = $_POST['peso_unitario'];
    $estado_producto = isset($_POST['estado_producto']) ? (bool) $_POST['estado_producto'] : false;
    $iva = $_POST['iva'];
    $descripcion_producto = $_POST['descripcion_producto'];
    $categoria = $_POST['categoria'];
    $stock_critico = $_POST['stock_critico'];

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL
    $sql = "INSERT INTO producto (numero_lote, nombre_producto, precio_unitario, stock, tamano, tipo_producto, peso_unitario, estado_producto, iva, descripcion_producto, categoria, stock_critico)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";

    $params = array($numero_lote, $nombre_producto, $precio_unitario, $stock, $tamano, $tipo_producto, $peso_unitario, $estado_producto, $iva, $descripcion_producto, $categoria, $stock_critico);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);
    
    if ($result) {
        header("Location: ../Lista Productos/lista_producto.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>

