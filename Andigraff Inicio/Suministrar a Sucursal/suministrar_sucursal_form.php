<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Suministro</title>
    <link rel="stylesheet" href="../styles/crear_suministro.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para cargar las opciones de bodegas y sucursales
            function cargarOpciones() {
                fetch('cargar_opciones.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        const bodegaSelect = document.getElementById('bodega');
                        const sucursalSelect = document.getElementById('sucursal');

                        data.bodegas.forEach(bodega => {
                            const option = document.createElement('option');
                            option.value = bodega.cod_establecimiento;
                            option.textContent = bodega.nombre_establecimiento;
                            bodegaSelect.appendChild(option);
                        });

                        data.sucursales.forEach(sucursal => {
                            const option = document.createElement('option');
                            option.value = sucursal.cod_establecimiento;
                            option.textContent = sucursal.nombre_establecimiento;
                            sucursalSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las opciones:', error);
                    });
            }

            // Llamar a la función para cargar las opciones al cargar la página
            cargarOpciones();

            // Función para cargar productos mediante AJAX
            function cargarProductos() {
                return fetch('./cargar_productos.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error de red');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return null;
                        }

                        const selectProducto = document.createElement('select');
                        selectProducto.setAttribute('name', 'productos[]');
                        selectProducto.required = true;

                        const optionDefault = document.createElement('option');
                        optionDefault.value = '';
                        optionDefault.textContent = 'Seleccione un producto';
                        selectProducto.appendChild(optionDefault);

                        data.forEach(producto => {
                            const option = document.createElement('option');
                            option.value = producto.cod_producto;
                            option.textContent = producto.nombre_producto;
                            selectProducto.appendChild(option);
                        });

                        return selectProducto;
                    })
                    .catch(error => {
                        console.error('Error al cargar productos:', error);
                        return null;
                    });
            }

            function agregarProducto() {
                const productoDiv = document.createElement('div');
                productoDiv.className = 'producto';

                const labelProducto = document.createElement('label');
                labelProducto.textContent = 'Producto:';
                productoDiv.appendChild(labelProducto);

                cargarProductos().then(selectProducto => {
                    if (selectProducto) {
                        productoDiv.appendChild(selectProducto);
                    }
                });

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

                const botonEliminar = document.createElement('button');
                botonEliminar.type = 'button';
                botonEliminar.textContent = 'Eliminar';
                botonEliminar.addEventListener('click', () => {
                    productoDiv.remove();
                    calcularCantidadTotal();
                });
                productoDiv.appendChild(botonEliminar);

                document.getElementById('productos').appendChild(productoDiv);
            }

            function calcularCantidadTotal() {
                let totalCantidad = 0;
                const cantidades = document.querySelectorAll('input[name="cantidades[]"]');
                cantidades.forEach(input => {
                    totalCantidad += parseInt(input.value) || 0;
                });
                document.getElementById('cantidad_suministrada').value = totalCantidad;
            }

            document.getElementById('agregarProductoBtn').addEventListener('click', agregarProducto);
            document.querySelector('form').addEventListener('submit', calcularCantidadTotal);
        });
    </script>
</head>
<body>
    <h2>Registrar Suministro a Sucursal</h2>
    <form action="suministrar_sucursal.php" method="POST">
        <div class="form-group">
            <label for="bodega">Bodega:</label>
            <select id="bodega" name="cod_establecimiento" required>
                <option value="">Seleccione una bodega</option>
            </select>
        </div>
        <div class="form-group">
            <label for="sucursal">Sucursal:</label>
            <select id="sucursal" name="suc_cod_establecimiento" required>
                <option value="">Seleccione una sucursal</option>
            </select>
        </div>
        <div id="productos">
            <div class="producto"></div>
        </div>
        <button type="button" id="agregarProductoBtn">Agregar Producto</button>
        <div class="form-group">
            <label for="cantidad_suministrada">Cantidad Suministrada:</label>
            <input type="number" id="cantidad_suministrada" name="cantidad_suministrada" readonly required>
        </div>
        <div class="form-group">
            <label for="fecha_suministra">Fecha de Suministro:</label>
            <input type="date" id="fecha_suministra" name="fecha_suministra" required>
        </div>
        <button type="submit">Registrar Suministro</button>
    </form>
</body>
</html>
