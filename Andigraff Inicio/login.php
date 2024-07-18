<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuario</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <form action="login.php" method="POST">
        <h2>Login de Usuario</h2>

        <div class="form-group">
            <label for="rut">RUT:</label>
            <input type="text" id="rut" name="rut" required>
        </div>

        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>

        <input type="submit" value="Iniciar Sesión">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('config/config.php');
        
        // Obtener la conexión a la base de datos
        $conn = getDBConnection();
        
        // Recibir datos del formulario
        $rut = $_POST['rut'];
        $contrasena = $_POST['contrasena'];
        
        // Consulta para verificar credenciales
        $query_login = "SELECT * FROM USUARIO WHERE rut = $1 AND Contrasena = $2";
        $params_login = array($rut, $contrasena);
        $result_login = pg_query_params($conn, $query_login, $params_login);
        
        if (!$result_login) {
            $error_message = pg_last_error($conn);
            die("Error en la consulta: " . $error_message);
        }
        
        $count = pg_num_rows($result_login);
        
        if ($count == 1) {
            // Inicio de sesión exitoso
            session_start();
            $_SESSION['rut'] = $rut;
            $_SESSION['tipo_usuario'] = pg_fetch_assoc($result_login)['tipo_usuario'];
            header("location: dashboard_admin.php"); // Redirigir al dashboard o página principal
        } else {
            echo "<p>El RUT o la contraseña son incorrectos. Por favor, intenta nuevamente.</p>";
        }
        
        // Cerrar la conexión
        pg_close($conn);
    }
    ?>
</body>
</html>
