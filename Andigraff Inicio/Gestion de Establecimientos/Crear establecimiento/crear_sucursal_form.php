<!DOCTYPE html>
<html>
<head>
    <title>Registro de Sucursal</title>
    <link rel="stylesheet" href="../styles/crear_establecimiento.css">
</head>
<body>
    <h1>Registro de Sucursal</h1>
    <form action="registrar_sucursal.php" method="post">
        <input type="hidden" name="cod_establecimiento" value="<?php echo $_GET['cod_establecimiento']; ?>">

        <label for="tipo_sucursal">Tipo de Sucursal:</label>
        <input type="text" id="tipo_sucursal" name="tipo_sucursal" required><br>

        <input type="submit" value="Registrar">
    </form>
</body>
</html>
