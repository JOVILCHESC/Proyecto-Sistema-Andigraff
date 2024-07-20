<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$tra_rut_usuario = $_SESSION['rut'];

// Conectar a la base de datos
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Consulta para obtener las facturas
$query = "SELECT numero_factura, lugar_emision, fecha_emision_factura, descripcion_operacion_factura, direccion_entrega_factura, rut FROM factura";
$result = pg_query($conn, $query);

if (!$result) {
    echo "Error en la consulta: " . pg_last_error($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Facturas</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/ver_facturas.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#facturas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                },
                "paging": true,
                "searching": true,
                "info": true,
                "lengthMenu": [10, 25, 50, 75, 100],
                "columnDefs": [
                    { "orderable": false, "targets": 6 }
                ]
            });
        });
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            margin-bottom: 20px;
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
        table.dataTable {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        table.dataTable th,
        table.dataTable td {
            padding: 10px;
            text-align: center;
        }
        .actions a {
            margin: 0 5px;
            color: #000;
        }
        .actions a:hover {
            color: #007bff;
        }
        .actions .fas.fa-trash:hover {
            color: red;
        }
        .actions .fas.fa-edit:hover {
            color: orange;
        }
        .actions .fas.fa-eye:hover {
            color: green;
        }
    </style>
</head>
<body>
    <h2>Facturas Creadas</h2>
    <table id="facturas" class="display">
        <thead>
            <tr>
                <th>Número de Factura</th>
                <th>Lugar de Emisión</th>
                <th>Fecha de Emisión</th>
                <th>Descripción</th>
                <th>Dirección de Entrega</th>
                <th>RUT del Cliente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = pg_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['numero_factura']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lugar_emision']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_emision_factura']) . "</td>";
                echo "<td>" . htmlspecialchars($row['descripcion_operacion_factura']) . "</td>";
                echo "<td>" . htmlspecialchars($row['direccion_entrega_factura']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rut']) . "</td>";
                echo "<td class='actions'>
                    <a href='ver_factura.php?numero_factura=" . htmlspecialchars($row['numero_factura']) . "' title='Ver'><i class='fas fa-eye'></i></a>
                    <a href='actualizar_factura.php?numero_factura=" . htmlspecialchars($row['numero_factura']) . "' title='Editar'><i class='fas fa-edit'></i></a>
                    <a href='eliminar_factura.php?numero_factura=" . htmlspecialchars($row['numero_factura']) . "' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta factura?\");'><i class='fas fa-trash'></i></a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="crear_factura_form.php" class="button">Crear Nueva Factura</a>
</body>
</html>

<?php
// Liberar el resultado
pg_free_result($result);

// Cerrar la conexión
pg_close($conn);
?>
