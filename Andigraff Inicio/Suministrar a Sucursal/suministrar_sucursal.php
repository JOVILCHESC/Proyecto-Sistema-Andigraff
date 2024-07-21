<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Conectar a PostgreSQL
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Recoger los datos del formulario
$bodega = $_POST['cod_establecimiento'];
$sucursal = $_POST['suc_cod_establecimiento'];
$cantidades = $_POST['cantidades'];
$productos = $_POST['productos'];
$cantidad_suministrada = $_POST['cantidad_suministrada'];
$fecha_suministra = $_POST['fecha_suministra'];

// Insertar el suministro en la base de datos
$queryInsertSuministro = "INSERT INTO suministra (cod_establecimiento, suc_cod_establecimiento, cantidad_suministrada, fecha_suministra) VALUES ($1, $2, $3, $4) RETURNING suc_cod_establecimiento, cod_establecimiento";
$resultInsertSuministro = pg_query_params($conn, $queryInsertSuministro, [$bodega, $sucursal, $cantidad_suministrada, $fecha_suministra]);

if (!$resultInsertSuministro) {
    echo "Error en la inserción del suministro: " . pg_last_error($conn);
    exit();
}

// Obtener los valores insertados para verificar
$result = pg_fetch_assoc($resultInsertSuministro);
$suc_cod_establecimiento = $result['suc_cod_establecimiento'];
$cod_establecimiento = $result['cod_establecimiento'];

// Insertar los productos suministrados
$queryInsertProducto = "INSERT INTO suministra (suc_cod_establecimiento, cod_establecimiento, cod_producto, cantidad) VALUES ($1, $2, $3, $4)";
foreach ($productos as $index => $producto) {
    $cantidad = $cantidades[$index];
    $resultInsertProducto = pg_query_params($conn, $queryInsertProducto, [$suc_cod_establecimiento, $cod_establecimiento, $producto, $cantidad]);

    if (!$resultInsertProducto) {
        echo "Error en la inserción del producto: " . pg_last_error($conn);
        exit();
    }
}

// Cerrar la conexión
pg_close($conn);

// Redirigir a una página de éxito o mostrar un mensaje
header("Location: exito.php");
exit();
?>
