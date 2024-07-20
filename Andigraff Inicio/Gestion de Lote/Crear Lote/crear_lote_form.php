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
    <title>Registro de Lote</title>
    <link rel="stylesheet" href="../styles/crear_lote.css">
</head>
<body>
    <form action="crear_lote.php" method="post">
        <h2>Registro de Lote</h2>
        
        <div class="form-group">
            <label for="precio_total">Precio Total</label>
            <input type="number" step="0.01" id="precio_total" name="precio_total" required>
        </div>

        <div class="form-group">
            <label for="peso_total">Peso Total</label>
            <input type="number" step="0.01" id="peso_total" name="peso_total" required>
        </div>

        <div class="form-group">
            <label for="cantidad_inicial">Cantidad Inicial</label>
            <input type="number" id="cantidad_inicial" name="cantidad_inicial" required>
        </div>

        <div class="form-group">
            <label for="cantidad_actual">Cantidad Actual</label>
            <input type="number" id="cantidad_actual" name="cantidad_actual" required>
        </div>

        <div class="form-group">
            <label for="tipo_embalaje">Tipo de Embalaje</label>
            <input type="text" id="tipo_embalaje" name="tipo_embalaje" required>
        </div>
        
        <input type="submit" value="Registrar">
    </form>
</body>
</html>

