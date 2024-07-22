<?php
header('Content-Type: application/json');

$host = "146.83.165.21";
$port = "5432";
$dbname = "bsilvestre"; // Replace with your actual database name
$user = "bsilvestre"; // Replace with your actual username
$password = "druIvAfaf4"; // Replace with your actual password

// Create connection string
$connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Function to connect to the database
function getDBConnection() {
    global $connectionString;
    $connect = pg_connect($connectionString);
    if (!$connect) {
        // Output JSON error
        echo json_encode(['error' => 'Error al conectar a la base de datos']);
        exit;
    }
    return $connect;
}

// Fetch data from the database
function fetchBodegas() {
    $connection = getDBConnection();
    $query = 'SELECT cod_establecimiento, nombre_establecimiento FROM bodega'; // Suponiendo que 'bodega' es la tabla y tiene estas columnas
    $result = pg_query($connection, $query);
    if (!$result) {
        // Output JSON error
        echo json_encode(['error' => 'Error en la consulta']);
        exit;
    }
    $bodegas = [];
    while ($row = pg_fetch_assoc($result)) {
        $bodegas[] = $row;
    }
    return $bodegas;
}

// Output the data as JSON
$bodegas = fetchBodegas();
echo json_encode($bodegas);

// Close the connection
pg_close(getDBConnection());
?>
