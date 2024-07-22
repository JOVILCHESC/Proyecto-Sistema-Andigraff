<?php
require(__DIR__ . '/../lib/fpdf/fpdf.php');

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Título
        $this->Cell(0, 10, utf8_decode('Reporte de ventas "Andigraff"'), 0, 1, 'C');
        // Salto de línea
        $this->Ln(10);

        // Encabezado de la tabla
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, utf8_decode('Mes'), 1);
        $this->Cell(50, 10, utf8_decode('Ventas ' . $_GET['year1']), 1);
        $this->Cell(50, 10, utf8_decode('Ventas ' . $_GET['year2']), 1);
        $this->Cell(50, 10, utf8_decode('Proyección 2024'), 1);
        $this->Ln();
    }

    // Pie de página
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Conectar a la base de datos
$dbconn = pg_pconnect("host=146.83.165.21 dbname=jvilches user=jvilches password=wEtbEQzH6v44");

if (!$dbconn) {
    die("No se pudo conectar a la base de datos.");
}

$year1 = isset($_GET['year1']) ? intval($_GET['year1']) : date('Y') - 1;
$year2 = isset($_GET['year2']) ? intval($_GET['year2']) : date('Y');

// Obtener datos de ventas para los años seleccionados y la proyección para 2024
$query = "
    SELECT EXTRACT(YEAR FROM dv.fecha) AS año, 
           EXTRACT(MONTH FROM dv.fecha) AS mes,
           SUM(dv.cantidad_orden * p.precio_unitario) AS total_vendido
    FROM public.venta v
    JOIN public.detalle_venta dv ON v.cod_venta = dv.cod_venta
    JOIN public.producto p ON dv.cod_producto = p.cod_producto
    WHERE EXTRACT(YEAR FROM dv.fecha) IN ($year1, $year2)
    GROUP BY año, mes
    ORDER BY año, mes";

$result = pg_query($dbconn, $query);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($dbconn));
}

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[$row['año']][$row['mes']] = $row['total_vendido'];
}

pg_close($dbconn);

// Calcular la proyección de ventas para 2024 utilizando la tasa de crecimiento
$projection2023 = [];
for ($mes = 1; $mes <= 12; $mes++) {
    $ventasYear1 = isset($data[$year1][$mes]) ? $data[$year1][$mes] : 0;
    $ventasYear2 = isset($data[$year2][$mes]) ? $data[$year2][$mes] : 0;
    
    if ($ventasYear1 > 0) {
        $growthRate = ($ventasYear2 - $ventasYear1) / $ventasYear1;
    } else {
        $growthRate = 0;
    }
    
    $projection2023[$mes] = $ventasYear2 + ($ventasYear2 * $growthRate);
}

// Crear PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$meses = [1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'];
$totalYear1 = 0;
$totalYear2 = 0;
$total2023 = 0;

foreach ($meses as $num => $mes) {
    $ventasYear1 = isset($data[$year1][$num]) ? $data[$year1][$num] : 0;
    $ventasYear2 = isset($data[$year2][$num]) ? $data[$year2][$num] : 0;
    $ventas2023 = $projection2023[$num];

    $totalYear1 += $ventasYear1;
    $totalYear2 += $ventasYear2;
    $total2023 += $ventas2023;

    $pdf->Cell(40, 10, utf8_decode($mes), 1);
    $pdf->Cell(50, 10, number_format($ventasYear1, 2), 1);
    $pdf->Cell(50, 10, number_format($ventasYear2, 2), 1);
    $pdf->Cell(50, 10, number_format($ventas2023, 2), 1);
    $pdf->Ln();
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'TOTAL', 1);
$pdf->Cell(50, 10, number_format($totalYear1, 2), 1);
$pdf->Cell(50, 10, number_format($totalYear2, 2), 1);
$pdf->Cell(50, 10, number_format($total2023, 2), 1);

$pdf->Output('reporte_ventas.pdf', 'D');
?>

