<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}

// Conectar a PostgreSQL
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Obtener bodegas
$queryBodegas = "SELECT cod_establecimiento, nombre_establecimiento FROM public.bodega";
$resultBodegas = pg_query($conn, $queryBodegas);

if (!$resultBodegas) {
    echo "Error en la consulta de bodegas: " . pg_last_error($conn);
    exit();
}

// Obtener sucursales
$querySucursales = "SELECT cod_establecimiento, nombre_establecimiento FROM public.sucursal";
$resultSucursales = pg_query($conn, $querySucursales);

if (!$resultSucursales) {
    echo "Error en la consulta de sucursales: " . pg_last_error($conn);
    exit();
}

// Obtener datos para pasar a JavaScript
$bodegas = pg_fetch_all($resultBodegas);
$sucursales = pg_fetch_all($resultSucursales);

pg_free_result($resultBodegas);
pg_free_result($resultSucursales);
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Suministro</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/crear_suministro.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bodegas = <?php echo json_encode($bodegas); ?>;
            const sucursales = <?php echo json_encode($sucursales); ?>;

            const bodegaSelect = document.getElementById('bodega');
            const sucursalSelect = document.getElementById('sucursal');
            const productosDiv = document.getElementById('productos');

            bodegas.forEach(bodega => {
                const option = document.createElement('option');
                option.value = bodega.cod_establecimiento;
                option.textContent = bodega.nombre_establecimiento;
                bodegaSelect.appendChild(option);
            });

            sucursales.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.cod_establecimiento;
                option.textContent = sucursal.nombre_establecimiento;
                sucursalSelect.appendChild(option);
            });

            // Cargar productos mediante AJAX
            function cargarProductos() {
    return fetch('./cargar_productos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Red error');
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

                productosDiv.appendChild(productoDiv);
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
                <!-- Opciones de bodegas se llenan mediante JavaScript -->
            </select>
        </div>
        <div class="form-group">
            <label for="sucursal">Sucursal:</label>
            <select id="sucursal" name="suc_cod_establecimiento" required>
                <option value="">Seleccione una sucursal</option>
                <!-- Opciones de sucursales se llenan mediante JavaScript -->
            </select>
        </div>
        <div id="productos">
            <div class="producto">
                
            </div>
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
