<?php
// Include the database connection
require_once(__DIR__ . '/../../config/config.php');

// Check if the ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID de orden de compra no proporcionado.');
}

$id = intval($_GET['id']); // Sanitize the input

// Function to fetch order data by ID
function fetchOrderById($id) {
    $connection = getDBConnection();
    $query = 'SELECT num_orden_compra, id_proveedor, rut, tipo_comprobante, costo_total, descripcion_orden, cantidad_solicitada, fecha_requerida, estado_compra, fecha_promesa, fecha_compra FROM orden_compra WHERE num_orden_compra = $1';
    $result = pg_query_params($connection, $query, [$id]);

    if (!$result) {
        echo "Error en la consulta.";
        return null;
    }

    return pg_fetch_assoc($result);
}

// Function to fetch the provider name by ID
function fetchProviderNameById($id) {
    $connection = getDBConnection();
    $query = 'SELECT nombre_proveedor FROM proveedor WHERE id_proveedor = $1';
    $result = pg_query_params($connection, $query, [$id]);

    if (!$result) {
        echo "Error en la consulta de proveedor.";
        return null;
    }

    $row = pg_fetch_assoc($result);
    return $row ? $row['nombre_proveedor'] : null;
}

// Fetch order data
$order = fetchOrderById($id);

if (!$order) {
    die('Orden de compra no encontrada.');
}

// Fetch the provider name
$providerName = fetchProviderNameById($order['id_proveedor']);

// Fetch associated products
function fetchOrderProducts($id) {
    $connection = getDBConnection();
    $query = 'SELECT p.cod_producto, p.nombre_producto FROM producto p INNER JOIN tiene3 t ON p.cod_producto = t.cod_producto WHERE t.num_orden_compra = $1';
    $result = pg_query_params($connection, $query, [$id]);

    if (!$result) {
        echo "Error en la consulta de productos.";
        return [];
    }

    $products = [];
    while ($row = pg_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}

// Fetch order products
$orderProducts = fetchOrderProducts($id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Orden de Compra</title>
    <link rel="stylesheet" href="../styles/crear_orden_compra.css">
</head>
<body>
    <form action="actualizar_orden_compra_controlador.php" method="post">
        <h2>Editar Orden de Compra</h2>
        
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['num_orden_compra']); ?>">
        
        <div class="form-group">
            <label for="nombre_proveedor">Proveedor</label>
            <input type="text" id="nombre_proveedor" value="<?php echo htmlspecialchars($providerName); ?>" readonly>
            <input type="hidden" name="id_proveedor" value="<?php echo htmlspecialchars($order['id_proveedor']); ?>">
        </div>

        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($order['rut']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tipo_comprobante">Tipo de Comprobante</label>
            <input type="text" id="tipo_comprobante" name="tipo_comprobante" value="<?php echo htmlspecialchars($order['tipo_comprobante']); ?>" required>
        </div>

        <div class="form-group">
            <label for="costo_total">Costo Total</label>
            <input type="number" step="0.01" id="costo_total" name="costo_total" value="<?php echo htmlspecialchars($order['costo_total']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion_orden">Descripción de la Orden</label>
            <input type="text" id="descripcion_orden" name="descripcion_orden" value="<?php echo htmlspecialchars($order['descripcion_orden']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cantidad_solicitada">Cantidad Solicitada</label>
            <input type="number" id="cantidad_solicitada" name="cantidad_solicitada" value="<?php echo htmlspecialchars($order['cantidad_solicitada']); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="fecha_requerida">Fecha Requerida</label>
            <input type="date" id="fecha_requerida" name="fecha_requerida" value="<?php echo htmlspecialchars($order['fecha_requerida']); ?>" required>
        </div>

        <div class="form-group">
            <label for="fecha_promesa">Fecha Promesa</label>
            <input type="date" id="fecha_promesa" name="fecha_promesa" value="<?php echo htmlspecialchars($order['fecha_promesa']); ?>" required>
        </div>

        <div class="form-group">
            <label for="fecha_compra">Fecha de Compra</label>
            <input type="date" id="fecha_compra" name="fecha_compra" value="<?php echo htmlspecialchars($order['fecha_compra']); ?>" required>
        </div>

        <h2>Productos</h2>
        <div id="productosDiv">
            <?php foreach ($orderProducts as $product): ?>
                <div>
                    <input type="hidden" name="productos[]" value="<?php echo htmlspecialchars($product['cod_producto']); ?>">
                    <span><?php echo htmlspecialchars($product['nombre_producto']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <input type="submit" value="Actualizar">
    </form>
</body>
</html>


