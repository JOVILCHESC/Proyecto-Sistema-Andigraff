<?php
// Configuración de conexión a la base de datos
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Consulta SQL
$query = "SELECT * FROM public.venta";

// Ejecutar la consulta
$result = pg_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Error en la consulta: " . pg_last_error($conn)]);
    exit();
}

// Obtener los datos
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

// Devolver datos en formato JSON
header('Content-Type: application/json');
echo json_encode(["data" => $data]);

// Cerrar la conexión
pg_close($conn);
?>
