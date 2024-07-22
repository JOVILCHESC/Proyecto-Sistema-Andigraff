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
    <title>Registro de Guía de Despacho</title>
    <link rel="stylesheet" href="../styles/crear_guia_despacho.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('./cargar_bodegas.php')
                .then(response => response.json())
                .then(data => {
                    const selectBodega = document.getElementById('bodega');
                    data.forEach(bodega => {
                        const option = document.createElement('option');
                        option.value = bodega.cod_establecimiento;
                        option.textContent = bodega.nombre_establecimiento;
                        selectBodega.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar bodegas:', error));

            fetch('./cargar_productos.php')
                .then(response => response.json())
                .then(data => {
                    const selectProducto = document.querySelector('select[name="productos[]"]');
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.cod_producto;
                        option.textContent = producto.nombre_producto;
                        selectProducto.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar productos:', error));
        });

        function agregarProducto() {
            const productosDiv = document.getElementById('productos');
            const productoDiv = document.createElement('div');
            productoDiv.className = 'producto';

            const labelProducto = document.createElement('label');
            labelProducto.textContent = 'Producto:';
            productoDiv.appendChild(labelProducto);

            const selectProducto = document.createElement('select');
            selectProducto.setAttribute('name', 'productos[]');
            const optionProducto = document.createElement('option');
            optionProducto.value = '';
            optionProducto.textContent = 'Seleccione un producto';
            selectProducto.appendChild(optionProducto);
            productoDiv.appendChild(selectProducto);

            const labelCantidad = document.createElement('label');
            labelCantidad.textContent = 'Cantidad:';
            productoDiv.appendChild(labelCantidad);

            const inputCantidad = document.createElement('input');
            inputCantidad.setAttribute('type', 'number');
            inputCantidad.setAttribute('name', 'cantidades[]');
            inputCantidad.setAttribute('step', '1');
            inputCantidad.setAttribute('min', '1');
            inputCantidad.required = true;
            productoDiv.appendChild(inputCantidad);

            productosDiv.appendChild(productoDiv);

            // Cargar opciones de productos en el nuevo select
            fetch('./cargar_productos.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.cod_producto;
                        option.textContent = producto.nombre_producto;
                        selectProducto.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar productos:', error));
        }
    </script>
</head>
<body>

<form action="crear_guia_despacho.php" method="post">
    <h2>Registro de Guía de Despacho</h2>
    
    <div class="form-group">
        <label for="rut">RUT</label>
        <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($rut_usuario); ?>" readonly>
    </div>
    
    <div class="form-group">
        <label for="direccion_origen">Dirección de Origen</label>
        <input type="text" id="direccion_origen" name="direccion_origen">
    </div>
    
    <div class="form-group">
        <label for="direccion_destino">Dirección de Destino</label>
        <input type="text" id="direccion_destino" name="direccion_destino">
    </div>
    
    <div class="form-group">
        <label for="condicion_entrega">Condición de Entrega</label>
        <select id="condicion_entrega" name="condicion_entrega">
            <option value="1">Entregado</option>
            <option value="0">No Entregado</option>
        </select>
    </div>

    <div class="form-group">
        <label for="bodega">Bodega</label>
        <select id="bodega" name="bodega">
            <option value="">Selecciona una bodega</option>
        </select>
    </div>

    <div class="form-group">
        <label for="fecha_emicion_guia_despacho">Fecha de Emisión</label>
        <input type="date" id="fecha_emicion_guia_despacho" name="fecha_emicion_guia_despacho">
    </div>

    <h2>Productos</h2>
    <div id="productos">
        <div class="producto">
            <label for="producto">Producto:</label>
            <select name="productos[]">
                <option value="">Seleccione un producto</option>
            </select>
            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidades[]" step="1" min="1" required>
        </div>
    </div>
    <button type="button" onclick="agregarProducto()">Agregar Producto</button>

    <input type="submit" value="Registrar">
</form>

</body>
</html>


