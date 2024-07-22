<?php
session_start();

// Verifica si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si el usuario está autenticado
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    // Recoge y sanitiza los datos del formulario
    $telefono = (int) $_POST['telefono'];
    $numero_estableciimiento = (int) $_POST['numero_estableciimiento'];
    $comuna_establecimiento = substr($_POST['comuna_establecimiento'], 0, 255);
    $calle_establecimiento = substr($_POST['calle_establecimiento'], 0, 255);
    $ciudad_establecimiento = substr($_POST['ciudad_establecimiento'], 0, 255);
    $nombre_establecimiento = substr($_POST['nombre_establecimiento'], 0, 255);
    $cant_empleados = (int) $_POST['cant_empleados'];
    $tipo = substr($_POST['tipo'], 0, 255);

    $tipo_sucursal = isset($_POST['tipo_sucursal']) ? substr($_POST['tipo_sucursal'], 0, 255) : null;
    $capacidad = isset($_POST['capacidad']) ? (int) $_POST['capacidad'] : null;
    $tipo_almacenamiento = isset($_POST['tipo_almacenamiento']) ? substr($_POST['tipo_almacenamiento'], 0, 255) : null;
    $estado_bodega = isset($_POST['estado_bodega']) ? (bool) $_POST['estado_bodega'] : false;

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Obtener el último valor de cod_establecimiento
    $sql = "SELECT MAX(cod_establecimiento) as max_cod FROM establecimiento";
    $result = pg_query($conn, $sql);

    if ($result) {
        $row = pg_fetch_assoc($result);
        $cod_establecimiento = $row['max_cod'] + 1;
    } else {
        $cod_establecimiento = 1; // Si no hay registros, comenzamos en 1
    }

    // Preparar la consulta SQL para insertar un nuevo establecimiento
    $sql = "INSERT INTO establecimiento (cod_establecimiento, telefono, numero_estableciimiento, comuna_establecimiento, calle_establecimiento, ciudad_establecimiento, nombre_establecimiento, cant_empleados)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";

    // Crear un arreglo con los parámetros para la consulta
    $params = array($cod_establecimiento, $telefono, $numero_estableciimiento, $comuna_establecimiento, $calle_establecimiento, $ciudad_establecimiento, $nombre_establecimiento, $cant_empleados);

    // Ejecutar la consulta con los parámetros
    $result = pg_query_params($conn, $sql, $params);    

    if ($result) { // Si la consulta fue exitosa
        if ($tipo === 'sucursal') {
            $sql_sucursal = "INSERT INTO sucursal (cod_establecimiento, tipo_sucursal, telefono, numero_estableciimiento, comuna_establecimiento, calle_establecimiento, ciudad_establecimiento, nombre_establecimiento, cant_empleados) VALUES ($1, $2,$3,$4,$5,$6,$7,$8,$9)";
            $params_sucursal = array($cod_establecimiento, $tipo_sucursal, $telefono, $numero_estableciimiento, $comuna_establecimiento, $calle_establecimiento, $ciudad_establecimiento, $nombre_establecimiento, $cant_empleados);
            $result_sucursal = pg_query_params($conn, $sql_sucursal, $params_sucursal);
            if (!$result_sucursal) {
                echo "Error al registrar sucursal: " . pg_last_error($conn);
            }
        } elseif ($tipo === 'bodega') {
            $sql_bodega = "INSERT INTO bodega (cod_establecimiento, capacidad, tipo_almacenamiento, estado_bodega, telefono, numero_estableciimiento, comuna_establecimiento, calle_establecimiento, ciudad_establecimiento, nombre_establecimiento, cant_empleados) VALUES ($1, $2, $3, $4,$5,$6,$7,$8,$9,$10,$11)";
            $params_bodega = array($cod_establecimiento, $capacidad, $tipo_almacenamiento, $estado_bodega, $telefono, $numero_estableciimiento, $comuna_establecimiento, $calle_establecimiento, $ciudad_establecimiento, $nombre_establecimiento, $cant_empleados);
            $result_bodega = pg_query_params($conn, $sql_bodega, $params_bodega);
            if (!$result_bodega) {
                echo "Error al registrar bodega: " . pg_last_error($conn);
            }
        }

        header("Location: ../Lista Establecimientos/lista_establecimiento.php"); // Redirige a la lista de establecimientos
        exit();
    } else { // Si hubo un error en la consulta
        echo "Error al registrar establecimiento: " . pg_last_error($conn);
    }

    // Cerrar la conexión a la base de datos
    pg_close($conn);
}
?>
