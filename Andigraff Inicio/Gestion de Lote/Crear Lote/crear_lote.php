<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $precio_total = $_POST['precio_total'];
    $peso_total = $_POST['peso_total'];
    $cantidad_inicial = $_POST['cantidad_inicial'];
    $cantidad_actual = $_POST['cantidad_actual'];
    $tipo_embalaje = $_POST['tipo_embalaje'];

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL
    $sql = "INSERT INTO lote (precio_total, peso_total, cantidad_inicial, cantidad_actual, tipo_embalaje)
            VALUES ($1, $2, $3, $4, $5)";

    $params = array($precio_total, $peso_total, $cantidad_inicial, $cantidad_actual, $tipo_embalaje);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);
    
    if ($result) {
        header("Location: ../Lista Lote/lista_lote.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
