<?php
header('Content-Type: application/json');

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
function fetchProducts() {
    $connection = getDBConnection();
    $query = 'SELECT cod_producto, nombre_producto FROM producto WHERE estado_producto = true';
    $result = pg_query($connection, $query);
    if (!$result) {
        echo json_encode(['error' => 'Error en la consulta']);
        exit;
    }
    $products = [];
    while ($row = pg_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

// Output the data as JSON
$products = fetchProducts();
echo json_encode($products);

// Close the connection
pg_close(getDBConnection());
?>
