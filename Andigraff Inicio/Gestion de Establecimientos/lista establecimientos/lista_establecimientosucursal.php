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

// Preparar la consulta SQL para obtener los datos de sucursales
$sql = "SELECT * FROM sucursal
        JOIN establecimiento ON sucursal.cod_establecimiento = establecimiento.cod_establecimiento";

$result = pg_query($conn, $sql);

if ($result) {
    echo "<h1>Listado de Sucursales</h1>";
    echo "<table border='1'>
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
            </tr>";

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
            </tr>";
    }

    echo "</table>";
} else {
    echo "Error al obtener los datos de sucursales: " . pg_last_error($conn);
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
