<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Guías de Despacho</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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
    </style>
</head>
<body>
    <h1>Lista de Guías de Despacho</h1>
    
    <table id="guidesTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Dirección de Origen</th>
                <th>Dirección de Destino</th>
                <th>Condición de Entrega</th>
                <th>Fecha de Emisión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // PostgreSQL connection parameters
            $host = "146.83.165.21";
            $port = "5432";
            $dbname = "jvilches";
            $user = "jvilches";
            $password = "wEtbEQzH6v44";

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
            function fetchGuides() {
                $connection = getDBConnection();
                $query = 'SELECT num_guia_despacho, direccion_origen, direccion_destino, condicion_entrega, estado_despacho, fecha_emicion_guia_despacho 
                          FROM guia_despacho
                          WHERE estado_despacho = true'; // Filtra por estado_despacho = true
                $result = pg_query($connection, $query);

                if (!$result) {
                    echo "Error en la consulta.";
                    return [];
                }

                $guides = [];
                while ($row = pg_fetch_assoc($result)) {
                    // Convert 'condicion_entrega' from string to boolean
                    $row['condicion_entrega'] = ($row['condicion_entrega'] === 't') ? true : false;
                    $guides[] = $row;
                }

                return $guides;
            }

            // Display the data
            $guides = fetchGuides();
            if (empty($guides)) {
                echo "<p>No hay guías de despacho disponibles.</p>";
            } else {
                foreach ($guides as $guide) {
                    echo "<tr>
                            <td>{$guide['num_guia_despacho']}</td>
                            <td>{$guide['direccion_origen']}</td>
                            <td>{$guide['direccion_destino']}</td>
                            <td>" . ($guide['condicion_entrega'] ? 'Entregado' : 'No Entregado') . "</td>
                            <td>{$guide['fecha_emicion_guia_despacho']}</td>
                            <td class='actions'>
                                <a href='../Actualizar Guia de Despacho/actualizar_guia_despacho_form.php?id={$guide['num_guia_despacho']}' title='Editar'><i class='fas fa-edit'></i></a>
                                <a href='../Eliminar Guias de Despacho/eliminar_guia_despacho.php?id={$guide['num_guia_despacho']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta guía?\");'><i class='fas fa-trash'></i></a>
                            </td>
                          </tr>";
                }
            }

            // Close the connection
            pg_close(getDBConnection());
            ?>
        </tbody>
    </table>
    
    <a href="../../sidebar/sidebar.html" class="button">Regresar al Inicio</a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            $('#guidesTable').DataTable();
        });
    </script>
</body>
</html>

