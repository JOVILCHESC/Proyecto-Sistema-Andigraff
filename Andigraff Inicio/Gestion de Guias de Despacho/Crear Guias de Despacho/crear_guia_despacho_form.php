<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$rut_usuario = $_SESSION['rut'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Guía de Despacho</title>
    <link rel="stylesheet" href="../styles/crear_guia_despacho.css">
</head>
<body>
    <form action="crear_guia_despacho.php" method="post">
        <h2>Registro de Guía de Despacho</h2>
        
        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($rut_usuario); ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="direccion_origen">Dirección de Origen</label>
            <input type="text" id="direccion_origen" name="direccion_origen">
        </div>
        
        <div class="form-group">
            <label for="direccion_destino">Dirección de Destino</label>
            <input type="text" id="direccion_destino" name="direccion_destino">
        </div>
        
        <div class="form-group">
            <label for="condicion_entrega">Condición de Entrega</label>
            <select id="condicion_entrega" name="condicion_entrega">
                <option value="1">Entregado</option>
                <option value="0">No Entregado</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="estado_despacho">Estado de Despacho</label>
            <select id="estado_despacho" name="estado_despacho">
                <option value="1">Despachado</option>
                <option value="0">No Despachado</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="fecha_emicion_guia_despacho">Fecha de Emisión</label>
            <input type="date" id="fecha_emicion_guia_despacho" name="fecha_emicion_guia_despacho">
        </div>
        
        <input type="submit" value="Registrar">
    </form>
</body>
</html>
