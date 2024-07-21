<?php
header('Content-Type: application/json');

// PostgreSQL connection parameters
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

// Create connection string
$connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Function to connect to the database
function getDBConnection() {
    global $connectionString;
    $connect = pg_connect($connectionString);
    if (!$connect) {
        echo json_encode(['error' => 'Error al conectar a la base de datos']);
        exit;
    }
    return $connect;
}

// Fetch data from the database
function fetchOptions() {
    $connection = getDBConnection();
    $bodegasQuery = 'SELECT cod_establecimiento, nombre_establecimiento FROM bodega';
    $sucursalesQuery = 'SELECT cod_establecimiento, nombre_establecimiento FROM sucursal';
    
    $bodegasResult = pg_query($connection, $bodegasQuery);
    $sucursalesResult = pg_query($connection, $sucursalesQuery);
    
    if (!$bodegasResult || !$sucursalesResult) {
        echo json_encode(['error' => 'Error en la consulta']);
        exit;
    }
    
    $bodegas = pg_fetch_all($bodegasResult);
    $sucursales = pg_fetch_all($sucursalesResult);
    
    return ['bodegas' => $bodegas, 'sucursales' => $sucursales];
}

// Output the data as JSON
$options = fetchOptions();
echo json_encode($options);

// Close the connection
pg_close(getDBConnection());
?>
