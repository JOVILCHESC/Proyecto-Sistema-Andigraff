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
        echo json_encode(['error' => 'Error al conectar a la base de datos']);
        exit;
    }
    return $connect;
}

// Fetch data from the database
function fetchProveedores() {
    $connection = getDBConnection();
    $query = 'SELECT id_proveedor, nombre_proveedor FROM proveedor';
    $result = pg_query($connection, $query);
    if (!$result) {
        echo json_encode(['error' => 'Error en la consulta']);
        exit;
    }
    $proveedores = [];
    while ($row = pg_fetch_assoc($result)) {
        $proveedores[] = $row;
    }
    return $proveedores;
}

// Output the data as JSON
$proveedores = fetchProveedores();
echo json_encode($proveedores);

// Close the connection
pg_close(getDBConnection());
?>
