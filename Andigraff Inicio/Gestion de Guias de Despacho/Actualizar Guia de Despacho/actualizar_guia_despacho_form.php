<?php
// Include the database connection
require_once(__DIR__ . '/../../config/config.php');

// Check if the num_guia_despacho is provided
if (!isset($_GET['num_guia_despacho']) || empty($_GET['num_guia_despacho'])) {
    die('ID de guía de despacho no proporcionado.');
}

$num_guia_despacho = intval($_GET['num_guia_despacho']); // Sanitize the input

// Function to fetch guide data by num_guia_despacho
function fetchGuideById($num_guia_despacho) {
    $connection = getDBConnection();
    $query = 'SELECT num_guia_despacho, direccion_origen, direccion_destino, condicion_entrega, estado_despacho, fecha_emicion_guia_despacho FROM guia_despacho WHERE num_guia_despacho = $1';
    $result = pg_query_params($connection, $query, [$num_guia_despacho]);

    if (!$result) {
        echo "Error en la consulta.";
        return null;
    }

    return pg_fetch_assoc($result);
}

// Fetch guide data
$guide = fetchGuideById($num_guia_despacho);

if (!$guide) {
    die('Guía de despacho no encontrada.');
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Guía de Despacho</title>
    <link rel="stylesheet" href="../styles/crear_guia_despacho.css">
</head>
<body>
<form action="actualizar_guia_despacho_controlador.php" method="post">
    <input type="hidden" name="num_guia_despacho" value="<?php echo htmlspecialchars($guide['num_guia_despacho']); ?>">
    <div class="form-group">
        <label for="direccion_origen">Dirección de Origen</label>
        <input type="text" id="direccion_origen" name="direccion_origen" value="<?php echo htmlspecialchars($guide['direccion_origen']); ?>">
    </div>
    <div class="form-group">
        <label for="direccion_destino">Dirección de Destino</label>
        <input type="text" id="direccion_destino" name="direccion_destino" value="<?php echo htmlspecialchars($guide['direccion_destino']); ?>">
    </div>
    <div class="form-group">
        <label for="condicion_entrega">Condición de Entrega</label>
        <select id="condicion_entrega" name="condicion_entrega">
            <option value="1" <?php echo $guide['condicion_entrega'] ? 'selected' : ''; ?>>Entregado</option>
            <option value="0" <?php echo !$guide['condicion_entrega'] ? 'selected' : ''; ?>>No Entregado</option>
        </select>
    </div>
    <div class="form-group" style="display:none;">
        <label for="estado_despacho">Estado de Despacho</label>
        <select id="estado_despacho" name="estado_despacho">
            <option value="1">Despachado</option>
            <option value="0">No Despachado</option>
        </select>
    </div>
    <div class="form-group">
        <label for="fecha_emicion_guia_despacho">Fecha de Emisión</label>
        <input type="date" id="fecha_emicion_guia_despacho" name="fecha_emicion_guia_despacho" value="<?php echo htmlspecialchars($guide['fecha_emicion_guia_despacho']); ?>">
    </div>
    <input type="submit" value="Actualizar">
</form>

</body>
</html>
