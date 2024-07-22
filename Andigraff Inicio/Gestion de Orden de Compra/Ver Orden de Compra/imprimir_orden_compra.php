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

if (isset($_GET['num_orden_compra'])) {
    $num_orden_compra = pg_escape_string($conn, $_GET['num_orden_compra']);

    $query_orden = "SELECT * FROM orden_compra WHERE num_orden_compra = $1 AND estado_compra = true";
    $result_orden = pg_query_params($conn, $query_orden, array($num_orden_compra));
    $orden = pg_fetch_assoc($result_orden);

    if (!$orden) {
        die("Orden de compra no encontrada.");
    }

    $query_proveedor = "SELECT nombre_proveedor FROM proveedor WHERE id_proveedor = (SELECT id_proveedor FROM orden_compra WHERE num_orden_compra = $1)";
    $result_proveedor = pg_query_params($conn, $query_proveedor, array($num_orden_compra));
    $proveedor = pg_fetch_assoc($result_proveedor);

    $query_productos = "SELECT p.cod_producto, p.nombre_producto, t.cantidad 
                        FROM tiene3 t 
                        JOIN producto p ON t.cod_producto = p.cod_producto 
                        WHERE t.num_orden_compra = $1";
    $result_productos = pg_query_params($conn, $query_productos, array($num_orden_compra));
    $productos = pg_fetch_all($result_productos);

    pg_close($conn);

    class PDF extends FPDF {
        private $num_orden_compra;

        function __construct($num_orden_compra) {
            parent::__construct();
            $this->num_orden_compra = $num_orden_compra;
        }

        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode('Orden de Compra Número: ' . $this->num_orden_compra), 0, 1, 'C');
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

    $pdf = new PDF($num_orden_compra);
    $pdf->AddPage();

    // Detalles de la Orden de Compra
    $pdf->ChapterTitle('Detalles de la Orden de Compra');
    $orden_data = [
        ['Proveedor', utf8_decode($proveedor['nombre_proveedor'])],
        ['Costo Total', utf8_decode($orden['costo_total'])],
        ['Descripción', utf8_decode($orden['descripcion_orden'])],
        ['Cantidad Solicitada', utf8_decode($orden['cantidad_solicitada'])],
        ['Fecha Requerida', utf8_decode($orden['fecha_requerida'])],
        ['Fecha Promesa', utf8_decode($orden['fecha_promesa'])],
        ['Fecha Compra', utf8_decode($orden['fecha_compra'])]
    ];
    $pdf->Table(['Campo', 'Valor'], $orden_data);

    // Detalles de los Productos
    $pdf->ChapterTitle('Detalles de Productos');
    $header = ['Código del Producto', 'Nombre del Producto', 'Cantidad'];
    $data = [];
    if ($productos) {
        foreach ($productos as $producto) {
            $data[] = [utf8_decode($producto['cod_producto']), utf8_decode($producto['nombre_producto']), utf8_decode($producto['cantidad'])];
        }
    } else {
        $data[] = ['No hay productos en esta orden.', '', ''];
    }
    $pdf->Table($header, $data);

    // Enviar el PDF al navegador como inline
    $pdf->Output('I', 'orden_compra_' . $num_orden_compra . '.pdf');
} else {
    echo "No se proporcionó un número de orden de compra.";
}
?>
