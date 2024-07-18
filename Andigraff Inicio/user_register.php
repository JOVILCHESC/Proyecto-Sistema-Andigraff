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

// Insertar datos en la tabla USUARIO
$query_usuario = "INSERT INTO USUARIO (rut, Nombre, Apellido_paterno, Apellido_materno, email, Contrasena, Sexo, Calle, Numero, Comuna, Fecha_nacimiento, telefono_usuario)
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";
$params_usuario = array($rut, $nombre, $apellido_paterno, $apellido_materno, $email, $contrasena, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono);
$result_usuario = pg_query_params($conn, $query_usuario, $params_usuario);

if (!$result_usuario) {
    $error_message = pg_last_error($conn);
    die("Error al registrar usuario: " . $error_message);
}

// Inserción dependiendo del tipo de usuario
if ($tipo_usuario === 'trabajador') {
    $cargo = $_POST['cargo'];
    $estado_civil = $_POST['estado_civil'];
    $num_credencial = $_POST['num_credencial'];
    $cod_establecimiento = $_POST['cod_establecimiento']; // Nuevo campo para sucursal

    // Preparar la consulta para trabajador
    $query_trabajador = "INSERT INTO TRABAJADOR (rut, Nombre, Apellido_paterno, Apellido_materno, email, Contrasena, Sexo, Calle, Numero, Comuna, Fecha_nacimiento, telefono_usuario, cod_establecimiento, Fecha_contratacion, Estado_laboral, Estado_civil, Cargo, num_credencial)
                         VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, TRUE, $15, $16, $17)";
    $params_trabajador = array($rut, $nombre, $apellido_paterno, $apellido_materno, $email, $contrasena, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono, $cod_establecimiento, '2022-01-01', $estado_civil, $cargo, $num_credencial);
    $result_trabajador = pg_query_params($conn, $query_trabajador, $params_trabajador);

    if (!$result_trabajador) {
        $error_message = pg_last_error($conn);
        die("Error al registrar trabajador: " . $error_message);
    }
} elseif ($tipo_usuario === 'cliente') {
    $tipo_cliente = $_POST['tipo_cliente'];

    // Preparar la consulta para cliente
    $query_cliente = "INSERT INTO CLIENTE (rut, Nombre, Apellido_paterno, Apellido_materno, email, Contrasena, Sexo, Calle, Numero, Comuna, Fecha_nacimiento, telefono_usuario, tipo_cliente)
                      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)";
    $params_cliente = array($rut, $nombre, $apellido_paterno, $apellido_materno, $email, $contrasena, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono, $tipo_cliente);
    $result_cliente = pg_query_params($conn, $query_cliente, $params_cliente);

    if (!$result_cliente) {
        $error_message = pg_last_error($conn);
        die("Error al registrar cliente: " . $error_message);
    }
}

echo "Usuario registrado correctamente.";

// Cerrar la conexión
pg_close($conn);
?>
