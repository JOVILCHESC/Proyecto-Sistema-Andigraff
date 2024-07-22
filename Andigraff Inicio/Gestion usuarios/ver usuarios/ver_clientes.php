<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/ver_clientes.css">
</head>
<body>
    <h1>Lista de Clientes</h1>
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Consultar los clientes
    $query = "
        SELECT c.rut, c.nombre, c.apellido_materno, c.apellido_paterno, c.email, c.sexo, c.calle, c.numero, c.comuna, c.fecha_nacimiento, c.telefono_usuario, c.tipo_cliente
        FROM cliente c
    ";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error en la consulta.";
        return [];
    }

    $clientes = [];
    while ($row = pg_fetch_assoc($result)) {
        $clientes[] = $row;
    }

    if (empty($clientes)) {
        echo "<p>No hay clientes disponibles.</p>";
    } else {
        echo "<table id='clientesTable' class='display'>
                <thead>
                    <tr>
                        <th>RUT</th>
                        <th>Nombre</th>
                        <th>Apellido Materno</th>
                        <th>Apellido Paterno</th>
                        <th>Email</th>
                        <th>Sexo</th>
                        <th>Calle</th>
                        <th>Número</th>
                        <th>Comuna</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Teléfono</th>
                        <th>Tipo de Cliente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($clientes as $cliente) {
            echo "<tr>
                    <td>{$cliente['rut']}</td>
                    <td>{$cliente['nombre']}</td>
                    <td>{$cliente['apellido_materno']}</td>
                    <td>{$cliente['apellido_paterno']}</td>
                    <td>{$cliente['email']}</td>
                    <td>{$cliente['sexo']}</td>
                    <td>{$cliente['calle']}</td>
                    <td>{$cliente['numero']}</td>
                    <td>{$cliente['comuna']}</td>
                    <td>{$cliente['fecha_nacimiento']}</td>
                    <td>{$cliente['telefono_usuario']}</td>
                    <td>{$cliente['tipo_cliente']}</td>
                    <td class='actions'>
                        <a href='../Actualizar usuario/actualizar_cliente_form.php?rut={$cliente['rut']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='../borrar usuario/eliminar_cliente.php?rut={$cliente['rut']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este cliente?\");'><i class='fas fa-trash'></i></a>
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
            $('#clientesTable').DataTable();
        });
    </script>
</body>
</html>
