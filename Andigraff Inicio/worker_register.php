<?php
// Incluir el archivo de configuración
include '/src/src/config/config.php';

// Conexión a la base de datos
$connect = getDBConnection();

// Datos del formulario
$rut = $_POST['rut'];
$cod_establecimiento = $_POST['cod_establecimiento'];
$nombre = $_POST['nombre'];
$apellido_materno = $_POST['apellido_materno'];
$apellido_paterno = $_POST['apellido_paterno'];
$email = $_POST['email'];
$contrasena = $_POST['contrasena'];
$sexo = $_POST['sexo'];
$calle = $_POST['calle'];
$numero = $_POST['numero'];
$comuna = $_POST['comuna'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$telefono_usuario = $_POST['telefono_usuario'];
$fecha_contratacion = $_POST['fecha_contratacion'];
$estado_laboral = $_POST['estado_laboral'];
$estado_civil = $_POST['estado_civil'];
$cargo = $_POST['cargo'];
$num_credencial = $_POST['num_credencial'];

// Insertar datos en la tabla TRABAJADOR
$query = "INSERT INTO TRABAJADOR (rut, cod_establecimiento, nombre, apellido_materno, apellido_paterno, email, contraseña, sexo, calle, numero, comuna, fecha_nacimiento, telefono_usuario, fecha_contratacion, estado_laboral, estado_civil, cargo, num_credencial) 
          VALUES ('$rut', '$cod_establecimiento', '$nombre', '$apellido_materno', '$apellido_paterno', '$email', '$contrasena', '$sexo', '$calle', '$numero', '$comuna', '$fecha_nacimiento', '$telefono_usuario', '$fecha_contratacion', '$estado_laboral', '$estado_civil', '$cargo', '$num_credencial')";

$result = pg_query($connect, $query);

if ($result) {
    echo 'Registro de trabajador exitoso';
} else {
    echo 'Error al registrar trabajador: ' . pg_last_error($connect);
}

pg_close($connect);
?>

