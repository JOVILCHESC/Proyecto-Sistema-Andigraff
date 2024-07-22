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

if (isset($_GET['id'])) {
    $num_guia_despacho = pg_escape_string($conn, $_GET['id']);

    $query_guia = "SELECT * FROM guia_despacho WHERE num_guia_despacho = $1";
    $result_guia = pg_query_params($conn, $query_guia, array($num_guia_despacho));
    $guia = pg_fetch_assoc($result_guia);

    if (!$guia) {
        die("Guía de despacho no encontrada.");
    }

    $query_productos = "SELECT p.cod_producto, p.nombre_producto, d.cantidad 
                        FROM detalle_producto_guia_despacho d 
                        JOIN producto p ON d.cod_producto = p.cod_producto 
                        WHERE d.num_guia_despacho = $1";
    $result_productos = pg_query_params($conn, $query_productos, array($num_guia_despacho));
    $productos = pg_fetch_all($result_productos);

    pg_close($conn);

    class PDF extends FPDF {
        private $num_guia_despacho;

        function __construct($num_guia_despacho) {
            parent::__construct();
            $this->num_guia_despacho = $num_guia_despacho;
        }

        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode('Guía de Despacho Número: ' . $this->num_guia_despacho), 0, 1, 'C');
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

    $pdf = new PDF($num_guia_despacho);
    $pdf->AddPage();

    $pdf->ChapterTitle('Detalles de la Guía de Despacho');
    $pdf->ChapterBody(
        "ID: " . htmlspecialchars($guia['num_guia_despacho']) . "\n" .
        "Dirección de Origen: " . htmlspecialchars($guia['direccion_origen']) . "\n" .
        "Dirección de Destino: " . htmlspecialchars($guia['direccion_destino']) . "\n" .
        "Condición de Entrega: " . ($guia['condicion_entrega'] ? 'Entregado' : 'No Entregado') . "\n" .
        "Fecha de Emisión: " . htmlspecialchars($guia['fecha_emicion_guia_despacho'])
    );

    $header = array('Código del Producto', 'Nombre del Producto', 'Cantidad');
    $data = [];
    if ($productos) {
        foreach ($productos as $producto) {
            $data[] = array(
                htmlspecialchars($producto['cod_producto']),
                htmlspecialchars($producto['nombre_producto']),
                htmlspecialchars($producto['cantidad'])
            );
        }
    } else {
        $data[] = array('No hay productos para esta guía de despacho', '', '');
    }

    $pdf->Table($header, $data);
    $pdf->Output();
} else {
    die("No se proporcionó un ID de guía de despacho.");
}
?>
