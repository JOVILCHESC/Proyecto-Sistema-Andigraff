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
    <title>Registrar Cotización</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/crear_cotizacion.css">
</head>
<body>
    <h2>Registrar Cotización</h2>
    <form action="registro_cotizacion.php" method="POST">
        <div class="form-group">
            <label for="cliente">Cliente:</label>
            <select id="cliente" name="rut">
                <option value="">Seleccione un cliente</option>
                <!-- Las opciones de clientes se cargarán aquí con JS -->
            </select>
        </div>
        <div class="form-group">
            <label for="tra_rut">RUT del Trabajador:</label>
            <input type="text" id="tra_rut" name="tra_rut" value="<?php echo htmlspecialchars($tra_rut_usuario); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="fecha_cotizacion">Fecha de Cotización:</label>
            <input type="date" id="fecha_cotizacion" name="fecha_cotizacion">
        </div>
        <div class="form-group">
            <label for="monto_total">Monto Total:</label>
            <input type="number" id="monto_total" name="monto_total">
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad">
        </div>
        <div class="form-group">
            <label for="descripcion_cotizacion">Descripción de la Cotización:</label>
            <textarea id="descripcion_cotizacion" name="descripcion_cotizacion"></textarea>
        </div>
        <div class="form-group">
            <label for="estado_cotizacion">Estado de la Cotización:</label>
            <input type="checkbox" id="estado_cotizacion" name="estado_cotizacion">
        </div>
        <div class="form-group">
            <label for="productos">Productos:</label>
            <select id="productos" name="productos[]" multiple>
                <option value="">Seleccione productos</option>
                <!-- Las opciones de productos se cargarán aquí con JS -->
            </select>
        </div>
        <button type="submit">Registrar</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('cargar_clientes.php')
                .then(response => response.json())
                .then(data => {
                    const clienteSelect = document.getElementById('cliente');
                    data.forEach(cliente => {
                        const option = document.createElement('option');
                        option.value = cliente.rut;
                        option.textContent = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno}`;
                        clienteSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar clientes:', error));

            fetch('cargar_productos.php')
                .then(response => response.json())
                .then(data => {
                    const productosSelect = document.getElementById('productos');
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.cod_producto;
                        option.textContent = producto.nombre_producto;
                        productosSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar productos:', error));
        });
    </script>
</body>
</html>
