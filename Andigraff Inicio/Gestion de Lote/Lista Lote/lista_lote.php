<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Lotes</title>
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
    <h1>Lista de Lotes</h1>
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Preparar y ejecutar la consulta para obtener los lotes
    $query = "SELECT numero_lote, precio_total, peso_total, cantidad_inicial, cantidad_actual, tipo_embalaje FROM lote";
    $result = pg_query($conn, $query);

    // Verificar resultado
    if (!$result) {
        echo "Error en la consulta.";
        pg_close($conn);
        exit();
    }

    // Mostrar los lotes en una tabla
    echo "<table border='1'>
            <thead>
                <tr>
                    <th>Número de Lote</th>
                    <th>Precio Total</th>
                    <th>Peso Total</th>
                    <th>Cantidad Inicial</th>
                    <th>Cantidad Actual</th>
                    <th>Tipo de Embalaje</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['numero_lote']}</td>
                <td>{$row['precio_total']}</td>
                <td>{$row['peso_total']}</td>
                <td>{$row['cantidad_inicial']}</td>
                <td>{$row['cantidad_actual']}</td>
                <td>{$row['tipo_embalaje']}</td>
                <td class='actions'>
                    <a href='ver_lote.php?id={$row['numero_lote']}' title='Ver'><i class='fas fa-eye'></i></a>
                    <a href='../Actualizar Lote/actualizar_lote_form.php?id={$row['numero_lote']}' title='Editar'><i class='fas fa-edit'></i></a>
                    <a href='../Eliminar Lote/eliminar_lote.php?id={$row['numero_lote']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este lote?\");'><i class='fas fa-trash'></i></a>
                </td>
              </tr>";
    }

    echo "  </tbody>
          </table>";

    // Cerrar la conexión
    pg_close($conn);
    ?>
</body>
</html>

