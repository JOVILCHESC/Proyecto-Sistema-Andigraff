<?php
require_once(__DIR__ . '/../../config/config.php');

$conn = getDBConnection();

$query = "SELECT cod_producto, nombre_producto FROM producto WHERE estado_producto = true";
$result = pg_query($conn, $query);

if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . pg_last_error($conn)]);
    exit;
}

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
pg_close($conn);
?>
