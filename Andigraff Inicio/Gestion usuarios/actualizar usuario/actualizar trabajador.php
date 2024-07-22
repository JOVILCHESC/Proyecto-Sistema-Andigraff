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
$cod_establecimiento = $_POST['cod_establecimiento'];
$fecha_contratacion = $_POST['fecha_contratacion'];
$estado_laboral = ($_POST['estado_laboral'] === 'Activo') ? 'TRUE' : 'FALSE';
$estado_civil = $_POST['estado_civil'];
$cargo = $_POST['cargo'];
$num_credencial = $_POST['num_credencial'];

// Iniciar una transacción
pg_query($connection, "BEGIN");

// Preparar la consulta SQL de actualización para la tabla trabajador
$update_trabajador_sql = "UPDATE trabajador SET 
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
        cod_establecimiento = $11,
        fecha_contratacion = $12,
        estado_laboral = $13::boolean,
        estado_civil = $14,
        cargo = $15,
        num_credencial = $16
    WHERE rut = $17";

// Crear el array de parámetros para la actualización
$trabajador_params = array($nombre, $apellido_materno, $apellido_paterno, $email, $sexo, $calle, $numero, $comuna, $fecha_nacimiento, $telefono_usuario, $cod_establecimiento, $fecha_contratacion, $estado_laboral, $estado_civil, $cargo, $num_credencial, $rut);

// Ejecutar la consulta de actualización
$update_trabajador_result = pg_query_params($connection, $update_trabajador_sql, $trabajador_params);

// Verificar si la actualización fue exitosa
if ($update_trabajador_result) {
    // Commit de la transacción si la actualización es exitosa
    pg_query($connection, "COMMIT");
    // Redirigir al usuario a la lista de trabajadores
    header('Location: ../ver_trabajadores.php');
    exit;
} else {
    // Rollback en caso de error en la actualización de la tabla trabajador
    pg_query($connection, "ROLLBACK");
    echo "<p>Error al actualizar el trabajador: " . pg_last_error($connection) . "</p>";
}

// Cerrar la conexión a la base de datos
pg_close($connection);
?>
