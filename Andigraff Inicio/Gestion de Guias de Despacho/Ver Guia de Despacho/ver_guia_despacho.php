<?php
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
} else {
    die("No se proporcionó un ID de guía de despacho.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Despacho</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Detalles de la Guía de Despacho</h1>
    <table>
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($guia['num_guia_despacho']); ?></td>
        </tr>
        <tr>
            <th>Dirección de Origen</th>
            <td><?php echo htmlspecialchars($guia['direccion_origen']); ?></td>
        </tr>
        <tr>
            <th>Dirección de Destino</th>
            <td><?php echo htmlspecialchars($guia['direccion_destino']); ?></td>
        </tr>
        <tr>
            <th>Condición de Entrega</th>
            <td><?php echo htmlspecialchars($guia['condicion_entrega'] ? 'Entregado' : 'No Entregado'); ?></td>
        </tr>
        <tr>
            <th>Fecha de Emisión</th>
            <td><?php echo htmlspecialchars($guia['fecha_emicion_guia_despacho']); ?></td>
        </tr>
    </table>

    <h2>Detalles de los Productos</h2>
    <table>
        <thead>
            <tr>
                <th>Código del Producto</th>
                <th>Nombre del Producto</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($productos) {
                foreach ($productos as $producto) {
                    echo "<tr>
                            <td>" . htmlspecialchars($producto['cod_producto']) . "</td>
                            <td>" . htmlspecialchars($producto['nombre_producto']) . "</td>
                            <td>" . htmlspecialchars($producto['cantidad']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay productos para esta guía de despacho.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="generar_guia_despacho_pdf.php?id=<?php echo htmlspecialchars($num_guia_despacho); ?>" class="button">Imprimir Guía</a>
    <a href="../../sidebar/sidebar.html" class="button">Regresar al Inicio</a>
</body>
</html>
