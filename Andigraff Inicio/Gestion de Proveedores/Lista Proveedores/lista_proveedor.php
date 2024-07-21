<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Proveedores</title>
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
            text-decoration: none.
        }
        .actions a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>Lista de Proveedores</h1>
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Obtener los proveedores activos de la base de datos
    $query = 'SELECT id_proveedor, contacto_principal, ciudad, pais, email_proveedor, telefono_proveedor, codigo_postal, cod_pais, nombre_proveedor FROM proveedor WHERE estado_proveedor = true';
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error en la consulta.";
        return;
    }

    echo "<table border='1'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contacto Principal</th>
                    <th>Ciudad</th>
                    <th>País</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Código Postal</th>
                    <th>Código del País</th>
                    <th>Nombre del Proveedor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id_proveedor']}</td>
                <td>{$row['contacto_principal']}</td>
                <td>{$row['ciudad']}</td>
                <td>{$row['pais']}</td>
                <td>{$row['email_proveedor']}</td>
                <td>{$row['telefono_proveedor']}</td>
                <td>{$row['codigo_postal']}</td>
                <td>{$row['cod_pais']}</td>
                <td>{$row['nombre_proveedor']}</td>
                <td class='actions'>
                    <a href='../Actualizar Proveedores/actualizar_proveedor_form.php?id={$row['id_proveedor']}' title='Editar'><i class='fas fa-edit'></i></a>
                    <a href='../Eliminar Proveedores/eliminar_proveedor.php?id={$row['id_proveedor']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este proveedor?\");'><i class='fas fa-trash'></i></a>
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
