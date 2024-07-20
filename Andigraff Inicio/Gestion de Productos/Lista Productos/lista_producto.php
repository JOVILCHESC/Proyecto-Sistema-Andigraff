<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilo básico para los iconos de los botones */
        .actions {
            text-align: center;
        }
        .actions a {
            color: black;
            margin: 0 5px;
            text-decoration: none;
        }
        .actions a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>Lista de Productos</h1>
    
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
    $query = "SELECT cod_producto, numero_lote, nombre_producto, precio_unitario, stock, tamano, tipo_producto, peso_unitario, iva, descripcion_producto, categoria, stock_critico FROM producto WHERE estado_producto = true";
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
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número de Lote</th>
                        <th>Nombre del Producto</th>
                        <th>Precio Unitario</th>
                        <th>Stock</th>
                        <th>Tamaño</th>
                        <th>Tipo de Producto</th>
                        <th>Peso Unitario</th>
                        <th>IVA</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Stock Crítico</th>
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
                    <td>{$producto['tamano']}</td>
                    <td>{$producto['tipo_producto']}</td>
                    <td>{$producto['peso_unitario']}</td>
                    <td>{$producto['iva']}</td>
                    <td>{$producto['descripcion_producto']}</td>
                    <td>{$producto['categoria']}</td>
                    <td>{$producto['stock_critico']}</td>
                    <td class='actions'>
                        <a href='ver_producto.php?id={$producto['cod_producto']}' title='Ver'><i class='fas fa-eye'></i></a>
                        <a href='../Actualizar Productos/actualizar_producto_form.php?id={$producto['cod_producto']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='eliminar_producto.php?id={$producto['cod_producto']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este producto?\");'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    }

    // Cerrar la conexión
    pg_close($conn);
    ?>
</body>
</html>
