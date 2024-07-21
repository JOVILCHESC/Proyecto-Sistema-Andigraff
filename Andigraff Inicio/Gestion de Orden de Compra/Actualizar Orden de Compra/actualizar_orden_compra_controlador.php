<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Datos del formulario no válidos.');
}

$id = intval($_POST['id']);
$id_proveedor = $_POST['id_proveedor'];
$rut = $_POST['rut'];
$tipo_comprobante = $_POST['tipo_comprobante'];
$costo_total = $_POST['costo_total'];
$descripcion_orden = $_POST['descripcion_orden'];
$cantidad_solicitada = $_POST['cantidad_solicitada'];
$fecha_requerida = $_POST['fecha_requerida'];
$estado_compra = true; // Aseguramos que el estado siga siendo true
$fecha_promesa = $_POST['fecha_promesa'];
$fecha_compra = $_POST['fecha_compra'];
$productos = isset($_POST['productos']) ? $_POST['productos'] : [];

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Iniciar una transacción
pg_query($conn, "BEGIN");

// Preparar la consulta SQL de actualización para la tabla `orden_compra`
$sql_orden = 'UPDATE orden_compra SET id_proveedor = $1, rut = $2, tipo_comprobante = $3, costo_total = $4, descripcion_orden = $5, cantidad_solicitada = $6, fecha_requerida = $7, estado_compra = $8, fecha_promesa = $9, fecha_compra = $10 WHERE num_orden_compra = $11';
$params_orden = array($id_proveedor, $rut, $tipo_comprobante, $costo_total, $descripcion_orden, $cantidad_solicitada, $fecha_requerida, $estado_compra, $fecha_promesa, $fecha_compra, $id);

$result_orden = pg_query_params($conn, $sql_orden, $params_orden);

if ($result_orden) {
    // Eliminar los registros anteriores de la tabla `tiene3`
    $sql_delete = 'DELETE FROM tiene3 WHERE num_orden_compra = $1';
    $result_delete = pg_query_params($conn, $sql_delete, [$id]);

    if ($result_delete) {
        // Insertar los nuevos registros en la tabla `tiene3`
        $error = false;
        foreach ($productos as $cod_producto) {
            $sql_tiene3 = 'INSERT INTO tiene3 (num_orden_compra, cod_producto) VALUES ($1, $2)';
            $params_tiene3 = array($id, $cod_producto);

            $result_tiene3 = pg_query_params($conn, $sql_tiene3, $params_tiene3);
            if (!$result_tiene3) {
                $error = true;
                break;
            }
        }

        if ($error) {
            pg_query($conn, "ROLLBACK");
            echo "Error al actualizar los productos en la tabla intermedia: " . pg_last_error($conn);
        } else {
            pg_query($conn, "COMMIT");
            header("Location: ../Lista Orden de Compra/lista_orden_compra.php"); // Redirigir a la vista de éxito
            exit();
        }
    } else {
        pg_query($conn, "ROLLBACK");
        echo "Error al eliminar los productos anteriores: " . pg_last_error($conn);
    }
} else {
    pg_query($conn, "ROLLBACK");
    echo "Error al actualizar la orden de compra: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
