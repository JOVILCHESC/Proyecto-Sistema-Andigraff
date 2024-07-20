<?php
// Configuración de la conexión a la base de datos
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

// Conectar a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo json_encode(["error" => "Error en la conexión a la base de datos"]);
    exit();
}

// Consultar los métodos de pago
$query = "SELECT id_metodo_pago, descripcion_pago FROM metodo_pago";
$result = pg_query($conn, $query);

if ($result) {
    $metodos_pago = [];
    
    while ($row = pg_fetch_assoc($result)) {
        $metodos_pago[] = $row;
    }

    echo json_encode($metodos_pago);
} else {
    echo json_encode(["error" => "Error al consultar los métodos de pago"]);
}

// Cerrar la conexión
pg_close($conn);
?>
