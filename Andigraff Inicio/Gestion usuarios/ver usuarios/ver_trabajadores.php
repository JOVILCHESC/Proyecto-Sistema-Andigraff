<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Trabajadores</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/ver_trabajadores.css">
</head>
<body>
    <h1>Lista de Trabajadores</h1>
    
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Consultar los trabajadores
    $query = "
        SELECT t.rut, t.nombre, t.apellido_materno, t.apellido_paterno, t.email, t.sexo, t.calle, t.numero, t.comuna, t.fecha_nacimiento, t.telefono_usuario, t.cod_establecimiento, t.fecha_contratacion, t.estado_laboral, t.estado_civil, t.cargo, t.num_credencial, e.nombre_establecimiento
        FROM trabajador t
        LEFT JOIN establecimiento e ON t.cod_establecimiento = e.cod_establecimiento
    ";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error en la consulta.";
        return [];
    }

    $trabajadores = [];
    while ($row = pg_fetch_assoc($result)) {
        $trabajadores[] = $row;
    }

    if (empty($trabajadores)) {
        echo "<p>No hay trabajadores disponibles.</p>";
    } else {
        echo "<table id='trabajadoresTable' class='display'>
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
                        <th>Establecimiento</th>
                        <th>Fecha de Contratación</th>
                        <th>Estado Laboral</th>
                        <th>Estado Civil</th>
                        <th>Cargo</th>
                        <th>Número de Credencial</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($trabajadores as $trabajador) {
            $estado_laboral = $trabajador['estado_laboral'] ? 'Activo' : 'Inactivo';
            echo "<tr>
                    <td>{$trabajador['rut']}</td>
                    <td>{$trabajador['nombre']}</td>
                    <td>{$trabajador['apellido_materno']}</td>
                    <td>{$trabajador['apellido_paterno']}</td>
                    <td>{$trabajador['email']}</td>
                    <td>{$trabajador['sexo']}</td>
                    <td>{$trabajador['calle']}</td>
                    <td>{$trabajador['numero']}</td>
                    <td>{$trabajador['comuna']}</td>
                    <td>{$trabajador['fecha_nacimiento']}</td>
                    <td>{$trabajador['telefono_usuario']}</td>
                    <td>{$trabajador['nombre_establecimiento']}</td>
                    <td>{$trabajador['fecha_contratacion']}</td>
                    <td>{$estado_laboral}</td>
                    <td>{$trabajador['estado_civil']}</td>
                    <td>{$trabajador['cargo']}</td>
                    <td>{$trabajador['num_credencial']}</td>
                    <td class='actions'>
                        <a href='../Actualizar usuario/actualizar_trabajador_form.php?rut={$trabajador['rut']}' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='../borrar usuario/eliminar_trabajador.php?rut={$trabajador['rut']}' title='Eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este trabajador?\");'><i class='fas fa-trash'></i></a>
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
            $('#trabajadoresTable').DataTable();
        });
    </script>
</body>
</html>
