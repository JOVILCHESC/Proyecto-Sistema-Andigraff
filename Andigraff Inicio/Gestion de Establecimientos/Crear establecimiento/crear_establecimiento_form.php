<!DOCTYPE html>
<html>
<head>
    <title>Registro de Establecimiento</title>
    <link rel="stylesheet" href="../styles/crear_establecimiento.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Establecimiento</h1>
        <form action="./crear_establecimiento.php" method="post">
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="numero_estableciimiento">Número Establecimiento:</label>
                <input type="text" id="numero_estableciimiento" name="numero_estableciimiento" required>
            </div>

            <div class="form-group">
                <label for="comuna_establecimiento">Comuna:</label>
                <input type="text" id="comuna_establecimiento" name="comuna_establecimiento" required>
            </div>

            <div class="form-group">
                <label for="calle_establecimiento">Calle:</label>
                <input type="text" id="calle_establecimiento" name="calle_establecimiento" required>
            </div>

            <div class="form-group">
                <label for="ciudad_establecimiento">Ciudad:</label>
                <input type="text" id="ciudad_establecimiento" name="ciudad_establecimiento" required>
            </div>

            <div class="form-group">
                <label for="nombre_establecimiento">Nombre Establecimiento:</label>
                <input type="text" id="nombre_establecimiento" name="nombre_establecimiento" required>
            </div>

            <div class="form-group">
                <label for="cant_empleados">Cantidad de Empleados:</label>
                <input type="number" id="cant_empleados" name="cant_empleados" required>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo de Establecimiento:</label>
                <select id="tipo" name="tipo" required>
                    <option value="sucursal">Sucursal</option>
                    <option value="bodega">Bodega</option>
                </select>
            </div>

            <div id="sucursal_fields" class="form-group" style="display: none;">
                <label for="tipo_sucursal">Tipo de Sucursal:</label>
                <input type="text" id="tipo_sucursal" name="tipo_sucursal">
            </div>

            <div id="bodega_fields" class="form-group" style="display: none;">
                <label for="capacidad">Capacidad:</label>
                <input type="number" id="capacidad" name="capacidad"><br>

                <label for="tipo_almacenamiento">Tipo de Almacenamiento:</label>
                <input type="text" id="tipo_almacenamiento" name="tipo_almacenamiento"><br>

                <label for="estado_bodega">Estado de Bodega:</label>
                <input type="checkbox" id="estado_bodega" name="estado_bodega" value="1">
            </div>

            <button type="submit">Registrar</button>
        </form>
    </div>

    <script>
        document.getElementById('tipo').addEventListener('change', function () {
            var sucursalFields = document.getElementById('sucursal_fields');
            var bodegaFields = document.getElementById('bodega_fields');
            if (this.value === 'sucursal') {
                sucursalFields.style.display = 'block';
                bodegaFields.style.display = 'none';
            } else if (this.value === 'bodega') {
                sucursalFields.style.display = 'none';
                bodegaFields.style.display = 'block';
            } else {
                sucursalFields.style.display = 'none';
                bodegaFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>

