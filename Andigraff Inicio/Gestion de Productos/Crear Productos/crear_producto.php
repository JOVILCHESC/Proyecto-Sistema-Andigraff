<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $numero_lote = $_POST['numero_lote'];
    $id_proveedor = intval($_POST['id_proveedor']);
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

    // Iniciar la transacción
    pg_query($conn, "BEGIN");

    // Preparar la consulta SQL para insertar el producto
    $sql_producto = "INSERT INTO producto (numero_lote, nombre_producto, precio_unitario, stock, tamano, tipo_producto, peso_unitario, estado_producto, iva, descripcion_producto, categoria, stock_critico)
                     VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)
                     RETURNING cod_producto";

    $params_producto = array($numero_lote, $nombre_producto, $precio_unitario, $stock, $tamano, $tipo_producto, $peso_unitario, $estado_producto, $iva, $descripcion_producto, $categoria, $stock_critico);

    $result_producto = pg_query_params($conn, $sql_producto, $params_producto);

    if ($result_producto) {
        $row = pg_fetch_assoc($result_producto);
        $cod_producto = $row['cod_producto'];

        // Preparar la consulta SQL para insertar en la tabla intermedia
        $sql_provee = "INSERT INTO provee (id_proveedor, cod_producto) VALUES ($1, $2)";
        $params_provee = array($id_proveedor, $cod_producto);

        $result_provee = pg_query_params($conn, $sql_provee, $params_provee);

        if ($result_provee) {
            // Confirmar la transacción
            pg_query($conn, "COMMIT");
            header("Location: ../Lista Productos/lista_producto.php"); // Redirigir a la vista de éxito
            exit();
        } else {
            // Revertir la transacción
            pg_query($conn, "ROLLBACK");
            echo "Error al registrar en la tabla intermedia: " . pg_last_error($conn);
        }
    } else {
        // Revertir la transacción
        pg_query($conn, "ROLLBACK");
        echo "Error al registrar el producto: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>

