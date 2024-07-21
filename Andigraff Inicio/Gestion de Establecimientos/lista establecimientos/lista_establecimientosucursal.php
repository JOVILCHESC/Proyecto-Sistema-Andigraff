<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Sucursales</title>
    <link rel="stylesheet" href="../styles/listado_sucursales.css"> 
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
<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Variables para almacenar los criterios de búsqueda
$search_criteria = [];

// Procesar el formulario de búsqueda si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['cod_establecimiento'])) {
        $search_criteria[] = "sucursal.cod_establecimiento = '" . pg_escape_string($conn, $_POST['cod_establecimiento']) . "'";
    }
    if (!empty($_POST['comuna_sucursal'])) {
        $search_criteria[] = "sucursal.comuna_establecimiento ILIKE '%" . pg_escape_string($conn, $_POST['comuna_sucursal']) . "%'";
    }
    if (!empty($_POST['calle_sucursal'])) {
        $search_criteria[] = "sucursal.calle_establecimiento ILIKE '%" . pg_escape_string($conn, $_POST['calle_sucursal']) . "%'";
    }
    if (!empty($_POST['nombre_sucursal'])) {
        $search_criteria[] = "sucursal.nombre_establecimiento ILIKE '%" . pg_escape_string($conn, $_POST['nombre_sucursal']) . "%'";
    }
    if (!empty($_POST['tipo_sucursal'])) {
        $search_criteria[] = "sucursal.tipo_sucursal ILIKE '%" . pg_escape_string($conn, $_POST['tipo_sucursal']) . "%'";
    }
}

// Construir la consulta SQL con los criterios de búsqueda si existen
$sql = "SELECT * FROM sucursal
        JOIN establecimiento ON sucursal.cod_establecimiento = establecimiento.cod_establecimiento";

if (!empty($search_criteria)) {
    $sql .= " WHERE " . implode(' AND ', $search_criteria);
}

$result = pg_query($conn, $sql);

if ($result) {
    echo "<h1>Listado de Sucursales</h1>";

    // Formulario de búsqueda
    echo "<form method='post' action=''>
            <label for='cod_establecimiento'>Cod Establecimiento:</label>
            <input type='text' id='cod_establecimiento' name='cod_establecimiento'>
            <label for='comuna_sucursal'>Comuna:</label>
            <input type='text' id='comuna_sucursal' name='comuna_sucursal'>
            <label for='calle_sucursal'>Calle:</label>
            <input type='text' id='calle_sucursal' name='calle_sucursal'>
            <label for='nombre_sucursal'>Nombre:</label>
            <input type='text' id='nombre_sucursal' name='nombre_sucursal'>
            <label for='tipo_sucursal'>Tipo:</label>
            <input type='text' id='tipo_sucursal' name='tipo_sucursal'>
            <button type='submit'>Buscar</button>
          </form>";

    echo "<table border='1'>
            <thead>
                <tr>
                    <th>Cod Establecimiento</th>
                    <th>Telefono</th>
                    <th>Numero Estableciimiento</th>
                    <th>Comuna</th>
                    <th>Calle</th>
                    <th>Ciudad</th>
                    <th>Nombre</th>
                    <th>Cant Empleados</th>
                    <th>Tipo Sucursal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['cod_establecimiento']}</td>
                <td>{$row['telefono']}</td>
                <td>{$row['numero_estableciimiento']}</td>
                <td>{$row['comuna_establecimiento']}</td>
                <td>{$row['calle_establecimiento']}</td>
                <td>{$row['ciudad_establecimiento']}</td>
                <td>{$row['nombre_establecimiento']}</td>
                <td>{$row['cant_empleados']}</td>
                <td>{$row['tipo_sucursal']}</td>
                <td class='actions'>
                    <a href='ver_sucursal.php?cod_establecimiento={$row['cod_establecimiento']}' title='Ver'><i class='fas fa-eye'></i></a>
                    <a href='../actualizar establecimiento/update_sucursal_form.php?cod_establecimiento={$row['cod_establecimiento']}' title='Actualizar'><i class='fas fa-edit'></i></a>
                    <a href='../eliminar establecimiento/delete_sucursal.php?cod_establecimiento={$row['cod_establecimiento']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta sucursal?\");'><i class='fas fa-trash'></i></a>
                </td>
            </tr>";
    }

    echo "  </tbody>
          </table>";
} else {
    echo "Error al obtener los datos de sucursales: " . pg_last_error($conn);
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
</body>
</html>
