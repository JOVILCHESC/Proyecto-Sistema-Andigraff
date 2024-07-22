<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden de Compra</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/ver_orden_compra.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            font-size: 16px;
            text-align: center;
            display: block;
            text-decoration: none;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .print-button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Detalle de Orden de Compra</h1>
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Obtener el número de orden de compra desde la URL
    $num_orden_compra = $_GET['num_orden_compra'] ?? null;

    if ($num_orden_compra) {
        // Consultar la orden de compra
        $query_orden = "SELECT o.num_orden_compra, p.nombre_proveedor, o.rut, o.tipo_comprobante, o.costo_total, o.descripcion_orden, o.cantidad_solicitada, o.fecha_requerida, o.fecha_promesa, o.fecha_compra 
                         FROM orden_compra o 
                         JOIN proveedor p ON o.id_proveedor = p.id_proveedor 
                         WHERE o.num_orden_compra = $1 AND o.estado_compra = true";
        $result_orden = pg_query_params($conn, $query_orden, array($num_orden_compra));

        if ($result_orden && pg_num_rows($result_orden) > 0) {
            $order = pg_fetch_assoc($result_orden);

            echo "<table>
                    <tr><th>ID</th><td>{$order['num_orden_compra']}</td></tr>
                    <tr><th>Proveedor</th><td>{$order['nombre_proveedor']}</td></tr>
                    <tr><th>RUT</th><td>{$order['rut']}</td></tr>
                    <tr><th>Tipo de Comprobante</th><td>{$order['tipo_comprobante']}</td></tr>
                    <tr><th>Costo Total</th><td>{$order['costo_total']}</td></tr>
                    <tr><th>Descripción</th><td>{$order['descripcion_orden']}</td></tr>
                    <tr><th>Cantidad Solicitada</th><td>{$order['cantidad_solicitada']}</td></tr>
                    <tr><th>Fecha Requerida</th><td>{$order['fecha_requerida']}</td></tr>
                    <tr><th>Fecha Promesa</th><td>{$order['fecha_promesa']}</td></tr>
                    <tr><th>Fecha Compra</th><td>{$order['fecha_compra']}</td></tr>
                  </table>";

            // Consultar los productos asociados a la orden de compra
            $query_productos = "SELECT p.cod_producto, p.nombre_producto, t.cantidad 
                                FROM tiene3 t 
                                JOIN producto p ON t.cod_producto = p.cod_producto 
                                WHERE t.num_orden_compra = $1";
            $result_productos = pg_query_params($conn, $query_productos, array($num_orden_compra));

            if ($result_productos && pg_num_rows($result_productos) > 0) {
                echo "<h2>Productos en la Orden</h2>
                      <table>
                        <thead>
                            <tr>
                                <th>Código de Producto</th>
                                <th>Nombre del Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($producto = pg_fetch_assoc($result_productos)) {
                    echo "<tr>
                            <td>{$producto['cod_producto']}</td>
                            <td>{$producto['nombre_producto']}</td>
                            <td>{$producto['cantidad']}</td>
                          </tr>";
                }

                echo "  </tbody>
                      </table>";
            } else {
                echo "<p>No hay productos asociados a esta orden de compra.</p>";
            }
        } else {
            echo "<p>No se encontró la orden de compra.</p>";
        }
    } else {
        echo "<p>ID de orden de compra no proporcionado.</p>";
    }

    // Cerrar la conexión
    pg_close($conn);
    ?>

    <a href="../Lista Orden de Compra/lista_orden_compra.php" class="button">Regresar a la Lista</a>
    <button class="print-button" onclick="window.print();">Imprimir Orden</button>
</body>
</html>
