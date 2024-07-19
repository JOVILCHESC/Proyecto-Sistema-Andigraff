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
    <title>Registro de Orden de Compra</title>
    <link rel="stylesheet" href="../styles/crear_orden_compra.css">
</head>
<body>
    <form action="crear_orden_compra.php" method="post">
        <h2>Registro de Orden de Compra</h2>
        
        <div class="form-group">
            <label for="num_orden_compra">Número de Orden de Compra</label>
            <input type="text" id="num_orden_compra" name="num_orden_compra" required>
        </div>
        
        <div class="form-group">
            <label for="id_proveedor">ID Proveedor</label>
            <input type="text" id="id_proveedor" name="id_proveedor" required>
        </div>

        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($rut_usuario); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="tipo_comprobante">Tipo de Comprobante</label>
            <input type="text" id="tipo_comprobante" name="tipo_comprobante" required>
        </div>

        <div class="form-group">
            <label for="costo_total">Costo Total</label>
            <input type="number" step="0.01" id="costo_total" name="costo_total" required>
        </div>

        <div class="form-group">
            <label for="descripcion_orden">Descripción de la Orden</label>
            <input type="text" id="descripcion_orden" name="descripcion_orden" required>
        </div>

        <div class="form-group">
            <label for="cantidad_solicitada">Cantidad Solicitada</label>
            <input type="number" id="cantidad_solicitada" name="cantidad_solicitada" required>
        </div>

        <div class="form-group">
            <label for="fecha_requerida">Fecha Requerida</label>
            <input type="date" id="fecha_requerida" name="fecha_requerida" required>
        </div>

        <div class="form-group">
            <label for="estado_compra">Estado de Compra</label>
            <select id="estado_compra" name="estado_compra">
                <option value="true">Comprado</option>
                <option value="false">No Comprado</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_promesa">Fecha Promesa</label>
            <input type="date" id="fecha_promesa" name="fecha_promesa" required>
        </div>

        <div class="form-group">
            <label for="fecha_compra">Fecha de Compra</label>
            <input type="date" id="fecha_compra" name="fecha_compra" required>
        </div>
        
        <input type="submit" value="Registrar">
    </form>
</body>
</html>

