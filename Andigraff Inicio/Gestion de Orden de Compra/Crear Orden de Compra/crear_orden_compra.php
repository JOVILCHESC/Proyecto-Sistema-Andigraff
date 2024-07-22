<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $id_proveedor = $_POST['id_proveedor'] ?? null;
    $rut = $_SESSION['rut'];
    $tipo_comprobante = $_POST['tipo_comprobante'] ?? null;
    $costo_total = $_POST['costo_total'] ?? null;
    $descripcion_orden = $_POST['descripcion_orden'] ?? null;
    $fecha_requerida = $_POST['fecha_requerida'] ?? null;
    $fecha_promesa = $_POST['fecha_promesa'] ?? null;
    $fecha_compra = $_POST['fecha_compra'] ?? null;
    $productos = isset($_POST['productos']) ? $_POST['productos'] : [];
    $cantidades = isset($_POST['cantidades']) ? $_POST['cantidades'] : [];

    // Calcular la cantidad solicitada como la suma de las cantidades de los productos
    $cantidad_solicitada = array_sum($cantidades);

    // Depuración: Imprimir las variables para verificar su contenido
    // Puedes eliminar esta sección en producción
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Iniciar una transacción
    pg_query($conn, "BEGIN");

    // Preparar la consulta SQL para insertar en la tabla `orden_compra`
    $sql_orden = "INSERT INTO public.orden_compra (id_proveedor, rut, tipo_comprobante, costo_total, descripcion_orden, cantidad_solicitada, fecha_requerida, estado_compra, fecha_promesa, fecha_compra)
                  VALUES ($1, $2, $3, $4, $5, $6, $7, 'true', $8, $9) RETURNING num_orden_compra";

    $params_orden = array($id_proveedor, $rut, $tipo_comprobante, $costo_total, $descripcion_orden, $cantidad_solicitada, $fecha_requerida, $fecha_promesa, $fecha_compra);
    $result_orden = pg_query_params($conn, $sql_orden, $params_orden);

    if ($result_orden) {
        $row = pg_fetch_assoc($result_orden);
        $num_orden_compra = $row['num_orden_compra'];

        // Preparar y ejecutar la consulta SQL para insertar en la tabla intermedia `tiene3`
        $error = false;
        foreach ($productos as $index => $cod_producto) {
            $cantidad = $cantidades[$index] ?? 0; // Asume una cantidad de 0 si no se especifica
            $sql_tiene3 = "INSERT INTO public.tiene3 (num_orden_compra, cod_producto, cantidad) VALUES ($1, $2, $3)";
            $params_tiene3 = array($num_orden_compra, $cod_producto, $cantidad);

            $result_tiene3 = pg_query_params($conn, $sql_tiene3, $params_tiene3);
            if (!$result_tiene3) {
                $error = true;
                break;
            }
        }

        if ($error) {
            pg_query($conn, "ROLLBACK");
            echo "Error al registrar los productos en la tabla intermedia: " . pg_last_error($conn);
        } else {
            pg_query($conn, "COMMIT");
            header("Location: ../Lista Orden de Compra/lista_orden_compra.php?success=1&num_orden_compra=$num_orden_compra"); // Redirigir a la vista de éxito
            exit();
        }
    } else {
        pg_query($conn, "ROLLBACK");
        echo "Error al registrar la orden de compra: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
