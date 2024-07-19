<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = $_POST['rut'];
    $direccion_origen = $_POST['direccion_origen'];
    $direccion_destino = $_POST['direccion_destino'];
    $condicion_entrega = isset($_POST['condicion_entrega']) ? (bool) $_POST['condicion_entrega'] : null;
    $estado_despacho = isset($_POST['estado_despacho']) ? (bool) $_POST['estado_despacho'] : null;
    $fecha_emicion_guia_despacho = $_POST['fecha_emicion_guia_despacho'];

    // Conectar a la base de datos
    $conn = new PDO("pgsql:host=your_host;dbname=your_dbname", "your_username", "your_password");

    // Preparar la consulta SQL
    $sql = "INSERT INTO public.guia_despacho (rut, direccion_origen, direccion_destino, condicion_entrega, estado_despacho, fecha_emicion_guia_despacho)
            VALUES (:rut, :direccion_origen, :direccion_destino, :condicion_entrega, :estado_despacho, :fecha_emicion_guia_despacho)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':rut', $rut);
    $stmt->bindParam(':direccion_origen', $direccion_origen);
    $stmt->bindParam(':direccion_destino', $direccion_destino);
    $stmt->bindParam(':condicion_entrega', $condicion_entrega);
    $stmt->bindParam(':estado_despacho', $estado_despacho);
    $stmt->bindParam(':fecha_emicion_guia_despacho', $fecha_emicion_guia_despacho);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "Registro exitoso";
    } else {
        echo "Error al registrar";
    }
}
?>
