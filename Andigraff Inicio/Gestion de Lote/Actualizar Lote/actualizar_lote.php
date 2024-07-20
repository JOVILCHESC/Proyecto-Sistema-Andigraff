<?php
// Iniciar sesión
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die('Datos del formulario no válidos.');
}

// Obtener datos del formulario
$id = intval($_POST['id']);
$precio_total = $_POST['precio_total'];
$peso_total = $_POST['peso_total'];
$cantidad_inicial = $_POST['cantidad_inicial'];
$cantidad_actual = $_POST['cantidad_actual'];
$tipo_embalaje = $_POST['tipo_embalaje'];

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Preparar la consulta de actualización
$sql = 'UPDATE lote SET precio_total = $1, peso_total = $2, cantidad_inicial = $3, cantidad_actual = $4, tipo_embalaje = $5 WHERE numero_lote = $6';
$params = array($precio_total, $peso_total, $cantidad_inicial, $cantidad_actual, $tipo_embalaje, $id);

// Ejecutar la consulta
$result = pg_query_params($conn, $sql, $params);

if ($result) {
    header('Location: ../Lista Lote/lista_lote.php'); // Redirigir a la lista de lotes
    exit();
} else {
    echo 'Error al actualizar el lote: ' . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>
