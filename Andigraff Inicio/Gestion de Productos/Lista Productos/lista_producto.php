<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/ver_productos.css">
</head>
<body>
    <h1>Lista de Productos</h1>

    <div class="filter-container">
        <label for="filter-category">Filtrar por Categoría:</label>
        <select id="filter-category">
            <option value="">Todas</option>
            <option value="televisores">Televisores</option>
            <option value="computadoras">Computadoras</option>
            <option value="smartphones">Smartphones</option>
            <option value="tablets">Tablets</option>
            <option value="accesorios">Accesorios</option>
            <option value="audio">Audio</option>
            <option value="videojuegos">Videojuegos</option>
            <option value="electrodomesticos">Electrodomésticos</option>
            <option value="otros">Otros</option>
        </select>

        <label for="filter-type">Filtrar por Tipo:</label>
        <select id="filter-type">
            <option value="">Todos</option>
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
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Consultar los productos que no están eliminados (estado_producto = true)
    $query = "
        SELECT p.cod_producto, p.numero_lote, p.nombre_producto, p.precio_unitario, p.stock, p.tipo_producto, p.peso_unitario, p.descripcion_producto, p.categoria, p.stock_critico, pr.nombre_proveedor
        FROM producto p
        LEFT JOIN provee pv ON p.cod_producto = pv.cod_producto
        LEFT JOIN proveedor pr ON pv.id_proveedor = pr.id_proveedor
        WHERE p.estado_producto = true
    ";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error en la consulta.";
        return [];
    }

    $productos = [];
    while ($row = pg_fetch_assoc($result)) {
        $productos[] = $row;
    }

    if (empty($productos)) {
        echo "<p>No hay productos disponibles.</p>";
    } else {
        echo "<table id='productosTable' class='display'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número de Lote</th>
                        <th>Nombre del Producto</th>
                        <th>Precio Unitario</th>
                        <th>Stock</th>
                        <th>Tipo de Producto</th>
                        <th>Peso Unitario</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Stock Crítico</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($productos as $producto) {
            echo "<tr>
                    <td>{$producto['cod_producto']}</td>
                    <td>{$producto['numero_lote']}</td>
                    <td>{$producto['nombre_producto']}</td>
                    <td>{$producto['precio_unitario']}</td>
                    <td>{$producto['stock']}</td>
                    <td>{$producto['tipo_producto']}</td>
                    <td>{$producto['peso_unitario']}</td>
                    <td>{$producto['descripcion_producto']}</td>
                    <td>{$producto['categoria']}</td>
                    <td>{$producto['stock_critico']}</td>
                    <td>{$producto['nombre_proveedor']}</td>
                    <td class='actions'>
                        <a href='../Actualizar Productos/actualizar_producto_form.php?id={$producto['cod_producto']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='../Eliminar Productos/eliminar_producto.php?id={$producto['cod_producto']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este producto?\");'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    }

    // Cerrar la conexión
    pg_close($conn);
    ?>
    
    <a href="../../sidebar/sidebar.html" class="button">Regresar al Inicio</a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            var table = $('#productosTable').DataTable();

            // Filtrar por categoría
            $('#filter-category').on('change', function() {
                var category = $(this).val();
                if (category) {
                    table.columns(8).search('^' + category + '$', true, false).draw();
                } else {
                    table.columns(8).search('').draw();
                }
            });

            // Filtrar por tipo de producto
            $('#filter-type').on('change', function() {
                var type = $(this).val();
                if (type) {
                    table.columns(5).search('^' + type + '$', true, false).draw();
                } else {
                    table.columns(5).search('').draw();
                }
            });
        });
    </script>
</body>
</html>




