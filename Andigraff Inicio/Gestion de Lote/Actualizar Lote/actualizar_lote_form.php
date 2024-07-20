<?php
// Include the database connection
require_once(__DIR__ . '/../../config/config.php');

// Check if the ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID de lote no proporcionado.');
}

$id = intval($_GET['id']); // Sanitize the input

// Function to fetch lote data by ID
function fetchLoteById($id) {
    $connection = getDBConnection();
    $query = 'SELECT numero_lote, precio_total, peso_total, cantidad_inicial, cantidad_actual, tipo_embalaje FROM lote WHERE numero_lote = $1';
    $result = pg_query_params($connection, $query, [$id]);

    if (!$result) {
        echo "Error en la consulta.";
        return null;
    }

    return pg_fetch_assoc($result);
}

// Fetch lote data
$lote = fetchLoteById($id);

if (!$lote) {
    die('Lote no encontrado.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Lote</title>
    <link rel="stylesheet" href="../styles/crear_lote.css">
</head>
<body>
    <form action="actualizar_lote.php" method="post">
        <h2>Editar Lote</h2>
        
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($lote['numero_lote']); ?>">
        
        <div class="form-group">
            <label for="precio_total">Precio Total</label>
            <input type="number" step="0.01" id="precio_total" name="precio_total" value="<?php echo htmlspecialchars($lote['precio_total']); ?>" required>
        </div>

        <div class="form-group">
            <label for="peso_total">Peso Total</label>
            <input type="number" step="0.01" id="peso_total" name="peso_total" value="<?php echo htmlspecialchars($lote['peso_total']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cantidad_inicial">Cantidad Inicial</label>
            <input type="number" id="cantidad_inicial" name="cantidad_inicial" value="<?php echo htmlspecialchars($lote['cantidad_inicial']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cantidad_actual">Cantidad Actual</label>
            <input type="number" id="cantidad_actual" name="cantidad_actual" value="<?php echo htmlspecialchars($lote['cantidad_actual']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tipo_embalaje">Tipo de Embalaje</label>
            <input type="text" id="tipo_embalaje" name="tipo_embalaje" value="<?php echo htmlspecialchars($lote['tipo_embalaje']); ?>" required>
        </div>
        
        <input type="submit" value="Actualizar">
    </form>
</body>
</html>
