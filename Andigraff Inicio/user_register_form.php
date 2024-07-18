<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="styles/register.css">
</head>
<body>
    <form action="user_register.php" method="POST">
        <h2>Registro de Usuario</h2>

        <div class="form-group">
            <label for="rut">RUT:</label>
            <input type="text" id="rut" name="rut" required>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="apellido_paterno">Apellido Paterno:</label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" required>
        </div>

        <div class="form-group">
            <label for="apellido_materno">Apellido Materno:</label>
            <input type="text" id="apellido_materno" name="apellido_materno">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>

        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <input type="text" id="sexo" name="sexo">
        </div>

        <div class="form-group">
            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle">
        </div>

        <div class="form-group">
            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero">
        </div>

        <div class="form-group">
            <label for="comuna">Comuna:</label>
            <input type="text" id="comuna" name="comuna">
        </div>

        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono">
        </div>


        <div class="form-group">
            <label for="tipo_usuario">Tipo de Usuario:</label>
            <select id="tipo_usuario" name="tipo_usuario" required>
                <option value="cliente">Cliente</option>
                <option value="trabajador">Trabajador</option>
            </select>
        </div>

        <div id="campos_trabajador" class="form-group" style="display: none;">
            <label for="cargo">Cargo (Trabajador):</label>
            <select id="cargo" name="cargo">
                <option value="Ventas RXX">Ventas</option>
                <option value="Contabilidad">Contabilidad</option>
                <option value="Tesorería">Tesorería</option>
                <option value="Facturación">Facturación</option>
                <option value="Bodegas">Bodegas</option>
            </select>
        
            <label for="estado_civil">Estado Civil:</label>
            <select id="estado_civil" name="estado_civil">
                <option value="SOLTERO">Soltero</option>
                <option value="CASADO">Casado</option>
                <option value="DIVORCIADO">Divorciado</option>
                <option value="VIUDO">Viudo</option>
            </select>
        
            <label for="num_credencial">Número de Credencial:</label>
            <input type="text" id="num_credencial" name="num_credencial" value="0">
        
            <label for="cod_establecimiento">Sucursal:</label>
            <select id="cod_establecimiento" name="cod_establecimiento">
                <!-- Opciones de sucursales deben ser cargadas dinámicamente desde la base de datos -->
                <?php
                // Obtener la conexión a la base de datos
                require_once('config/config.php');
                $conn = getDBConnection();
                
                // Consulta para obtener las sucursales disponibles
                $query_sucursales = "SELECT cod_establecimiento, nombre_establecimiento FROM SUCURSAL";
                $result_sucursales = pg_query($conn, $query_sucursales);
                
                // Iterar sobre los resultados y generar opciones
                while ($row = pg_fetch_assoc($result_sucursales)) {
                    echo "<option value=\"" . $row['cod_establecimiento'] . "\">" . $row['nombre_establecimiento'] . "</option>";
                }
                
                // Cerrar la conexión
                pg_close($conn);
                ?>
            </select>
        </div>
        

        

        <div id="campos_cliente" class="form-group" style="display: none;">
            <label for="tipo_cliente">Tipo de Cliente:</label>
            <select id="tipo_cliente" name="tipo_cliente">
                <option value="minorista">Minorista</option>
                <option value="mayorista">Mayorista</option>
            </select>
        </div>

        <input type="submit" value="Registrar">
    </form>

    <script>
        document.getElementById('tipo_usuario').addEventListener('change', function() {
            var tipoUsuario = this.value;
            document.getElementById('campos_trabajador').style.display = (tipoUsuario === 'trabajador') ? 'block' : 'none';
            document.getElementById('campos_cliente').style.display = (tipoUsuario === 'cliente') ? 'block' : 'none';
        });
    </script>
</body>
</html>

