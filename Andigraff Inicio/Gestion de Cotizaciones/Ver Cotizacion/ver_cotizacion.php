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

if (isset($_GET['num_cotizacion'])) {
    $num_cotizacion = pg_escape_string($conn, $_GET['num_cotizacion']);
    $query = "SELECT * FROM cotizacion WHERE num_cotizacion = '$num_cotizacion'";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error en la consulta: " . pg_last_error($conn);
        exit();
    }

    if (pg_num_rows($result) > 0) {
        $cotizacion = pg_fetch_assoc($result);

        // Obtener productos asociados a la cotización
        $queryProductos = "SELECT p.cod_producto, p.nombre_producto, p.precio_unitario 
                           FROM tiene2 t 
                           JOIN producto p ON t.cod_producto = p.cod_producto 
                           WHERE t.num_cotizacion = '$num_cotizacion'";
        $resultProductos = pg_query($conn, $queryProductos);

        if (!$resultProductos) {
            echo "Error en la consulta de productos: " . pg_last_error($conn);
            exit();
        }
        $productos = pg_fetch_all($resultProductos);

        // Calcular subtotal y total con impuesto
        $subtotal = 0;
        foreach ($productos as $producto) {
            $subtotal += $producto['precio_unitario'];
        }
        $impuesto = 0.19;
        $totalConImpuesto = $subtotal * (1 + $impuesto);

        // Obtener datos del cliente
        $queryCliente = "SELECT * FROM cliente WHERE rut = '" . pg_escape_string($cotizacion['rut']) . "'";
        $resultCliente = pg_query($conn, $queryCliente);

        if (!$resultCliente) {
            echo "Error en la consulta del cliente: " . pg_last_error($conn);
            exit();
        }
        $cliente = pg_fetch_assoc($resultCliente);

    } else {
        echo "Cotización no encontrada.";
        exit();
    }
} else {
    echo "Número de cotización no proporcionado.";
    exit();
}

if (isset($_POST['print'])) {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode('Cotización'), 0, 1, 'C');
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo()), 0, 0, 'C');
        }

        function Table($header, $data) {
            $this->SetFont('Arial', 'B', 12);
            foreach($header as $col) {
                $this->Cell(60, 10, utf8_decode($col), 1);
            }
            $this->Ln();
            $this->SetFont('Arial', '', 12);
            foreach($data as $row) {
                foreach($row as $cell) {
                    $this->Cell(60, 10, utf8_decode($cell), 1);
                }
                $this->Ln();
            }
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Información de la cotización
    $cotizacionHeader = array('Campo', 'Valor');
    $cotizacionData = array(
        array('Número de Cotización', $cotizacion['num_cotizacion']),
        array('Fecha Cotización', $cotizacion['fecha_cotizacion']),
        array('Monto Total', $cotizacion['monto_total']),
        array('Descripción', $cotizacion['descripcion_cotizacion'])
    );

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Información de la Cotización'));
    $pdf->Ln();
    $pdf->Table($cotizacionHeader, $cotizacionData);
    $pdf->Ln(10);

    // Datos del Cliente
    $clienteHeader = array('Campo', 'Valor');
    $clienteData = array(
        array('RUT', $cliente['rut']),
        array('Nombre', $cliente['nombre']),
        array('Apellido Paterno', $cliente['apellido_paterno']),
        array('Apellido Materno', $cliente['apellido_materno']),
        array('Email', $cliente['email']),
        array('Teléfono', $cliente['telefono_usuario']),
        array('Dirección', $cliente['calle'] . ' ' . $cliente['numero']),
        array('Comuna', $cliente['comuna']),
        array('Fecha de Nacimiento', $cliente['fecha_nacimiento']),
        array('Sexo', $cliente['sexo']),
        array('Tipo de Cliente', $cliente['tipo_cliente'])
    );

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Datos del Cliente'));
    $pdf->Ln();
    $pdf->Table($clienteHeader, $clienteData);
    $pdf->Ln(10);

    // Agregar productos al PDF
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Productos Asociados:'));
    $pdf->Ln();
    $productHeader = array('Código Producto', 'Nombre Producto', 'Precio Unitario');
    $productData = array();
    foreach ($productos as $producto) {
        $productData[] = array(
            $producto['cod_producto'],
            $producto['nombre_producto'],
            '$' . number_format($producto['precio_unitario'], 2)
        );
    }
    $pdf->Table($productHeader, $productData);

    $pdf->Ln();
    $pdf->Cell(0, 10, utf8_decode('Subtotal: $' . number_format($subtotal, 2)));
    $pdf->Ln();
    $pdf->Cell(0, 10, utf8_decode('Total con Impuesto (19%): $' . number_format($totalConImpuesto, 2)));
    $pdf->Ln();

    $pdf->Output('D', 'cotizacion_' . $cotizacion['num_cotizacion'] . '.pdf');
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cotización</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Detalles de Cotización</h2>
    <table>
        <thead>
            <tr>
                <th>Criterio</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Número de Cotización</td>
                <td><?php echo htmlspecialchars($cotizacion['num_cotizacion']); ?></td>
            </tr>
            <tr>
                <td>Fecha Cotización</td>
                <td><?php echo htmlspecialchars($cotizacion['fecha_cotizacion']); ?></td>
            </tr>
            <tr>
                <td>Monto Total</td>
                <td><?php echo htmlspecialchars($cotizacion['monto_total']); ?></td>
            </tr>
            <tr>
                <td>Descripción</td>
                <td><?php echo htmlspecialchars($cotizacion['descripcion_cotizacion']); ?></td>
            </tr>
        </tbody>
    </table>

    <h3>Datos del Cliente</h3>
    <table>
        <thead>
            <tr>
                <th>Campo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>RUT</td>
                <td><?php echo htmlspecialchars($cliente['rut']); ?></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
            </tr>
            <tr>
                <td>Apellido Paterno</td>
                <td><?php echo htmlspecialchars($cliente['apellido_paterno']); ?></td>
            </tr>
            <tr>
                <td>Apellido Materno</td>
                <td><?php echo htmlspecialchars($cliente['apellido_materno']); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
            </tr>
            <tr>
                <td>Teléfono</td>
                <td><?php echo htmlspecialchars($cliente['telefono_usuario']); ?></td>
            </tr>
            <tr>
                <td>Dirección</td>
                <td><?php echo htmlspecialchars($cliente['calle']) . ' ' . htmlspecialchars($cliente['numero']); ?></td>
            </tr>
            <tr>
                <td>Comuna</td>
                <td><?php echo htmlspecialchars($cliente['comuna']); ?></td>
            </tr>
            <tr>
                <td>Fecha de Nacimiento</td>
                <td><?php echo htmlspecialchars($cliente['fecha_nacimiento']); ?></td>
            </tr>
            <tr>
                <td>Sexo</td>
                <td><?php echo htmlspecialchars($cliente['sexo']); ?></td>
            </tr>
            <tr>
                <td>Tipo de Cliente</td>
                <td><?php echo htmlspecialchars($cliente['tipo_cliente']); ?></td>
            </tr>
        </tbody>
    </table>

    <h3>Productos Asociados</h3>
    <table>
        <thead>
            <tr>
                <th>Código Producto</th>
                <th>Nombre Producto</th>
                <th>Precio Unitario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($producto['cod_producto']); ?></td>
                    <td><?php echo htmlspecialchars($producto['nombre_producto']); ?></td>
                    <td><?php echo '$' . number_format($producto['precio_unitario'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Subtotal:</strong> $<?php echo number_format($subtotal, 2); ?></p>
    <p><strong>Total con Impuesto (19%):</strong> $<?php echo number_format($totalConImpuesto, 2); ?></p>

    <form method="post">
        <input type="submit" name="print" class="button" value="Generar PDF">
    </form>
</body>
</html>
