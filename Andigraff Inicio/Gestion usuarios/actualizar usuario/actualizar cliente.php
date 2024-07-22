<?php
session_start();

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['rut'])) {
    die('Datos del formulario no válidos.');
}

// Obtener la conexión a la base de datos
$connection = getDBConnection();

// Verificar la conexión
if (!$connection) {
    die("Error en la conexión: " . pg_last_error());
}

// Obtener los datos del formulario
$rut = $_POST['rut'];
$nombre = $_POST['nombre'];
$apellido_materno = $_POST['apellido_materno'];
$apellido_paterno = $_POST['apellido_paterno'];
$email = $_POST['email'];
$sexo = $_POST['sexo'];
$calle = $_POST['calle'];
$numero = $_POST['numero'];
$comuna = $_POST['comuna'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$telefono_usuario = $_POST['telefono_usuario'];
$tipo_cliente = $_POST['tipo_cliente'];

// Escapar los datos para evitar SQL Injection
$rut = pg_escape_string($connection, $rut);
$nombre = pg_escape_string($connection, $nombre);
$apellido_materno = pg_escape_string($connection, $apellido_materno);
$apellido_paterno = pg_escape_string($connection, $apellido_paterno);
$email = pg_escape_string($connection, $email);
$sexo = pg_escape_string($connection, $sexo);
$calle = pg_escape_string($connection, $calle);
$numero = pg_escape_string($connection, $numero);
$comuna = pg_escape_string($connection, $comuna);
$fecha_nacimiento = pg_escape_string($connection, $fecha_nacimiento);
$telefono_usuario = pg_escape_string($connection, $telefono_usuario);
$tipo_cliente = pg_escape_string($connection, $tipo_cliente);

// Iniciar una transacción
pg_query($connection, "BEGIN");

// Preparar la consulta SQL de actualización para la tabla cliente
$update_cliente_sql = "UPDATE cliente SET 
        nombre = $1,
        apellido_materno = $2,
        apellido_paterno = $3,
        email = $4,
        sexo = $5,
        calle = $6,
        numero = $7,
        comuna = $8,
        fecha_nacimiento = $9,
        telefono_usuario = $10,
        tipo_cliente = $11
    WHERE rut = $12";

// Crear el array de parámetros para la actualización
$cliente_params = array($nombre, $apellido_materno, $apellido_paterno, $email, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono_usuario, $tipo_cliente, $rut);

// Ejecutar la consulta de actualización
$update_cliente_result = pg_query_params($connection, $update_cliente_sql, $cliente_params);

// Verificar si la actualización fue exitosa
if ($update_cliente_result) {
    // Commit de la transacción si la actualización es exitosa
    pg_query($connection, "COMMIT");
    // Redirigir al usuario a la lista de clientes
    header('Location: ../ver_clientes.php');
    exit;
} else {
    // Rollback en caso de error en la actualización
    pg_query($connection, "ROLLBACK");
    echo "<p>Error al actualizar el cliente: " . pg_last_error($connection) . "</p>";
}

// Cerrar la conexión a la base de datos
pg_close($connection);
?>
