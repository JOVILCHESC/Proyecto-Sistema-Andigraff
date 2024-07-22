<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

$host = "146.83.165.21";
$port = "5432";
$dbname = "bsilvestre"; // Replace with your actual database name
$user = "bsilvestre"; // Replace with your actual username
$password = "druIvAfaf4"; // Replace with your actual password

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

// Iniciar una transacción
pg_query($conn, "BEGIN");

try {
    // Insertar el suministro en la base de datos
    $queryInsertSuministro = "INSERT INTO suministra (cod_establecimiento, suc_cod_establecimiento, cantidad_suministrada, fecha_suministra) 
                              VALUES ($1, $2, $3, $4)";
    $resultInsertSuministro = pg_query_params($conn, $queryInsertSuministro, [$bodega, $sucursal, $cantidad_suministrada, $fecha_suministra]);

    if (!$resultInsertSuministro) {
        throw new Exception("Error en la inserción del suministro: " . pg_last_error($conn));
    }

    // Insertar los productos suministrados
    $queryInsertProducto = "INSERT INTO suministra_productos (suc_cod_establecimiento, cod_establecimiento, cod_producto, cantidad) VALUES ($1, $2, $3, $4)";
    foreach ($productos as $index => $producto) {
        $cantidad = $cantidades[$index];
        $resultInsertProducto = pg_query_params($conn, $queryInsertProducto, [$sucursal, $bodega, $producto, $cantidad]);

        if (!$resultInsertProducto) {
            throw new Exception("Error en la inserción del producto: " . pg_last_error($conn));
        }
    }

    // Confirmar la transacción
    pg_query($conn, "COMMIT");
    header("Location: exito.php");
    exit();

} catch (Exception $e) {
    // Deshacer la transacción en caso de error
    pg_query($conn, "ROLLBACK");
    echo $e->getMessage();
    exit();
}

// Cerrar la conexión
pg_close($conn);
?>
