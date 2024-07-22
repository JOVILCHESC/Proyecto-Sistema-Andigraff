<?php
require(__DIR__ . '/../../lib/fpdf/fpdf.php');

session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

if (isset($_GET['numero_factura'])) {
    $numero_factura = pg_escape_string($conn, $_GET['numero_factura']);

    $query_factura = "SELECT * FROM factura WHERE numero_factura = $1";
    $result_factura = pg_query_params($conn, $query_factura, array($numero_factura));
    $factura = pg_fetch_assoc($result_factura);

    if (!$factura) {
        die("Factura no encontrada.");
    }

    $query_venta = "SELECT * FROM venta WHERE numero_factura = $1";
    $result_venta = pg_query_params($conn, $query_venta, array($numero_factura));
    $venta = pg_fetch_assoc($result_venta);

    $query_detalle_venta = "SELECT * FROM detalle_venta WHERE cod_venta = $1";
    $result_detalle_venta = pg_query_params($conn, $query_detalle_venta, array($venta['cod_venta']));
    $detalles_venta = pg_fetch_all($result_detalle_venta);

    $query_tiene = "SELECT * FROM tiene WHERE cod_venta = $1";
    $result_tiene = pg_query_params($conn, $query_tiene, array($venta['cod_venta']));
    $metodos_pago = pg_fetch_all($result_tiene);

    pg_close($conn);

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Factura', 0, 1, 'C');
            $this->Ln(10);
        }

        function ChapterTitle($title) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, $title, 0, 1, 'L');
            $this->Ln(4);
        }

        function ChapterBody($body) {
            $this->SetFont('Arial', '', 12);
            $this->MultiCell(0, 10, $body);
            $this->Ln();
        }

        function ImprovedTable($header, $data) {
            $this->SetFont('Arial', 'B', 12);
            foreach ($header as $col) {
                $this->Cell(40, 10, $col, 1);
            }
            $this->Ln();

            $this->SetFont('Arial', '', 12);
            foreach ($data as $row) {
                foreach ($row as $col) {
                    $this->Cell(40, 10, $col, 1);
                }
                $this->Ln();
            }
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    $pdf->ChapterTitle('Detalles de la Factura');
    $pdf->ChapterBody(
        "Lugar de Emisión: " . $factura['lugar_emision'] . "\n" .
        "Fecha de Emisión: " . $factura['fecha_emision_factura'] . "\n" .
        "Descripción: " . $factura['descripcion_operacion_factura'] . "\n" .
        "Dirección de Entrega: " . $factura['direccion_entrega_factura'] . "\n" .
        "RUT del Cliente: " . $factura['rut']
    );

    $pdf->ChapterTitle('Detalles de la Venta');
    $pdf->ChapterBody(
        "RUT del Trabajador: " . $venta['tra_rut'] . "\n" .
        "Total Venta: " . $venta['total_venta'] . "\n" .
        "Hora de Venta: " . $venta['hora_venta'] . "\n" .
        "Subtotal: " . $venta['sub_total'] . "\n" .
        "Estado de Venta: " . ($venta['estado_venta'] ? 'Activa' : 'Inactiva') . "\n" .
        "IVA Venta: " . $venta['iva_venta']
    );

    $pdf->ChapterTitle('Detalles de Productos');
    $header = ['Código del Producto', 'Cantidad Ordenada', 'Fecha'];
    $data = [];
    if ($detalles_venta) {
        foreach ($detalles_venta as $detalle) {
            $data[] = [$detalle['cod_producto'], $detalle['cantidad_orden'], $detalle['fecha']];
        }
    } else {
        $data[] = ['No hay productos en esta venta.', '', ''];
    }
    $pdf->ImprovedTable($header, $data);

    $pdf->ChapterTitle('Métodos de Pago');
    $header = ['ID del Método de Pago', 'Porcentaje de Pago'];
    $data = [];
    if ($metodos_pago) {
        foreach ($metodos_pago as $metodo) {
            $data[] = [$metodo['id_metodo_pago'], $metodo['porcentaje_pago']];
        }
    } else {
        $data[] = ['No hay métodos de pago registrados.', ''];
    }
    $pdf->ImprovedTable($header, $data);

    $pdf->Output();
} else {
    echo "No se proporcionó un número de factura.";
}
?>
