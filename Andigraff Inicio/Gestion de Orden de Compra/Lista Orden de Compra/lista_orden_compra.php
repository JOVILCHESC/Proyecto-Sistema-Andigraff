<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Órdenes de Compra</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilo básico para los iconos de los botones */
        .actions {
            text-align: center;
        }
        .actions a {
            color: black;
            margin: 0 5px;
            text-decoration: none;
        }
        .actions a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>Lista de Órdenes de Compra</h1>
    
    <?php
    // PostgreSQL connection parameters
    $host = "magallanes.inf.unap.cl";
    $port = "5432";
    $dbname = "jvilches"; // Replace with your actual database name
    $user = "jvilches"; // Replace with your actual username
    $password = "wEtbEQzH6v44"; // Replace with your actual password

    // Create connection string
    $connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

    // Function to connect to the database
    function getDBConnection() {
        global $connectionString;
        $connect = pg_connect($connectionString);

        if (!$connect) {
            die('Error al conectar a la base de datos');
        }

        return $connect;
    }

    // Fetch data from the database
    function fetchOrders() {
        $connection = getDBConnection();
        $query = 'SELECT num_orden_compra, id_proveedor, rut, tipo_comprobante, costo_total, descripcion_orden, cantidad_solicitada, fecha_requerida, estado_compra, fecha_promesa, fecha_compra FROM orden_compra';
        $result = pg_query($connection, $query);

        if (!$result) {
            echo "Error en la consulta.";
            return [];
        }

        $orders = [];
        while ($row = pg_fetch_assoc($result)) {
            $orders[] = $row;
        }

        return $orders;
    }

    // Display the data
    $orders = fetchOrders();
    if (empty($orders)) {
        echo "<p>No hay órdenes de compra disponibles.</p>";
    } else {
        echo "<table border='1'>
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
                        <th>Estado</th>
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
                    <td>" . ($order['estado_compra'] ? 'Comprado' : 'No Comprado') . "</td>
                    <td>{$order['fecha_promesa']}</td>
                    <td>{$order['fecha_compra']}</td>
                    <td class='actions'>
                        <a href='ver_orden_compra.php?id={$order['num_orden_compra']}' title='Ver'><i class='fas fa-eye'></i></a>
                        <a href='../Actualizar Orden de Compra/actualizar_orden_compra_form.php?id={$order['num_orden_compra']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='eliminar_orden_compra.php?id={$order['num_orden_compra']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta orden?\");'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    }

    // Close the connection
    pg_close(getDBConnection());
    ?>
</body>
</html>
