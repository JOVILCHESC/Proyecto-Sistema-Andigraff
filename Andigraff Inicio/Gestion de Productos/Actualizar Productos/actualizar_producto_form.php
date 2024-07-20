<?php
// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Verificar si el ID del producto está presente en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID del producto no proporcionado.');
}

$id = intval($_GET['id']); // Sanitizar el ID del producto

// Función para obtener los datos del producto por ID
function fetchProductById($id) {
    $connection = getDBConnection();
    $query = 'SELECT cod_producto, numero_lote, nombre_producto, precio_unitario, stock, tamano, tipo_producto, peso_unitario, iva, descripcion_producto, categoria, stock_critico FROM producto WHERE cod_producto = $1';
    $result = pg_query_params($connection, $query, [$id]);

    if (!$result) {
        echo "Error en la consulta.";
        return null;
    }

    return pg_fetch_assoc($result);
}

// Obtener los datos del producto
$product = fetchProductById($id);

if (!$product) {
    die('Producto no encontrado.');
}

// Conectar a la base de datos para obtener los números de lote
$conn = getDBConnection();

// Obtener los números de lote de la base de datos
$query_lotes = "SELECT DISTINCT numero_lote FROM public.producto";
$result_lotes = pg_query($conn, $query_lotes);

$numeros_lote = [];
if ($result_lotes) {
    while ($row = pg_fetch_assoc($result_lotes)) {
        $numeros_lote[] = $row['numero_lote'];
    }
} else {
    echo "Error al obtener los números de lote: " . pg_last_error($conn);
}

// Cerrar la conexión
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../styles/crear_producto.css">
</head>
<body>
    <form action="actualizar_producto.php" method="post">
        <h2>Editar Producto</h2>
        
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['cod_producto']); ?>">
        
        <div class="form-group">
            <label for="numero_lote">Número de Lote</label>
            <select id="numero_lote" name="numero_lote" required>
                <option value="">Selecciona un lote</option>
                <?php foreach ($numeros_lote as $numero_lote): ?>
                    <option value="<?php echo htmlspecialchars($numero_lote); ?>" <?php echo $numero_lote == $product['numero_lote'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($numero_lote); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre_producto">Nombre del Producto</label>
            <input type="text" id="nombre_producto" name="nombre_producto" value="<?php echo htmlspecialchars($product['nombre_producto']); ?>" required>
        </div>

        <div class="form-group">
            <label for="precio_unitario">Precio Unitario</label>
            <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" value="<?php echo htmlspecialchars($product['precio_unitario']); ?>" required>
        </div>

        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tamano">Tamaño</label>
            <input type="text" id="tamano" name="tamano" value="<?php echo htmlspecialchars($product['tamano']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tipo_producto">Tipo de Producto</label>
            <input type="text" id="tipo_producto" name="tipo_producto" value="<?php echo htmlspecialchars($product['tipo_producto']); ?>" required>
        </div>

        <div class="form-group">
            <label for="peso_unitario">Peso Unitario</label>
            <input type="text" id="peso_unitario" name="peso_unitario" value="<?php echo htmlspecialchars($product['peso_unitario']); ?>" required>
        </div>

        <div class="form-group">
            <label for="iva">IVA</label>
            <input type="number" step="0.01" id="iva" name="iva" value="<?php echo htmlspecialchars($product['iva']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion_producto">Descripción del Producto</label>
            <input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo htmlspecialchars($product['descripcion_producto']); ?>" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría</label>
            <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($product['categoria']); ?>" required>
        </div>

        <div class="form-group">
            <label for="stock_critico">Stock Crítico</label>
            <input type="number" id="stock_critico" name="stock_critico" value="<?php echo htmlspecialchars($product['stock_critico']); ?>" required>
        </div>

        <input type="submit" value="Actualizar">
    </form>
</body>
</html>
