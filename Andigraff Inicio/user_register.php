<?php
require_once('config/config.php');

// Obtener la conexión a la base de datos
$conn = getDBConnection();

// Recibir datos del formulario
$rut = $_POST['rut'];
$nombre = $_POST['nombre'];
$apellido_paterno = $_POST['apellido_paterno'];
$apellido_materno = $_POST['apellido_materno'];
$email = $_POST['email'];
$contrasena = $_POST['contrasena'];
$sexo = $_POST['sexo'];
$calle = $_POST['calle'];
$numero = $_POST['numero'];
$comuna = $_POST['comuna'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$telefono = $_POST['telefono'];
$tipo_usuario = $_POST['tipo_usuario'];

// Inserción dependiendo del tipo de usuario
if ($tipo_usuario === 'trabajador') {
    $cargo = $_POST['cargo'];
    $fecha_contratacion = $_POST['fecha_contratacion'];

    // Preparar la consulta para trabajador
    $query = "INSERT INTO TRABAJADOR (rut, Nombre, Apellido_paterno, Apellido_materno, email, Contrasena, Sexo, Calle, Numero, Comuna, Fecha_nacimiento, telefono_usuario, Fecha_contratacion, Estado_laboral, Estado_civil, Cargo, num_credencial)
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17)";
    $params = array($rut, $nombre, $apellido_paterno, $apellido_materno, $email, $contrasena, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono, $fecha_contratacion, true, 'SIN_ESPECIFICAR', $cargo, 0);
} elseif ($tipo_usuario === 'cliente') {
    $tipo_cliente = $_POST['tipo_cliente'];

    // Preparar la consulta para cliente
    $query = "INSERT INTO CLIENTE (rut, Nombre, Apellido_paterno, Apellido_materno, email, Contrasena, Sexo, Calle, Numero, Comuna, Fecha_nacimiento, telefono_usuario, tipo_cliente)
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)";
    $params = array($rut, $nombre, $apellido_paterno, $apellido_materno, $email, $contrasena, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono, $tipo_cliente);
}

// Ejecutar la consulta con parámetros
$result = pg_query_params($conn, $query, $params);

if (!$result) {
    $error_message = pg_last_error($conn);
    die("Error al registrar usuario: " . $error_message);
} else {
    echo "Usuario registrado correctamente.";
}

// Cerrar la conexión
pg_close($conn);
?>
