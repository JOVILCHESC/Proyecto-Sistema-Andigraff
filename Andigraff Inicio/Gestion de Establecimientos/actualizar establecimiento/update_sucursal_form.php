<?php
// Incluir la conexión a la base de datos
require_once(__DIR__ . '/../../config/config.php');

// Verificar si el cod_establecimiento se ha proporcionado
if (!isset($_GET['cod_establecimiento'])) {
    die('Cod Establecimiento no proporcionado.');
}

$cod_establecimiento = pg_escape_string(getDBConnection(), $_GET['cod_establecimiento']);

// Conectar a la base de datos
$connection = getDBConnection();

// Consultar los datos de la sucursal
$query = "SELECT * FROM sucursal WHERE cod_establecimiento = $1";
$result = pg_query_params($connection, $query, [$cod_establecimiento]);

if (!$result || pg_num_rows($result) === 0) {
    die('Sucursal no encontrada.');
}

$sucursal = pg_fetch_assoc($result);
pg_close($connection);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Sucursal</title>
    <link rel="stylesheet" href="../styles/update_sucursal.css">
</head>
<body>
    <h1>Actualizar Sucursal</h1>
    <form action="update_sucursal.php" method="post">
        <input type="hidden" name="cod_establecimiento" value="<?php echo htmlspecialchars($sucursal['cod_establecimiento']); ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($sucursal['nombre_establecimiento']); ?>" required>

        <label for="telefono">Telefono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($sucursal['telefono']); ?>" required>

        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($sucursal['numero_estableciimiento']); ?>" required>

        <label for="comuna">Comuna:</label>
        <input type="text" id="comuna" name="comuna" value="<?php echo htmlspecialchars($sucursal['comuna_establecimiento']); ?>" required>

        <label for="calle">Calle:</label>
        <input type="text" id="calle" name="calle" value="<?php echo htmlspecialchars($sucursal['calle_establecimiento']); ?>" required>

        <label for="ciudad">Ciudad:</label>
        <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($sucursal['ciudad_establecimiento']); ?>" required>

        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($sucursal['tipo_sucursal']); ?>" required>

        <label for="cant_empleados">Cantidad de Empleados:</label>
        <input type="number" id="cant_empleados" name="cant_empleados" value="<?php echo htmlspecialchars($sucursal['cant_empleados']); ?>" required>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
