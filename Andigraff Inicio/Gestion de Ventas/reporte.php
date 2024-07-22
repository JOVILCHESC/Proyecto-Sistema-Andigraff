<?php
$dbconn = pg_pconnect("host=146.83.165.21 dbname=jvilches user=jvilches password=wEtbEQzH6v44");

if (!$dbconn) {
    die("No se pudo conectar a la base de datos.");
}

$year1 = isset($_GET['year1']) ? intval($_GET['year1']) : date('Y') - 1;
$year2 = isset($_GET['year2']) ? intval($_GET['year2']) : date('Y');

// Obtener datos de ventas para los años seleccionados y la proyección para 2023
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

// Proyección de ventas para 2024 
$projection2023 = [
    1 => 550000, 2 => 650000, 3 => 480000, 4 => 550000,
    5 => 810000, 6 => 570000, 7 => 150000, 8 => 730000,
    9 => 630000, 10 => 770000, 11 => 920000, 12 => 120000
];

pg_close($dbconn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            background-color: #ffcc99;
        }
    </style>
</head>
<body>
    <h1>Reporte de ventas<br>"Andigraff"</h1>

    <form method="GET" action="">
        <label for="year1">Seleccione el primer año:</label>
        <select id="year1" name="year1">
            <?php
            for ($i = 2000; $i <= date('Y'); $i++) {
                echo "<option value='$i'" . ($year1 == $i ? ' selected' : '') . ">$i</option>";
            }
            ?>
        </select>
        <label for="year2">Seleccione el segundo año:</label>
        <select id="year2" name="year2">
            <?php
            for ($i = 2000; $i <= date('Y'); $i++) {
                echo "<option value='$i'" . ($year2 == $i ? ' selected' : '') . ">$i</option>";
            }
            ?>
        </select>
        <button type="submit">Generar Reporte</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>MESES</th>
                <th>VENTAS <?php echo $year1; ?></th>
                <th>VENTAS <?php echo $year2; ?></th>
                <th>PROYECCIÓN 2024</th>
            </tr>
        </thead>
        <tbody>
            <?php
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

                echo "<tr>
                        <td style='text-align:left;'>{$mes}</td>
                        <td>" . number_format($ventasYear1, 2) . "</td>
                        <td>" . number_format($ventasYear2, 2) . "</td>
                        <td>" . number_format($ventas2023, 2) . "</td>
                      </tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="total">
                <td style="text-align:left;">TOTAL</td>
                <td><?php echo number_format($totalYear1, 2); ?></td>
                <td><?php echo number_format($totalYear2, 2); ?></td>
                <td><?php echo number_format($total2023, 2); ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

