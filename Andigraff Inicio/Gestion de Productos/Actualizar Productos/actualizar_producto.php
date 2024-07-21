<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Datos del formulario no válidos.');
}

$id = intval($_POST['id']);
$id_proveedor = intval($_POST['id_proveedor']);
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

// Iniciar una transacción
pg_query($conn, "BEGIN");

// Preparar la consulta SQL de actualización para la tabla `producto`
$sql_producto = 'UPDATE producto SET numero_lote = $1, nombre_producto = $2, precio_unitario = $3, stock = $4, tamano = $5, tipo_producto = $6, peso_unitario = $7, iva = $8, descripcion_producto = $9, categoria = $10, stock_critico = $11 WHERE cod_producto = $12';
$params_producto = array($numero_lote, $nombre_producto, $precio_unitario, $stock, $tamano, $tipo_producto, $peso_unitario, $iva, $descripcion_producto, $categoria, $stock_critico, $id);

$result_producto = pg_query_params($conn, $sql_producto, $params_producto);

if ($result_producto) {
    // Preparar la consulta SQL de actualización en la tabla intermedia
    $sql_provee = 'UPDATE provee SET id_proveedor = $1 WHERE cod_producto = $2';
    $params_provee = array($id_proveedor, $id);

    $result_provee = pg_query_params($conn, $sql_provee, $params_provee);

    if ($result_provee) {
        // Confirmar la transacción
        pg_query($conn, "COMMIT");
        header('Location: ../Lista Productos/lista_producto.php'); // Redirigir a la vista de éxito
        exit();
    } else {
        // Revertir la transacción en caso de error
        pg_query($conn, "ROLLBACK");
        echo 'Error al actualizar la tabla intermedia: ' . pg_last_error($conn);
    }
} else {
    // Revertir la transacción en caso de error
    pg_query($conn, "ROLLBACK");
    echo 'Error al actualizar el producto: ' . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
