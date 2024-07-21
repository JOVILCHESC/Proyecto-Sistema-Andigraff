<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que el usuario esté autenticado
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $rut = $_SESSION['rut'];
    $direccion_origen = $_POST['direccion_origen'];
    $direccion_destino = $_POST['direccion_destino'];
    $condicion_entrega = isset($_POST['condicion_entrega']) ? intval($_POST['condicion_entrega']) : null;

    // Ajustar estado_despacho a true por defecto si no se proporciona
    $estado_despacho = isset($_POST['estado_despacho']) ? (bool) $_POST['estado_despacho'] : true;

    $fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];

    // Parámetros de conexión a PostgreSQL
    $host = "146.83.165.21";
    $port = "5432";
    $dbname = "jvilches";
    $user = "jvilches";
    $password = "wEtbEQzH6v44";

    // Crear cadena de conexión
    $connectionString = "host=$host port=$port dbname=$dbname user=$user password=$password";

    // Intentar conectar a la base de datos PostgreSQL
    $conn = pg_connect($connectionString);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
        die('Error al conectar a la base de datos');
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO public.guia_despacho (rut, direccion_origen, direccion_destino, condicion_entrega, estado_despacho, fecha_emicion_guia_despacho)
            VALUES ($1, $2, $3, $4, $5, $6)";

    $params = array($rut, $direccion_origen, $direccion_destino, $condicion_entrega, $estado_despacho, $fecha_emicion_guia_despacho);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
        header("Location: ../Lista Guias de Despacho/lista_guia_despacho.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
