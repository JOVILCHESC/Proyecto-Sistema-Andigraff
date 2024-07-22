<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$tra_rut_usuario = $_SESSION['rut'];

$host = "146.83.165.21";
$port = "5432";
$dbname = "bsilvestre"; // Replace with your actual database name
$user = "bsilvestre"; // Replace with your actual username
$password = "druIvAfaf4"; // Replace with your actual password

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Construir la consulta SQL base
$query = "SELECT num_cotizacion, rut, tra_rut, fecha_cotizacion, monto_total, descripcion_cotizacion, estado_cotizacion FROM cotizacion";

// Aplicar filtros si están presentes
$filters = [];

if (isset($_GET['filterRutCliente']) && !empty($_GET['filterRutCliente'])) {
    $filterRutCliente = pg_escape_string($conn, $_GET['filterRutCliente']);
    $filters[] = "rut ILIKE '%$filterRutCliente%'";
}

if (isset($_GET['filterFechaCotizacion']) && !empty($_GET['filterFechaCotizacion'])) {
    $filterFechaCotizacion = pg_escape_string($conn, $_GET['filterFechaCotizacion']);
    $filters[] = "fecha_cotizacion::date = '$filterFechaCotizacion'";
}

if (isset($_GET['filterEstadoCotizacion']) && $_GET['filterEstadoCotizacion'] !== '') {
    $filterEstadoCotizacion = pg_escape_string($conn, $_GET['filterEstadoCotizacion']);
    $filters[] = "estado_cotizacion = " . ($filterEstadoCotizacion === 'true' ? 'TRUE' : 'FALSE');
}

// Añadir filtros a la consulta
if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
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
    <title>Ver Cotizaciones</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
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
        .filters input[type="text"],
        .filters input[type="date"],
        .filters select {
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <h2>Cotizaciones Registradas</h2>
    
    <!-- Filtros -->
    <div class="filters">
        <form method="get" action="">
            <label for="filterRutCliente">Filtrar por RUT del Cliente:</label>
            <input type="text" id="filterRutCliente" name="filterRutCliente" value="<?php echo isset($_GET['filterRutCliente']) ? htmlspecialchars($_GET['filterRutCliente']) : ''; ?>">

            <label for="filterFechaCotizacion">Filtrar por Fecha de Cotización:</label>
            <input type="date" id="filterFechaCotizacion" name="filterFechaCotizacion" value="<?php echo isset($_GET['filterFechaCotizacion']) ? htmlspecialchars($_GET['filterFechaCotizacion']) : ''; ?>">

            <label for="filterEstadoCotizacion">Filtrar por Estado de Cotización:</label>
            <select id="filterEstadoCotizacion" name="filterEstadoCotizacion">
                <option value="">Seleccione un estado</option>
                <option value="true" <?php echo (isset($_GET['filterEstadoCotizacion']) && $_GET['filterEstadoCotizacion'] === 'true') ? 'selected' : ''; ?>>Activo</option>
                <option value="false" <?php echo (isset($_GET['filterEstadoCotizacion']) && $_GET['filterEstadoCotizacion'] === 'false') ? 'selected' : ''; ?>>Inactivo</option>
            </select>

            <button type="submit">Filtrar</button>
        </form>
    </div>

    <table id="cotizaciones" class="display">
        <thead>
            <tr>
                <th>ID de Cotización</th>
                <th>RUT del Cliente</th>
                <th>RUT del Trabajador</th>
                <th>Fecha de Cotización</th>
                <th>Monto Total</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = pg_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['num_cotizacion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rut']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tra_rut']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_cotizacion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['monto_total']) . "</td>";
                echo "<td>" . htmlspecialchars($row['descripcion_cotizacion']) . "</td>";
                echo "<td>" . ($row['estado_cotizacion'] ? 'Activo' : 'Inactivo') . "</td>";
                echo "<td class='actions'>
                    <a href='../Eliminar Cotizaciones/eliminar_cotizacion.php?num_cotizacion=" . htmlspecialchars($row['num_cotizacion']) . "' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta cotización?\");'><i class='fas fa-trash'></i></a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="../Crear Cotizaciones/crear_cotizacion_form.php" class="button">Registrar Nueva Cotización</a>
    <a href="../../../sidebar/sidebar.html" class="button regresar">Regresar al Inicio</a>

    <!-- Script para DataTables y filtros -->
    <script>
        $(document).ready(function() {
            var table = $('#cotizaciones').DataTable();

            // Filtros personalizados
            $('#filterRutCliente').on('keyup', function() {
                table.column(1).search(this.value).draw(); // Filtra por RUT del cliente
            });

            $('#filterFechaCotizacion').on('change', function() {
                table.column(3).search(this.value).draw(); // Filtra por fecha de cotización
            });

            $('#filterEstadoCotizacion').on('change', function() {
                table.column(6).search(this.value === 'true' ? 'Activo' : this.value === 'false' ? 'Inactivo' : '').draw(); // Filtra por estado
            });
        });
    </script>
</body>
</html>

<?php
// Cerrar la conexión
pg_close($conn);
?>

