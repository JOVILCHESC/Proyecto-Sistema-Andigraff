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
    <title>Registro de Proveedor</title>
    <link rel="stylesheet" href="../styles/crear_proveedor.css">
</head>
<body>
    <form action="crear_proveedor.php" method="post">
        <h2>Registro de Proveedor</h2>
        
        <div class="form-group">
            <label for="contacto_principal">Contacto Principal</label>
            <input type="text" id="contacto_principal" name="contacto_principal" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="email_proveedor">Email</label>
            <input type="email" id="email_proveedor" name="email_proveedor" maxlength="15" required>
        </div>

        <div class="form-group">
            <label for="telefono_proveedor">Teléfono</label>
            <input type="text" id="telefono_proveedor" name="telefono_proveedor" maxlength="15" required>
        </div>

        <div class="form-group">
            <label for="codigo_postal">Código Postal</label>
            <input type="text" id="codigo_postal" name="codigo_postal" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="cod_pais">Código País</label>
            <input type="text" id="cod_pais" name="cod_pais" maxlength="255" required>
        </div>

        <div class="form-group">
            <label for="nombre_proveedor">Nombre del Proveedor</label>
            <input type="text" id="nombre_proveedor" name="nombre_proveedor" maxlength="255" required>
        </div>

        <!-- Ocultar el campo "Estado del Proveedor" -->
        <input type="hidden" id="estado_proveedor" name="estado_proveedor" value="true">

        <input type="submit" value="Registrar">
    </form>
</body>
</html>
