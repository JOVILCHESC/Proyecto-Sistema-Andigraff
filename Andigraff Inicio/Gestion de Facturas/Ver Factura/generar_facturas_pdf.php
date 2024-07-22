<?php
require(__DIR__ . '/../../lib/fpdf/fpdf.php');

session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

$host = "146.83.165.21";
$port = "5432";
$dbname = "bsilvestre"; // Replace with your actual database name
$user = "bsilvestre"; // Replace with your actual username
$password = "druIvAfaf4"; // Replace with your actual password

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
        private $numero_factura;

        function __construct($numero_factura) {
            parent::__construct();
            $this->numero_factura = $numero_factura;
        }

        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode('Factura Número: ' . $this->numero_factura), 0, 1, 'C');
            $this->Ln(10);
        }

        function ChapterTitle($title) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode($title), 0, 1, 'L');
            $this->Ln(4);
        }

        function ChapterBody($body) {
            $this->SetFont('Arial', '', 12);
            $this->MultiCell(0, 10, utf8_decode($body));
            $this->Ln();
        }

        function Table($header, $data) {
            $this->SetFont('Arial', 'B', 12);
            $widths = array_fill(0, count($header), 60); // Set a fixed width for each column
            foreach ($header as $i => $col) {
                $this->Cell($widths[$i], 10, utf8_decode($col), 1);
            }
            $this->Ln();

            $this->SetFont('Arial', '', 12);
            foreach ($data as $row) {
                foreach ($row as $i => $col) {
                    $this->Cell($widths[$i], 10, utf8_decode($col), 1);
                }
                $this->Ln();
            }
        }
    }

    $pdf = new PDF($numero_factura);
    $pdf->AddPage();

    $pdf->ChapterTitle('Detalles de la Factura');
    $factura_data = [
        ['Lugar de Emisión', utf8_decode($factura['lugar_emision'])],
        ['Fecha de Emisión', utf8_decode($factura['fecha_emision_factura'])],
        ['Descripción', utf8_decode($factura['descripcion_operacion_factura'])],
        ['Dirección de Entrega', utf8_decode($factura['direccion_entrega_factura'])],
        ['RUT del Cliente', utf8_decode($factura['rut'])]
    ];
    $pdf->Table(['Campo', 'Valor'], $factura_data);

    $pdf->ChapterTitle('Detalles de la Venta');
    $venta_data = [
        ['RUT del Trabajador', utf8_decode($venta['tra_rut'])],
        ['Total Venta', utf8_decode($venta['total_venta'])],
        ['Hora de Venta', utf8_decode($venta['hora_venta'])],
        ['Subtotal', utf8_decode($venta['sub_total'])],
        ['Estado de Venta', ($venta['estado_venta'] ? 'Activa' : 'Inactiva')],
        ['IVA Venta', utf8_decode($venta['iva_venta'])]
    ];
    $pdf->Table(['Campo', 'Valor'], $venta_data);

    $pdf->ChapterTitle('Detalles de Productos');
    $header = ['Código del Producto', 'Cantidad Ordenada', 'Fecha'];
    $data = [];
    if ($detalles_venta) {
        foreach ($detalles_venta as $detalle) {
            $data[] = [utf8_decode($detalle['cod_producto']), utf8_decode($detalle['cantidad_orden']), utf8_decode($detalle['fecha'])];
        }
    } else {
        $data[] = ['No hay productos en esta venta.', '', ''];
    }
    $pdf->Table($header, $data);

    $pdf->ChapterTitle('Métodos de Pago');
    $header = ['ID del Método de Pago', 'Porcentaje de Pago'];
    $data = [];
    if ($metodos_pago) {
        foreach ($metodos_pago as $metodo) {
            $data[] = [utf8_decode($metodo['id_metodo_pago']), utf8_decode($metodo['porcentaje_pago'])];
        }
    } else {
        $data[] = ['No hay métodos de pago registrados.', ''];
    }
    $pdf->Table($header, $data);

    $pdf->Output();
} else {
    echo "No se proporcionó un número de factura.";
}
?>
