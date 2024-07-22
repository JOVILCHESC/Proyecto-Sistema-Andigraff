<?php
header('Content-Type: application/json');

$dbconn = pg_pconnect("host=146.83.165.21 dbname=jvilches user=jvilches password=wEtbEQzH6v44");

if (!$dbconn) {
    echo json_encode(['error' => 'No se pudo conectar a la base de datos.']);
    exit;
}

$reportType = isset($_GET['reportType']) ? $_GET['reportType'] : 'year';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

$query = "SELECT EXTRACT(YEAR FROM dv.fecha) AS año, 
                 p.nombre_producto, 
                 dv.cod_producto, 
                 SUM(dv.cantidad_orden) AS total_vendido
          FROM public.venta v
          JOIN public.detalle_venta dv ON v.cod_venta = dv.cod_venta
          JOIN public.producto p ON dv.cod_producto = p.cod_producto";

if ($reportType == 'month' && $year && $month) {
    // Calcula el primer y último día del mes
    $startDate = "$year-$month-01";
    $endDate = date("Y-m-t", strtotime($startDate));
    $query .= " WHERE dv.fecha BETWEEN $1 AND $2";
} elseif ($reportType == 'year' && $year) {
    $query .= " WHERE EXTRACT(YEAR FROM dv.fecha) = $1";
} elseif ($reportType == 'range' && $startDate && $endDate) {
    $query .= " WHERE dv.fecha BETWEEN $1 AND $2";
}

$query .= " GROUP BY año, p.nombre_producto, dv.cod_producto
            ORDER BY año, dv.cod_producto";

// Ejecutar la consulta
if ($reportType == 'month' && $year && $month) {
    $result = pg_query_params($dbconn, $query, array($startDate, $endDate));
} elseif ($reportType == 'year' && $year) {
    $result = pg_query_params($dbconn, $query, array($year));
} elseif ($reportType == 'range' && $startDate && $endDate) {
    $result = pg_query_params($dbconn, $query, array($startDate, $endDate));
} else {
    $result = pg_query($dbconn, $query);
}

if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . pg_last_error($dbconn)]);
    exit;
}

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);

pg_close($dbconn);
?>
