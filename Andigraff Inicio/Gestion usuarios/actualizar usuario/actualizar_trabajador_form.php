<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Trabajador</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/actualizar_trabajador.css">
</head>
<body>
    <h1>Actualizar Trabajador</h1>
    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $connection = getDBConnection();

    // Obtener el RUT del trabajador a actualizar
    $rut = isset($_GET['rut']) ? pg_escape_string($connection, $_GET['rut']) : '';

    // Consultar el trabajador específico
    $query = "SELECT * FROM trabajador WHERE rut = $1";
    $result = pg_query_params($connection, $query, array($rut));

    if (!$result) {
        echo "<p>Error en la consulta.</p>";
        exit;
    }

    $trabajador = pg_fetch_assoc($result);
    pg_close($connection);

    if (!$trabajador) {
        echo "<p>Trabajador no encontrado.</p>";
        exit;
    }
    ?>
    <form action="actualizar trabajador.php" method="POST">
        <input type="hidden" name="rut" value="<?php echo htmlspecialchars($trabajador['rut']); ?>">
        
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($trabajador['nombre']); ?>" required><br>

        <label for="apellido_materno">Apellido Materno:</label>
        <input type="text" id="apellido_materno" name="apellido_materno" value="<?php echo htmlspecialchars($trabajador['apellido_materno']); ?>"><br>

        <label for="apellido_paterno">Apellido Paterno:</label>
        <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?php echo htmlspecialchars($trabajador['apellido_paterno']); ?>"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($trabajador['email']); ?>"><br>

        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo">
            <option value="Masculino" <?php if ($trabajador['sexo'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
            <option value="Femenino" <?php if ($trabajador['sexo'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
            <option value="Otro" <?php if ($trabajador['sexo'] == 'Otro') echo 'selected'; ?>>Otro</option>
        </select><br>

        <label for="calle">Calle:</label>
        <input type="text" id="calle" name="calle" value="<?php echo htmlspecialchars($trabajador['calle']); ?>"><br>

        <label for="numero">Número:</label>
        <input type="number" id="numero" name="numero" value="<?php echo htmlspecialchars($trabajador['numero']); ?>"><br>

        <label for="comuna">Comuna:</label>
        <input type="text" id="comuna" name="comuna" value="<?php echo htmlspecialchars($trabajador['comuna']); ?>"><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($trabajador['fecha_nacimiento']); ?>"><br>

        <label for="telefono_usuario">Teléfono:</label>
        <input type="tel" id="telefono_usuario" name="telefono_usuario" value="<?php echo htmlspecialchars($trabajador['telefono_usuario']); ?>"><br>

        <label for="cod_establecimiento">Código de Establecimiento:</label>
        <input type="number" id="cod_establecimiento" name="cod_establecimiento" value="<?php echo htmlspecialchars($trabajador['cod_establecimiento']); ?>"><br>

        <label for="fecha_contratacion">Fecha de Contratación:</label>
        <input type="date" id="fecha_contratacion" name="fecha_contratacion" value="<?php echo htmlspecialchars($trabajador['fecha_contratacion']); ?>"><br>

        <label for="estado_laboral">Estado Laboral:</label>
        <select id="estado_laboral" name="estado_laboral">
            <option value="Activo" <?php if ($trabajador['estado_laboral'] == 'Activo') echo 'selected'; ?>>Activo</option>
            <option value="Inactivo" <?php if ($trabajador['estado_laboral'] == 'Inactivo') echo 'selected'; ?>>Inactivo</option>
        </select><br>

        <label for="estado_civil">Estado Civil:</label>
        <input type="text" id="estado_civil" name="estado_civil" value="<?php echo htmlspecialchars($trabajador['estado_civil']); ?>"><br>

        <label for="cargo">Cargo:</label>
        <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($trabajador['cargo']); ?>"><br>

        <label for="num_credencial">Número de Credencial:</label>
        <input type="text" id="num_credencial" name="num_credencial" value="<?php echo htmlspecialchars($trabajador['num_credencial']); ?>"><br>

        <button type="submit">Actualizar</button>
    </form>

    <a href="../ver_trabajadores.php" class="button">Cancelar</a>
</body>
</html>
