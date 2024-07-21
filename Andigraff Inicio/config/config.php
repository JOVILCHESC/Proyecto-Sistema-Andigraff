<?php
// PostgreSQL connection parameters
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches"; // Replace with your actual database name
$user = "jvilches"; // Replace with your actual username
$password = "wEtbEQzH6v44"; // Replace with your actual password

// Create connection string
$connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Function to connect to the database
function getDBConnection() {
    global $connectionString;
    $connect = pg_connect($connectionString);

    if (!$connect) {
        die('Error al conectar a la base de datos');
    }

    return $connect;
}
?>
