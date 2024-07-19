<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Datos del formulario no válidos.');
}

$id = intval($_POST['id']);
$numero_lote = $_POST['numero_lote'];
$nombre_producto = $_POST['nombre_producto'];
$precio_unitario = $_POST['precio_unitario'];
$stock = $_POST['stock'];
$tamano = $_POST['tamano'];
$tipo_producto = $_POST['tipo_producto'];
$peso_unitario = $_POST['peso_unitario'];
$iva = $_POST['iva'];
$descripcion_producto = $_POST['descripcion_producto'];
$categoria = $_POST['categoria'];
$stock_critico = $_POST['stock_critico'];

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Preparar la consulta SQL de actualización
$sql = 'UPDATE producto SET numero_lote = $1, nombre_producto = $2, precio_unitario = $3, stock = $4, tamano = $5, tipo_producto = $6, peso_unitario = $7, iva = $8, descripcion_producto = $9, categoria = $10, stock_critico = $11 WHERE cod_producto = $12';

$params = array($numero_lote, $nombre_producto, $precio_unitario, $stock, $tamano, $tipo_producto, $peso_unitario, $iva, $descripcion_producto, $categoria, $stock_critico, $id);

// Ejecutar la consulta
$result = pg_query_params($conn, $sql, $params);

if ($result) {
    header('Location: ../Lista Productos/lista_producto.php'); // Redirigir a la vista de éxito
    exit();
} else {
    echo 'Error al actualizar el producto: ' . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
