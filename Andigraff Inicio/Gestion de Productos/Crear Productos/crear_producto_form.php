<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$rut_usuario = $_SESSION['rut'];

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Verificar conexión
if (!$conn) {
    die("Error en la conexión: " . pg_last_error());
}

// Obtener los números de lote de la base de datos
$query = "SELECT DISTINCT numero_lote FROM public.lote";
$result = pg_query($conn, $query);

$numeros_lote = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $numeros_lote[] = $row['numero_lote'];
    }
} else {
    echo "Error al obtener los números de lote: " . pg_last_error($conn);
}

// Obtener los proveedores de la base de datos
$query_proveedores = "SELECT id_proveedor, nombre_proveedor FROM public.proveedor WHERE estado_proveedor = true";
$result_proveedores = pg_query($conn, $query_proveedores);

$proveedores = [];
if ($result_proveedores) {
    while ($row = pg_fetch_assoc($result_proveedores)) {
        $proveedores[] = $row;
    }
} else {
    echo "Error al obtener los proveedores: " . pg_last_error($conn);
}

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

// Cerrar la conexión
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Producto</title>
    <link rel="stylesheet" href="../styles/crear_producto.css">
</head>
<body>
    <form action="crear_producto.php" method="post">
        <h2>Registro de Producto</h2>
        
        <div class="form-group">
            <label for="numero_lote">Número de Lote</label>
            <select id="numero_lote" name="numero_lote" required>
                <option value="">Selecciona un lote</option>
                <?php foreach ($numeros_lote as $numero_lote): ?>
                    <option value="<?php echo htmlspecialchars($numero_lote); ?>"><?php echo htmlspecialchars($numero_lote); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_proveedor">Proveedor</label>
            <select id="id_proveedor" name="id_proveedor" required>
                <option value="">Selecciona un proveedor</option>
                <?php foreach ($proveedores as $proveedor): ?>
                    <option value="<?php echo htmlspecialchars($proveedor['id_proveedor']); ?>"><?php echo htmlspecialchars($proveedor['nombre_proveedor']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="cod_establecimiento">Establecimiento</label>
            <select id="cod_establecimiento" name="cod_establecimiento" required>
                <option value="">Selecciona un establecimiento</option>
                <?php foreach ($establecimientos as $establecimiento): ?>
                    <option value="<?php echo htmlspecialchars($establecimiento['cod_establecimiento']); ?>"><?php echo htmlspecialchars($establecimiento['nombre_establecimiento']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre_producto">Nombre del Producto</label>
            <input type="text" id="nombre_producto" name="nombre_producto" required>
        </div>

        <div class="form-group">
            <label for="precio_unitario">Precio Unitario</label>
            <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" required>
        </div>

        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" required>
        </div>

        <div class="form-group">
            <label for="tamano">Tamaño</label>
            <input type="text" id="tamano" name="tamano" required>
        </div>

        <div class="form-group">
            <label for="tipo_producto">Tipo de Producto</label>
            <select id="tipo_producto" name="tipo_producto" required>
                <option value="">Selecciona un tipo</option>
                <option value="electrodomestico">Electrodoméstico</option>
                <option value="control">Control</option>
                <option value="accesorio">Accesorio</option>
                <option value="hardware">Hardware</option>
                <option value="software">Software</option>
                <option value="cable">Cable</option>
                <option value="bateria">Batería</option>
                <option value="otro">Otro</option>
            </select>
        </div>

        <div class="form-group">
            <label for="peso_unitario">Peso Unitario</label>
            <input type="text" id="peso_unitario" name="peso_unitario" required>
        </div>

        <div class="form-group">
            <label for="iva">IVA</label>
            <input type="number" id="iva" name="iva" value="0.19" step="0.01" readonly>
        </div>

        <div class="form-group">
            <label for="descripcion_producto">Descripción del Producto</label>
            <input type="text" id="descripcion_producto" name="descripcion_producto" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <option value="televisores">Televisores</option>
                <option value="computadoras">Computadoras</option>
                <option value="smartphones">Smartphones</option>
                <option value="tablets">Tablets</option>
                <option value="accesorios">Accesorios</option>
                <option value="audio">Audio</option>
                <option value="videojuegos">Videojuegos</option>
                <option value="otros">Otros</option>
            </select>
        </div>

        <div class="form-group">
            <label for="stock_critico">Stock Crítico</label>
            <input type="number" id="stock_critico" name="stock_critico" required>
        </div>

        <!-- Ocultar el campo "Estado del Producto" -->
        <input type="hidden" id="estado_producto" name="estado_producto" value="true">

        <input type="submit" value="Registrar">
    </form>
</body>
</html>







