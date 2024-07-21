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
    <title>Registro de Orden de Compra</title>
    <link rel="stylesheet" href="../styles/crear_orden_compra.css">
</head>
<body>
    <form id="crear-orden-form" action="crear_orden_compra.php" method="POST">
        <h2>Registro de Orden de Compra</h2>
        
        <div class="form-group">
            <label for="id_proveedor">ID Proveedor</label>
            <select id="id_proveedor" name="id_proveedor" required>
                <option value="">Selecciona un proveedor</option>
                <!-- Las opciones de proveedores se cargarán aquí con JS -->
            </select>
        </div>

        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($rut_usuario); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="tipo_comprobante">Tipo de Comprobante</label>
            <input type="text" id="tipo_comprobante" name="tipo_comprobante" required>
        </div>

        <div class="form-group">
            <label for="costo_total">Costo Total</label>
            <input type="number" step="0.01" id="costo_total" name="costo_total" required>
        </div>

        <div class="form-group">
            <label for="descripcion_orden">Descripción de la Orden</label>
            <input type="text" id="descripcion_orden" name="descripcion_orden" required>
        </div>

        <div class="form-group">
            <label for="cantidad_solicitada">Cantidad Solicitada</label>
            <input type="number" id="cantidad_solicitada" name="cantidad_solicitada" readonly>
        </div>

        <div class="form-group">
            <label for="fecha_requerida">Fecha Requerida</label>
            <input type="date" id="fecha_requerida" name="fecha_requerida" required>
        </div>

        <div class="form-group">
            <label for="fecha_promesa">Fecha Promesa</label>
            <input type="date" id="fecha_promesa" name="fecha_promesa" required>
        </div>

        <div class="form-group">
            <label for="fecha_compra">Fecha de Compra</label>
            <input type="date" id="fecha_compra" name="fecha_compra" required>
        </div>

        <h2>Productos</h2>
        <div id="productosDiv"></div>

        <div class="form-group">
            <label for="producto">Producto</label>
            <select id="producto">
                <option value="">Seleccione un producto</option>
                <!-- Las opciones de productos se cargarán aquí con JS -->
            </select>
        </div>
        
        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad">
        </div>
        
        <button type="button" onclick="agregarProducto()">Agregar Producto</button>
        
        <input type="submit" value="Registrar">
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('productos.php')
                .then(response => response.json())
                .then(data => {
                    const productoSelect = document.getElementById('producto');
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.cod_producto;
                        option.textContent = producto.nombre_producto;
                        productoSelect.appendChild(option);
                    });
                });

            fetch('proveedores.php')
                .then(response => response.json())
                .then(data => {
                    const proveedorSelect = document.getElementById('id_proveedor');
                    data.forEach(proveedor => {
                        const option = document.createElement('option');
                        option.value = proveedor.id_proveedor;
                        option.textContent = proveedor.nombre_proveedor;
                        proveedorSelect.appendChild(option);
                    });
                });
        });

        function agregarProducto() {
            const productoSelect = document.getElementById('producto');
            const cantidadInput = document.getElementById('cantidad');
            const productosDiv = document.getElementById('productosDiv');

            const productoId = productoSelect.value;
            const productoNombre = productoSelect.options[productoSelect.selectedIndex].text;
            const cantidad = cantidadInput.value;

            if (!productoId || !cantidad) {
                alert('Por favor seleccione un producto y la cantidad.');
                return;
            }

            const productoHTML = `
                <div>
                    <input type="hidden" name="productos[]" value="${productoId}">
                    <input type="hidden" name="cantidades[]" value="${cantidad}">
                    <span>${productoNombre} - Cantidad: ${cantidad}</span>
                    <button type="button" onclick="this.parentElement.remove(); actualizarCantidadTotal();">Eliminar</button>
                </div>
            `;
            productosDiv.insertAdjacentHTML('beforeend', productoHTML);
            cantidadInput.value = '';
            actualizarCantidadTotal();
        }

        function actualizarCantidadTotal() {
            const productosDiv = document.getElementById('productosDiv');
            const cantidades = productosDiv.querySelectorAll('input[name="cantidades[]"]');
            let totalCantidad = 0;
            cantidades.forEach(input => {
                totalCantidad += parseInt(input.value);
            });
            document.getElementById('cantidad_solicitada').value = totalCantidad;
        }
    </script>
</body>
</html>





