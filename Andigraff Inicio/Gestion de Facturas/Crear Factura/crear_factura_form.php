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
    <title>Registro de Factura</title>
    <link rel="stylesheet" href="../styles/crear_factura.css">
</head>
<body>
    <form action="crear_factura.php" method="post">
        <h2>Registro de Factura</h2>
        
        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($rut_usuario); ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="lugar_emision">Lugar de Emisión</label>
            <input type="text" id="lugar_emision" name="lugar_emision" required>
        </div>
        
        <div class="form-group">
            <label for="fecha_emision">Fecha de Emisión</label>
            <input type="date" id="fecha_emision" name="fecha_emision" required>
        </div>
        
        <div class="form-group">
            <label for="descripcion_operacion">Descripción de la Operación</label>
            <input type="text" id="descripcion_operacion" name="descripcion_operacion" required>
        </div>
        
        <div class="form-group">
            <label for="direccion_entrega">Dirección de Entrega</label>
            <input type="text" id="direccion_entrega" name="direccion_entrega" required>
        </div>
        
        <input type="submit" value="Registrar">
    </form>
</body>
</html>
