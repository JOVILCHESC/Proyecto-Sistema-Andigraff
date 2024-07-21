<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Productos - SuSol</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('BaseDeDatos.png');
            background-size: cover;
            background-repeat: no-repeat;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"], .search-bar select, .search-bar button {
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .search-bar input[type="text"] {
            border-radius: 5px 0 0 5px;
            width: 300px;
        }

        .search-bar select {
            border-radius: 0;
        }

        .search-bar button {
            background-color: #333;
            color: #fff;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-bar button:hover {
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .btn-actualizar {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            background-color: #2196F3;
            color: white;
        }

        /* Estilo para el botón "Volver" */
        .volver {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Ver Productos</h1>

    <div class="search-bar">
        <form method="get" action="">
            <select name="atributo">
                <option value="id_producto">ID Producto</option>
                <option value="tipo_pro">Tipo</option>
                <option value="modelo_pro">Modelo</option>
                <option value="marca_pro">Marca</option>
            </select>
            <input type="text" name="buscar" placeholder="Buscar..." value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>">
            <button type="submit">Buscar</button>
            <input type="checkbox" name="incluir_eliminados" id="incluir_eliminados" <?php echo isset($_GET['incluir_eliminados']) ? 'checked' : ''; ?>>
            <label for="incluir_eliminados">Incluir eliminados</label>
        </form>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID Producto</th>
            <th>Tipo</th>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Peso (kg)</th>
            <th>Estado</th>
            <th>Eliminado En</th>
            <th>Motivo Eliminado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>

        <?php
        // Incluir el archivo de conexión
        include 'conexion.php';

        // Construir la consulta SQL base
        $sql = "SELECT * FROM producto";

        // Agregar la cláusula WHERE correctamente
        if (isset($_GET['buscar']) && isset($_GET['atributo'])) {
            $atributo = pg_escape_string($connect, $_GET['atributo']);
            $buscar = pg_escape_string($connect, $_GET['buscar']);

            // Verificar si el atributo es id_producto y realizar la conversión
            if ($atributo == 'id_producto') {
                $atributo = "CAST($atributo AS TEXT)";
            }

            // Verificar si ya hay una cláusula WHERE
            if (strpos($sql, "WHERE") === false) {
                $sql .= " WHERE";
            } else {
                $sql .= " AND";
            }

            $sql .= " $atributo LIKE '%$buscar%'";
        }

        // Agregar condición para mostrar eliminados solo si no se incluyeron en la búsqueda
        if (!isset($_GET['incluir_eliminados']) && strpos($sql, "WHERE") === false) { 
            $sql .= " WHERE eliminado_en IS NULL"; 
        } elseif (!isset($_GET['incluir_eliminados'])) {
            $sql .= " AND eliminado_en IS NULL";
        }

        // Ejecutar la consulta
        $resultado = pg_query($connect, $sql);

        // Mostrar los resultados en la tabla
        while ($fila = pg_fetch_assoc($resultado)): 
        ?>
            <tr>
                <td><?php echo $fila['id_producto']; ?></td>
                <td><?php echo $fila['tipo_pro']; ?></td>
                <td><?php echo $fila['modelo_pro']; ?></td>
                <td><?php echo $fila['marca_pro']; ?></td>
                <td><?php echo $fila['peso_pro']; ?></td>
                <td><?php echo $fila['estado_pro']; ?></td>
                <td><?php echo $fila['eliminado_en']; ?></td>
                <td><?php echo $fila['motivoelimanodo']; ?></td>
                <td>
                    <button class="btn-actualizar" onclick="actualizarProducto(<?php echo $fila['id_producto']; ?>)">Actualizar</button>
                </td>
            </tr>
        <?php endwhile; ?>
        
        </tbody>
    </table>

    <div class="volver">
        <a href="index.php">Volver al Panel de Control</a>
    </div>
</div>

<script>
    function actualizarProducto(idProducto) {
        // Redireccionar a la página de actualización con el ID del producto
        window.location.href = `actualizar_producto.php?producto_id=${idProducto}`;
    }
</script>
</body>
</html>