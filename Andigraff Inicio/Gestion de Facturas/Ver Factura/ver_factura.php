<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

if (isset($_GET['numero_factura'])) {
    $numero_factura = pg_escape_string($conn, $_GET['numero_factura']);

    // Obtener detalles de la factura
    $query_factura = "SELECT * FROM factura WHERE numero_factura = $1";
    $result_factura = pg_query_params($conn, $query_factura, array($numero_factura));
    $factura = pg_fetch_assoc($result_factura);

    if (!$factura) {
        echo "Factura no encontrada.";
        exit();
    }

    // Obtener detalles de la venta
    $query_venta = "SELECT * FROM venta WHERE numero_factura = $1";
    $result_venta = pg_query_params($conn, $query_venta, array($numero_factura));
    $venta = pg_fetch_assoc($result_venta);

    // Obtener detalles del detalle_venta
    $query_detalle_venta = "SELECT * FROM detalle_venta WHERE cod_venta = $1";
    $result_detalle_venta = pg_query_params($conn, $query_detalle_venta, array($venta['cod_venta']));
    $detalles_venta = pg_fetch_all($result_detalle_venta);

    // Obtener detalles de los métodos de pago
    $query_tiene = "SELECT * FROM tiene WHERE cod_venta = $1";
    $result_tiene = pg_query_params($conn, $query_tiene, array($venta['cod_venta']));
    $metodos_pago = pg_fetch_all($result_tiene);
} else {
    echo "No se proporcionó un número de factura.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Factura Número: <?php echo htmlspecialchars($factura['numero_factura']); ?></h2>
    <h3>Detalles de la Factura</h3>
    <table>
        <tr><th>Lugar de Emisión</th><td><?php echo htmlspecialchars($factura['lugar_emision']); ?></td></tr>
        <tr><th>Fecha de Emisión</th><td><?php echo htmlspecialchars($factura['fecha_emision_factura']); ?></td></tr>
        <tr><th>Descripción</th><td><?php echo htmlspecialchars($factura['descripcion_operacion_factura']); ?></td></tr>
        <tr><th>Dirección de Entrega</th><td><?php echo htmlspecialchars($factura['direccion_entrega_factura']); ?></td></tr>
        <tr><th>RUT del Cliente</th><td><?php echo htmlspecialchars($factura['rut']); ?></td></tr>
    </table>

    <h3>Detalles de la Venta</h3>
    <table>
        <tr><th>RUT del Trabajador</th><td><?php echo htmlspecialchars($venta['tra_rut']); ?></td></tr>
        <tr><th>Total Venta</th><td><?php echo htmlspecialchars($venta['total_venta']); ?></td></tr>
        <tr><th>Hora de Venta</th><td><?php echo htmlspecialchars($venta['hora_venta']); ?></td></tr>
        <tr><th>Subtotal</th><td><?php echo htmlspecialchars($venta['sub_total']); ?></td></tr>
        <tr><th>Estado de Venta</th><td><?php echo $venta['estado_venta'] ? 'Activa' : 'Inactiva'; ?></td></tr>
        <tr><th>IVA Venta</th><td><?php echo htmlspecialchars($venta['iva_venta']); ?></td></tr>
    </table>

    <h3>Detalles de Productos</h3>
    <table>
        <thead>
            <tr>
                <th>Código del Producto</th>
                <th>Cantidad Ordenada</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($detalles_venta) { ?>
                <?php foreach ($detalles_venta as $detalle) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($detalle['cod_producto']); ?></td>
                    <td><?php echo htmlspecialchars($detalle['cantidad_orden']); ?></td>
                    <td><?php echo htmlspecialchars($detalle['fecha']); ?></td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="3">No hay productos en esta venta.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <h3>Métodos de Pago</h3>
    <table>
        <thead>
            <tr>
                <th>ID del Método de Pago</th>
                <th>Porcentaje de Pago</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($metodos_pago) { ?>
                <?php foreach ($metodos_pago as $metodo) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($metodo['id_metodo_pago']); ?></td>
                    <td><?php echo htmlspecialchars($metodo['porcentaje_pago']); ?></td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="2">No hay métodos de pago registrados para esta venta.</td></tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="../Ver Factura/generar_facturas_pdf.php?numero_factura=<?php echo urlencode($factura['numero_factura']); ?>" class="button">Generar PDF</a>
    <a href="../Lista de Facturas/ver_facturas.php" class="button">Regresar a la lista de facturas</a>
</body>
</html>

<?php
// Cerrar la conexión
pg_close($conn);
?>
