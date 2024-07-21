<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$tra_rut_usuario = $_SESSION['rut'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Factura</title>
    <link rel="stylesheet" href="../styles/crear_factura.css">
</head>
<body>
    <form id="crear-factura-form" action="crear_factura.php" method="POST">
        <h2>Datos de la Factura</h2>
        
        <label for="lugar_emision">Lugar de Emisión:</label>
        <input type="text" id="lugar_emision" name="lugar_emision" required>
        
        <label for="fecha_emision_factura">Fecha de Emisión:</label>
        <input type="date" id="fecha_emision_factura" name="fecha_emision_factura" required>
        
        <label for="descripcion_operacion_factura">Descripción:</label>
        <input type="text" id="descripcion_operacion_factura" name="descripcion_operacion_factura">
        
        <label for="direccion_entrega_factura">Dirección de Entrega:</label>
        <input type="text" id="direccion_entrega_factura" name="direccion_entrega_factura">

        <h2>Datos del Cliente</h2>
        <label for="cliente">Cliente:</label>
        <select id="cliente" name="cliente">
            <option value="">Seleccione un cliente</option>
            <!-- Las opciones de clientes se cargarán aquí con JS -->
        </select>

        <h2>Datos de la Venta</h2>
        
        <label for="tra_rut">RUT del Trabajador:</label>
        <input type="text" id="tra_rut" name="tra_rut" value="<?php echo htmlspecialchars($tra_rut_usuario); ?>" readonly>
        
        <label for="total_venta">Total de Venta:</label>
        <input type="number" id="total_venta" name="total_venta" step="0.01" required>
        
        <label for="hora_venta">Hora de Venta:</label>
        <input type="time" id="hora_venta" name="hora_venta" required>
        
        <label for="sub_total">Sub Total:</label>
        <input type="number" id="sub_total" name="sub_total" step="0.01" required>
        
        <!-- Elimina el campo estado_venta -->
        
        <label for="iva_venta">IVA Venta:</label>
        <input type="number" id="iva_venta" name="iva_venta" step="0.01" value="0.19" required>

        <h2>Productos</h2>
        <div id="productos">
            <div class="producto">
                <label for="producto">Producto:</label>
                <select name="productos[]">
                    <option value="">Seleccione un producto</option>
                    <!-- Las opciones de productos se cargarán aquí con JS -->
                </select>
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidades[]" step="1" min="1" required>
            </div>
        </div>
        <button type="button" onclick="agregarProducto()">Agregar Producto</button>

        <h2>Métodos de Pago</h2>
        <div id="metodos_pago_contenedor">
            <div class="metodo_pago">
                <label for="metodo_pago">Método de Pago:</label>
                <select name="metodos_pago[]">
                    <option value="">Seleccione un método de pago</option>
                    <!-- Las opciones de métodos de pago se cargarán aquí con JS -->
                </select>
                <label for="porcentaje_pago">Porcentaje:</label>
                <input type="number" name="porcentajes_pago[]" step="0.01" min="0" max="100" required>
            </div>
        </div>
        <button type="button" id="agregar_metodo_pago">Agregar Método de Pago</button>

        <button type="submit">Registrar Factura y Venta</button>
    </form>

    <script src="../js/crear_factura.js"></script>
</body>
</html>
