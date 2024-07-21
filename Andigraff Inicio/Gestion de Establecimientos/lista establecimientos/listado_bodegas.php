<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de bodegas</title>
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
    if (!empty($_POST['codigo_establecimiento'])) {
        $search_criteria[] = "bodega.cod_establecimiento = '" . pg_escape_string($conn, $_POST['codigo_establecimiento']) . "'";
    }
    if (!empty($_POST['comuna_establecimiento'])) {
        $search_criteria[] = "bodega.comuna_establecimiento ILIKE '%" . pg_escape_string($conn, $_POST['comuna_establecimiento']) . "%'";
    }
    if (!empty($_POST['nombre_establecimiento'])) {
        $search_criteria[] = "bodega.nombre_establecimiento ILIKE '%" . pg_escape_string($conn, $_POST['nombre_establecimiento']) . "%'";
    }
    if (!empty($_POST['estado_bodega'])) {
        $search_criteria[] = "bodega.estado_bodega = '" . pg_escape_string($conn, $_POST['estado_bodega']) . "'";
    }
    if (!empty($_POST['tipo_almacenamiento'])) {
        $search_criteria[] = "bodega.tipo_almacenamiento ILIKE '%" . pg_escape_string($conn, $_POST['tipo_almacenamiento']) . "%'";
    }
}

// Construir la consulta SQL con los criterios de búsqueda si existen
$sql = "SELECT * FROM bodega
        JOIN establecimiento ON bodega.cod_establecimiento = establecimiento.cod_establecimiento";

if (!empty($search_criteria)) {
    $sql .= " WHERE " . implode(' AND ', $search_criteria);
}

$result = pg_query($conn, $sql);

if ($result) {
    echo "<h1>Listado de Bodegas</h1>";

    // Formulario de búsqueda
    echo "<form method='post' action=''>
            <label for='codigo_establecimiento'>Código Establecimiento:</label>
            <input type='text' id='codigo_establecimiento' name='codigo_establecimiento'>
            <label for='comuna_establecimiento'>Comuna:</label>
            <input type='text' id='comuna_establecimiento' name='comuna_establecimiento'>
            <label for='nombre_establecimiento'>Nombre:</label>
            <input type='text' id='nombre_establecimiento' name='nombre_establecimiento'>
            <label for='estado_bodega'>Estado:</label>
            <input type='text' id='estado_bodega' name='estado_bodega'>
            <label for='tipo_almacenamiento'>Tipo Almacenamiento:</label>
            <input type='text' id='tipo_almacenamiento' name='tipo_almacenamiento'>
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
                    <th>Capacidad</th>
                    <th>Tipo Almacenamiento</th>
                    <th>Estado Bodega</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = pg_fetch_assoc($result)) {
        $estado_bodega = $row['estado_bodega'] ? 'Sí' : 'No';
        echo "<tr>
                <td>{$row['cod_establecimiento']}</td>
                <td>{$row['telefono']}</td>
                <td>{$row['numero_estableciimiento']}</td>
                <td>{$row['comuna_establecimiento']}</td>
                <td>{$row['calle_establecimiento']}</td>
                <td>{$row['ciudad_establecimiento']}</td>
                <td>{$row['nombre_establecimiento']}</td>
                <td>{$row['cant_empleados']}</td>
                <td>{$row['capacidad']}</td>
                <td>{$row['tipo_almacenamiento']}</td>
                <td>{$estado_bodega}</td>
                <td class='actions'>
                    <a href='ver_bodega.php?cod_establecimiento={$row['cod_establecimiento']}' title='Ver'><i class='fas fa-eye'></i></a>
                    <a href='../actualizar establecimiento/update_bodega_form.php?cod_establecimiento={$row['cod_establecimiento']}' title='Actualizar'><i class='fas fa-edit'></i></a>
                    <a href='../eliminar establecimiento/delete_bodega.php?cod_establecimiento={$row['cod_establecimiento']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta bodega?\");'><i class='fas fa-trash'></i></a>
                </td>
            </tr>";
    }

    echo "  </tbody>
          </table>";
} else {
    echo "Error al obtener los datos de bodegas: " . pg_last_error($conn);
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
