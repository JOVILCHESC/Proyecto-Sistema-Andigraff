<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Cliente</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/actualizar_cliente.css">
</head>
<body>
    <h1>Actualizar Cliente</h1>

    <?php
    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Obtener el RUT del cliente a actualizar
    if (!isset($_GET['rut'])) {
        die('RUT no proporcionado.');
    }

    $rut = $_GET['rut'];

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Verificar conexión
    if (!$conn) {
        die("Error en la conexión: " . pg_last_error());
    }

    // Consultar los detalles del cliente
    $query = "SELECT * FROM cliente WHERE rut = $1";
    $result = pg_query_params($conn, $query, array($rut));

    if (!$result) {
        die("Error en la consulta: " . pg_last_error());
    }

    $cliente = pg_fetch_assoc($result);

    if (!$cliente) {
        die("Cliente no encontrado.");
    }
    ?>

    <form action="actualizar cliente.php" method="POST">
        <input type="hidden" name="rut" value="<?php echo htmlspecialchars($cliente['rut']); ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>

        <label for="apellido_materno">Apellido Materno:</label>
        <input type="text" id="apellido_materno" name="apellido_materno" value="<?php echo htmlspecialchars($cliente['apellido_materno']); ?>" required>

        <label for="apellido_paterno">Apellido Paterno:</label>
        <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?php echo htmlspecialchars($cliente['apellido_paterno']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>

        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="Masculino" <?php if ($cliente['sexo'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
            <option value="Femenino" <?php if ($cliente['sexo'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
        </select>

        <label for="calle">Calle:</label>
        <input type="text" id="calle" name="calle" value="<?php echo htmlspecialchars($cliente['calle']); ?>" required>

        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($cliente['numero']); ?>" required>

        <label for="comuna">Comuna:</label>
        <input type="text" id="comuna" name="comuna" value="<?php echo htmlspecialchars($cliente['comuna']); ?>" required>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($cliente['fecha_nacimiento']); ?>" required>

        <label for="telefono_usuario">Teléfono:</label>
        <input type="text" id="telefono_usuario" name="telefono_usuario" value="<?php echo htmlspecialchars($cliente['telefono_usuario']); ?>" required>

        <label for="tipo_cliente">Tipo de Cliente:</label>
        <input type="text" id="tipo_cliente" name="tipo_cliente" value="<?php echo htmlspecialchars($cliente['tipo_cliente']); ?>" required>

        <button type="submit">Actualizar Cliente</button>
    </form>

    <a href="../ver_clientes.php" class="button">Regresar a la Lista de Clientes</a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
