<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $numero_lote = $_POST['numero_lote'];
    $id_proveedor = intval($_POST['id_proveedor']);
    $cod_establecimiento = intval($_POST['cod_establecimiento']);
    $nombre_producto = $_POST['nombre_producto'];
    $precio_unitario = $_POST['precio_unitario'];
    $stock = $_POST['stock'];
    $tamano = $_POST['tamano'];
    $tipo_producto = $_POST['tipo_producto'];
    $peso_unitario = $_POST['peso_unitario'];
    $iva = 0.19;  // IVA siempre será 0.19
    $descripcion_producto = $_POST['descripcion_producto'];
    $categoria = $_POST['categoria'];
    $stock_critico = $_POST['stock_critico'];

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar si el establecimiento existe en la tabla bodega antes de la inserción
    $query_check_establecimiento = "SELECT 1 FROM public.bodega WHERE cod_establecimiento = $1";
    $result_check_establecimiento = pg_query_params($conn, $query_check_establecimiento, array($cod_establecimiento));

    if (pg_num_rows($result_check_establecimiento) === 0) {
        die("Error: El establecimiento seleccionado no existe.");
    }

    // Iniciar la transacción
    pg_query($conn, "BEGIN");

    // Preparar la consulta SQL para insertar el producto
    $sql_producto = "INSERT INTO producto (numero_lote, nombre_producto, precio_unitario, stock, tamano, tipo_producto, peso_unitario, estado_producto, iva, descripcion_producto, categoria, stock_critico)
                     VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)
                     RETURNING cod_producto";

    $params_producto = array($numero_lote, $nombre_producto, $precio_unitario, $stock, $tamano, $tipo_producto, $peso_unitario, true, $iva, $descripcion_producto, $categoria, $stock_critico);

    $result_producto = pg_query_params($conn, $sql_producto, $params_producto);

    if ($result_producto) {
        $row = pg_fetch_assoc($result_producto);
        $cod_producto = $row['cod_producto'];

        // Preparar la consulta SQL para insertar en la tabla intermedia provee
        $sql_provee = "INSERT INTO provee (id_proveedor, cod_producto) VALUES ($1, $2)";
        $params_provee = array($id_proveedor, $cod_producto);

        $result_provee = pg_query_params($conn, $sql_provee, $params_provee);

        // Preparar la consulta SQL para insertar en la tabla intermedia almacena
        $sql_almacena = "INSERT INTO almacena (cod_establecimiento, cod_producto) VALUES ($1, $2)";
        $params_almacena = array($cod_establecimiento, $cod_producto);

        $result_almacena = pg_query_params($conn, $sql_almacena, $params_almacena);

        if ($result_provee && $result_almacena) {
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

