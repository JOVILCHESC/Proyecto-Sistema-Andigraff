document.addEventListener('DOMContentLoaded', () => {
    const clienteSelect = document.getElementById('cliente');
    
    // Verificar que el elemento clienteSelect existe
    if (!clienteSelect) {
        console.error('No se encontró el elemento #cliente en el DOM.');
        return;
    }

    // Cargar clientes
    fetch('../Crear Factura/cargar_clientes.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.rut;
                option.textContent = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno}`;
                clienteSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading clients:', error));

    // Cargar productos
    function cargarProductos() {
        fetch('../Crear Factura/cargar_productos.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                const selects = document.querySelectorAll('select[name="productos[]"]');
                selects.forEach(select => {
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.cod_producto;
                        option.textContent = producto.nombre_producto;
                        select.appendChild(option);
                    });
                });
            })
            .catch(error => console.error('Error loading products:', error));
    }

    cargarProductos();

    // Función para agregar más productos
    window.agregarProducto = function() {
        const productoDiv = document.createElement('div');
        productoDiv.classList.add('producto');
        productoDiv.innerHTML = `
            <label for="producto">Producto:</label>
            <select name="productos[]">
                <option value="">Seleccione un producto</option>
                <!-- Las opciones de productos se cargarán aquí con JS -->
            </select>
            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidades[]" step="1" min="1" required>
        `;
        document.getElementById('productos').appendChild(productoDiv);
        
        // Cargar los productos en el nuevo select
        const nuevosSelects = productoDiv.querySelectorAll('select[name="productos[]"]');
        fetch('../Crear Factura/cargar_productos.php')
            .then(response => response.json())
            .then(data => {
                nuevosSelects.forEach(select => {
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.cod_producto;
                        option.textContent = producto.nombre_producto;
                        select.appendChild(option);
                    });
                });
            })
            .catch(error => console.error('Error loading products for new select:', error));
    };

    // Cargar métodos de pago
    const metodoPagoContenedor = document.getElementById('metodos_pago_contenedor');

    // Verificar que el elemento metodoPagoContenedor existe
    if (!metodoPagoContenedor) {
        console.error('No se encontró el elemento #metodos_pago_contenedor en el DOM.');
        return;
    }

    // Cargar métodos de pago en el contenedor inicial
    fetch('../Crear Factura/cargar_metodos_pago.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            const select = metodoPagoContenedor.querySelector('select[name="metodos_pago[]"]');
            data.forEach(metodo => {
                const option = document.createElement('option');
                option.value = metodo.id_metodo_pago;
                option.textContent = metodo.descripcion_pago;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading payment methods:', error));

    // Función para agregar campos de método de pago
    document.getElementById('agregar_metodo_pago').addEventListener('click', () => {
        const div = document.createElement('div');
        div.classList.add('metodo_pago');
        
        div.innerHTML = `
            <label for="metodo_pago">Método de Pago:</label>
            <select name="metodos_pago[]">
                <!-- Las opciones de métodos de pago se cargarán aquí con JS -->
            </select>
            <label for="porcentaje_pago">Porcentaje:</label>
            <input type="number" name="porcentajes_pago[]" step="0.01" min="0" max="100" required>
        `;
        
        metodoPagoContenedor.appendChild(div);
        
        // Actualizar las opciones de métodos de pago en el nuevo select
        fetch('../Crear Factura/cargar_metodos_pago.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(metodo => {
                    const option = document.createElement('option');
                    option.value = metodo.id_metodo_pago;
                    option.textContent = metodo.descripcion_pago;
                    div.querySelector('select').appendChild(option);
                });
            })
            .catch(error => console.error('Error loading payment methods for new select:', error));
    });
});
