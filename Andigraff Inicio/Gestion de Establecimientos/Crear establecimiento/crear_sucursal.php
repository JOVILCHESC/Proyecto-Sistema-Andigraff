<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conectar a la base de datos
    $host = "146.83.165.21";
    $port = "5432";
    $dbname = "jvilches";
    $user = "jvilches";
    $password = "wEtbEQzH6v44";

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    if (!$conn) {
        die("Error en la conexión a la base de datos");
    }

    // Recoger datos del formulario
    $cod_establecimiento = $_POST['cod_establecimiento'];
    $tipo_sucursal = $_POST['tipo_sucursal'];

    // Insertar en la tabla sucursal
    $query = "INSERT INTO sucursal (cod_establecimiento, tipo_sucursal) 
              VALUES ($1, $2)";
    $result = pg_query_params($conn, $query, array($cod_establecimiento, $tipo_sucursal));

    if ($result) {
        echo "Sucursal registrada exitosamente.";
    } else {
        echo "Error al registrar la sucursal: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
