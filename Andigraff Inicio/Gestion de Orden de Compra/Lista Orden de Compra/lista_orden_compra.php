<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Órdenes de Compra</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/ver_orden_compra.css">
</head>
<body>
    <h1>Lista de Órdenes de Compra</h1>
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Consultar las órdenes de compra que no están eliminadas (estado_compra = true)
    $query = "SELECT num_orden_compra, id_proveedor, rut, tipo_comprobante, costo_total, descripcion_orden, cantidad_solicitada, fecha_requerida, fecha_promesa, fecha_compra FROM orden_compra WHERE estado_compra = true";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error en la consulta.";
        return [];
    }

    $orders = [];
    while ($row = pg_fetch_assoc($result)) {
        $orders[] = $row;
    }

    if (empty($orders)) {
        echo "<p>No hay órdenes de compra disponibles.</p>";
    } else {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Proveedor</th>
                        <th>RUT</th>
                        <th>Tipo de Comprobante</th>
                        <th>Costo Total</th>
                        <th>Descripción</th>
                        <th>Cantidad Solicitada</th>
                        <th>Fecha Requerida</th>
                        <th>Fecha Promesa</th>
                        <th>Fecha Compra</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($orders as $order) {
            echo "<tr>
                    <td>{$order['num_orden_compra']}</td>
                    <td>{$order['id_proveedor']}</td>
                    <td>{$order['rut']}</td>
                    <td>{$order['tipo_comprobante']}</td>
                    <td>{$order['costo_total']}</td>
                    <td>{$order['descripcion_orden']}</td>
                    <td>{$order['cantidad_solicitada']}</td>
                    <td>{$order['fecha_requerida']}</td>
                    <td>{$order['fecha_promesa']}</td>
                    <td>{$order['fecha_compra']}</td>
                    <td class='actions'>
                        <a href='ver_orden_compra.php?id={$order['num_orden_compra']}' title='Ver'><i class='fas fa-eye'></i></a>
                        <a href='../Actualizar Orden de Compra/actualizar_orden_compra_form.php?id={$order['num_orden_compra']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='../Eliminar Orden de Compra/eliminar_orden_compra.php?id={$order['num_orden_compra']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta orden?\");'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    }

    // Cerrar la conexión
    pg_close($conn);
    ?>
</body>
</html>




