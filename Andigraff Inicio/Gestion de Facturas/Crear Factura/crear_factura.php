<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$tra_rut_usuario = $_SESSION['rut'];

$host = "146.83.165.21";
$port = "5432";
$dbname = "bsilvestre"; // Replace with your actual database name
$user = "bsilvestre"; // Replace with your actual username
$password = "druIvAfaf4"; // Replace with your actual password

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Función para generar una fecha aleatoria en el rango de 2021 a 2022
function generar_fecha_aleatoria($anio_inicio = 2021, $anio_fin = 2022) {
    $fecha_inicio = strtotime("01-01-$anio_inicio");
    $fecha_fin = strtotime("31-12-$anio_fin");
    $fecha_aleatoria = mt_rand($fecha_inicio, $fecha_fin);
    return date('Y-m-d', $fecha_aleatoria);
}

// Recoger datos del formulario
$lugar_emision = pg_escape_string($conn, $_POST['lugar_emision']);
$fecha_emision_factura = pg_escape_string($conn, $_POST['fecha_emision_factura']);
$descripcion_operacion_factura = pg_escape_string($conn, $_POST['descripcion_operacion_factura']);
$direccion_entrega_factura = pg_escape_string($conn, $_POST['direccion_entrega_factura']);
$cliente_rut = pg_escape_string($conn, $_POST['cliente']);
$tra_rut = pg_escape_string($conn, $_POST['tra_rut']);
$total_venta = pg_escape_string($conn, $_POST['total_venta']);
$hora_venta = pg_escape_string($conn, $_POST['hora_venta']);
$sub_total = pg_escape_string($conn, $_POST['sub_total']);
// Establecer el estado_venta como true directamente
$estado_venta = true;
$iva_venta = pg_escape_string($conn, $_POST['iva_venta']);

// Insertar datos en la tabla factura
$query_factura = "INSERT INTO factura (lugar_emision, fecha_emision_factura, descripcion_operacion_factura, direccion_entrega_factura, rut) 
                  VALUES ($1, $2, $3, $4, $5) RETURNING numero_factura";

$result_factura = pg_query_params($conn, $query_factura, array($lugar_emision, $fecha_emision_factura, $descripcion_operacion_factura, $direccion_entrega_factura, $cliente_rut));

if ($result_factura) {
    $numero_factura = pg_fetch_result($result_factura, 0, 'numero_factura');

    // Insertar datos en la tabla venta
    $query_venta = "INSERT INTO venta (numero_factura, rut, tra_rut, total_venta, hora_venta, sub_total, estado_venta, iva_venta) 
                    VALUES ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING cod_venta";

    $result_venta = pg_query_params($conn, $query_venta, array($numero_factura, $cliente_rut, $tra_rut, $total_venta, $hora_venta, $sub_total, $estado_venta, $iva_venta));

    if ($result_venta) {
        $cod_venta = pg_fetch_result($result_venta, 0, 'cod_venta');

        // Insertar datos en la tabla detalle_venta
        $productos = $_POST['productos'];
        $cantidades = $_POST['cantidades'];

        for ($i = 0; $i < count($productos); $i++) {
            $cod_producto = pg_escape_string($conn, $productos[$i]);
            $cantidad_orden = pg_escape_string($conn, $cantidades[$i]);
            $fecha = generar_fecha_aleatoria(); // Fecha aleatoria entre 2021 y 2022

            $query_detalle_venta = "INSERT INTO detalle_venta (cod_producto, cod_venta, fecha, cantidad_orden) 
                                    VALUES ($1, $2, $3, $4)";

            $result_detalle_venta = pg_query_params($conn, $query_detalle_venta, array($cod_producto, $cod_venta, $fecha, $cantidad_orden));

            if (!$result_detalle_venta) {
                echo "Error al insertar el detalle de venta: " . pg_last_error($conn);
                pg_close($conn);
                exit();
            }
        }

        // Insertar datos en la tabla tiene
        $metodos_pago = $_POST['metodos_pago'];
        $porcentajes_pago = $_POST['porcentajes_pago'];

        for ($i = 0; $i < count($metodos_pago); $i++) {
            $id_metodo_pago = pg_escape_string($conn, $metodos_pago[$i]);
            $porcentaje_pago = pg_escape_string($conn, $porcentajes_pago[$i]);

            $query_tiene = "INSERT INTO tiene (cod_venta, id_metodo_pago, porcentaje_pago) 
                            VALUES ($1, $2, $3)";

            $result_tiene = pg_query_params($conn, $query_tiene, array($cod_venta, $id_metodo_pago, $porcentaje_pago));

            if (!$result_tiene) {
                echo "Error al insertar el método de pago: " . pg_last_error($conn);
                pg_close($conn);
                exit();
            }
        }

        echo "Factura, venta, detalle de venta y métodos de pago registrados exitosamente.";
    } else {
        echo "Error al insertar la venta: " . pg_last_error($conn);
    }
} else {
    echo "Error al insertar la factura: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
