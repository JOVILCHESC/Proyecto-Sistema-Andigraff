<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$tra_rut_usuario = $_SESSION['rut'];

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
$query = "SELECT num_cotizacion, rut, tra_rut, fecha_cotizacion, monto_total, descripcion_cotizacion, estado_cotizacion 
          FROM cotizacion 
          WHERE estado_cotizacion = TRUE"; // Mostrar solo cotizaciones activas

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
                echo "<td>" . (htmlspecialchars($row['estado_cotizacion']) === 't' ? 'Activo' : 'Inactivo') . "</td>";
                echo "<td class='actions'>";
                echo "<a href='../Actualizar Cotizacion/actualizar_cotizacion_form.php?num_cotizacion=" . htmlspecialchars($row['num_cotizacion']) . "'><i class='fas fa-edit'></i></a>";
                echo "<a href='../Eliminar Cotizaciones/eliminar_cotizaciones.php?num_cotizacion=" . htmlspecialchars($row['num_cotizacion']) . "'><i class='fas fa-trash'></i></a>";
                echo "</td>";
                echo "</tr>";
            }
            pg_free_result($result);
            pg_close($conn);
            ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#cotizaciones').DataTable();
        });
    </script>
</body>
</html>
