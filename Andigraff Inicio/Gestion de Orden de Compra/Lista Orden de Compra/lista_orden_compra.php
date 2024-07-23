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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
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
    $query = "SELECT o.num_orden_compra, p.nombre_proveedor, o.rut, o.tipo_comprobante, o.costo_total, o.descripcion_orden, o.cantidad_solicitada, o.fecha_requerida, o.fecha_promesa, o.fecha_compra 
              FROM orden_compra o 
              JOIN proveedor p ON o.id_proveedor = p.id_proveedor 
              WHERE o.estado_compra = true";
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
        echo "<table id='ordenesTable'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proveedor</th>
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
                    <td>{$order['nombre_proveedor']}</td>
                    <td>{$order['rut']}</td>
                    <td>{$order['tipo_comprobante']}</td>
                    <td>{$order['costo_total']}</td>
                    <td>{$order['descripcion_orden']}</td>
                    <td>{$order['cantidad_solicitada']}</td>
                    <td>{$order['fecha_requerida']}</td>
                    <td>{$order['fecha_promesa']}</td>
                    <td>{$order['fecha_compra']}</td>
                    <td class='actions'>
                        <a href='../Actualizar Orden de Compra/actualizar_orden_compra_form.php?id={$order['num_orden_compra']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='../Eliminar Orden de Compra/eliminar_orden_compra.php?id={$order['num_orden_compra']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta orden?\");'><i class='fas fa-trash'></i></a>
                        <a href='../Ver Orden de Compra/ver_orden_de_compra.php?num_orden_compra={$order['num_orden_compra']}' class='view-button' title='Ver Orden de Compra'><i class='fas fa-eye'></i></a>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    }

    // Cerrar la conexión
    pg_close($conn);
    ?>
    <a href="../../sidebar/sidebar.html" class="button">Regresar al Inicio</a>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ordenesTable').DataTable();
        });
    </script>
</body>
</html>

