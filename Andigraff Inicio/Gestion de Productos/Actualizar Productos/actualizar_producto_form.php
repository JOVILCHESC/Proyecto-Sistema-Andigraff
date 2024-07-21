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

// Obtener los IDs de proveedores
$query_proveedores = "SELECT id_proveedor FROM public.proveedor WHERE estado_proveedor = true";
$result_proveedores = pg_query($conn, $query_proveedores);

$ids_proveedores = [];
if ($result_proveedores) {
    while ($row = pg_fetch_assoc($result_proveedores)) {
        $ids_proveedores[] = $row['id_proveedor'];
    }
} else {
    echo "Error al obtener los IDs de proveedores: " . pg_last_error($conn);
}

// Obtener el proveedor actual del producto
$query_proveedor_actual = "SELECT id_proveedor FROM public.provee WHERE cod_producto = $1";
$result_proveedor_actual = pg_query_params($conn, $query_proveedor_actual, [$id]);

$proveedor_actual = pg_fetch_result($result_proveedor_actual, 0, 'id_proveedor');

// Obtener los establecimientos de la base de datos
$query_establecimientos = "SELECT cod_establecimiento, nombre_establecimiento FROM public.bodega";
$result_establecimientos = pg_query($conn, $query_establecimientos);

$establecimientos = [];
if ($result_establecimientos) {
    while ($row = pg_fetch_assoc($result_establecimientos)) {
        $establecimientos[] = $row;
    }
} else {
    echo "Error al obtener los establecimientos: " . pg_last_error($conn);
}

// Obtener el establecimiento actual del producto
$query_establecimiento_actual = "SELECT cod_establecimiento FROM public.almacena WHERE cod_producto = $1";
$result_establecimiento_actual = pg_query_params($conn, $query_establecimiento_actual, [$id]);

$establecimiento_actual = pg_fetch_result($result_establecimiento_actual, 0, 'cod_establecimiento');

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
            <label for="id_proveedor">Proveedor</label>
            <select id="id_proveedor" name="id_proveedor" required>
                <option value="">Selecciona un proveedor</option>
                <?php foreach ($ids_proveedores as $id_proveedor): ?>
                    <option value="<?php echo htmlspecialchars($id_proveedor); ?>" <?php echo $id_proveedor == $proveedor_actual ? 'selected' : ''; ?>><?php echo htmlspecialchars($id_proveedor); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
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
            <label for="cod_establecimiento">Establecimiento</label>
            <select id="cod_establecimiento" name="cod_establecimiento" required>
                <option value="">Selecciona un establecimiento</option>
                <?php foreach ($establecimientos as $establecimiento): ?>
                    <option value="<?php echo htmlspecialchars($establecimiento['cod_establecimiento']); ?>" <?php echo $establecimiento['cod_establecimiento'] == $establecimiento_actual ? 'selected' : ''; ?>><?php echo htmlspecialchars($establecimiento['nombre_establecimiento']); ?></option>
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
            <input type="number" id="iva" name="iva" value="19" readonly>
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






