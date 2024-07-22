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

// Construir la consulta SQL base
$query = "SELECT numero_factura, lugar_emision, fecha_emision_factura, descripcion_operacion_factura, direccion_entrega_factura, rut FROM factura WHERE estado_factura = TRUE";

// Aplicar filtros si están presentes
$filters = [];

if (isset($_GET['filterNumeroFactura']) && !empty($_GET['filterNumeroFactura'])) {
    $filterNumeroFactura = pg_escape_string($conn, $_GET['filterNumeroFactura']);
    $filters[] = "numero_factura ILIKE '%$filterNumeroFactura%'";
}

if (isset($_GET['filterLugarEmision']) && !empty($_GET['filterLugarEmision'])) {
    $filterLugarEmision = pg_escape_string($conn, $_GET['filterLugarEmision']);
    $filters[] = "lugar_emision ILIKE '%$filterLugarEmision%'";
}

if (isset($_GET['filterFechaEmision']) && !empty($_GET['filterFechaEmision'])) {
    $filterFechaEmision = pg_escape_string($conn, $_GET['filterFechaEmision']);
    $filters[] = "fecha_emision_factura::date = '$filterFechaEmision'";
}

// Añadir filtros a la consulta
if (!empty($filters)) {
    $query .= " AND " . implode(" AND ", $filters);
}

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
        .filters {
            margin-bottom: 20px;
        }
        .filters label {
            margin-right: 10px;
        }
        .filters input[type="text"] {
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <h2>Facturas Creadas</h2>
    
    <!-- Filtros -->
    <div class="filters">
        <form method="get" action="">
            <label for="filterNumeroFactura">Filtrar por Número de Factura:</label>
            <input type="text" id="filterNumeroFactura" name="filterNumeroFactura" value="<?php echo isset($_GET['filterNumeroFactura']) ? htmlspecialchars($_GET['filterNumeroFactura']) : ''; ?>">

            <label for="filterLugarEmision">Filtrar por Lugar de Emisión:</label>
            <input type="text" id="filterLugarEmision" name="filterLugarEmision" value="<?php echo isset($_GET['filterLugarEmision']) ? htmlspecialchars($_GET['filterLugarEmision']) : ''; ?>">

            <label for="filterFechaEmision">Filtrar por Fecha de Emisión:</label>
            <input type="date" id="filterFechaEmision" name="filterFechaEmision" value="<?php echo isset($_GET['filterFechaEmision']) ? htmlspecialchars($_GET['filterFechaEmision']) : ''; ?>">

            <button type="submit">Filtrar</button>
        </form>
    </div>

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
                    <a href='../Ver Factura/ver_factura.php?numero_factura=" . htmlspecialchars($row['numero_factura']) . "' title='Ver'><i class='fas fa-eye'></i></a>
                    <a href='../Eliminar Facturas/eliminar_factura.php?numero_factura=" . htmlspecialchars($row['numero_factura']) . "' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta factura?\");'><i class='fas fa-trash'></i></a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="../crear Factura/crear_factura_form.php" class="button">Crear Nueva Factura</a>
    <a href="../../sidebar/sidebar.html" class="button">Regresar al Inicio</a>

    <!-- Script para DataTables y filtros -->
    <script>
        $(document).ready(function() {
            $('#facturas').DataTable({
                "language": {
                    "sEmptyTable": "No hay datos disponibles en la tabla",
                    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                    "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ",",
                    "sLengthMenu": "Mostrar _MENU_ entradas",
                    "sLoadingRecords": "Cargando...",
                    "sProcessing": "Procesando...",
                    "sSearch": "Buscar:",
                    "sZeroRecords": "No se encontraron resultados",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });

            // Filtros personalizados
            $('#filterNumeroFactura').on('keyup', function() {
                $('#facturas').DataTable().column(0).search(this.value).draw(); // Filtra por número de factura
            });

            $('#filterLugarEmision').on('keyup', function() {
                $('#facturas').DataTable().column(1).search(this.value).draw(); // Filtra por lugar de emisión
            });

            $('#filterFechaEmision').on('change', function() {
                $('#facturas').DataTable().column(2).search(this.value).draw(); // Filtra por fecha de emisión
            });
        });
    </script>
</body>
</html>

<?php
// Liberar el resultado si existe
if (isset($result)) {
    pg_free_result($result);
}

// Cerrar la conexión si existe
if (isset($conn)) {
    pg_close($conn);
}
?>
