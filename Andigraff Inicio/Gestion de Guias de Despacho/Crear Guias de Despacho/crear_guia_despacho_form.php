<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: login.php");
    exit();
}
$rut_usuario = $_SESSION['rut'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Guía de Despacho</title>
    <link rel="stylesheet" href="../styles/crear_guia_despacho.css">
    <script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('./cargar_bodegas.php')  // Asegúrate de que la ruta es correcta
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Verificar los datos en la consola
            const selectBodega = document.getElementById('bodega');
            if (data.error) {
                console.error(data.error);
                return;
            }
            data.forEach(bodega => {
                const option = document.createElement('option');
                option.value = bodega.cod_establecimiento;
                option.textContent = bodega.nombre_establecimiento; // Asegúrate de usar 'nombre_establecimiento'
                selectBodega.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar bodegas:', error));
});
</script>

</head>
<body>

<form action="crear_guia_despacho.php" method="post">
    <h2>Registro de Guía de Despacho</h2>
    
    <div class="form-group">
        <label for="rut">RUT</label>
        <input type="text" id="rut" name="rut" value="<?php echo htmlspecialchars($rut_usuario); ?>" readonly>
    </div>
    
    <div class="form-group">
        <label for="direccion_origen">Dirección de Origen</label>
        <input type="text" id="direccion_origen" name="direccion_origen">
    </div>
    
    <div class="form-group">
        <label for="direccion_destino">Dirección de Destino</label>
        <input type="text" id="direccion_destino" name="direccion_destino">
    </div>
    
    <div class="form-group">
        <label for="condicion_entrega">Condición de Entrega</label>
        <select id="condicion_entrega" name="condicion_entrega">
            <option value="1">Entregado</option>
            <option value="0">No Entregado</option>
        </select>
    </div>

    <div class="form-group">
        <label for="bodega">Bodega</label>
        <select id="bodega" name="bodega">
            <option value="">Selecciona una bodega</option>
            <!-- Options will be populated by JavaScript -->
        </select>
    </div>

    <div class="form-group">
        <label for="fecha_emicion_guia_despacho">Fecha de Emisión</label>
        <input type="date" id="fecha_emicion_guia_despacho" name="fecha_emicion_guia_despacho">
    </div>
    
    <input type="submit" value="Registrar">
</form>


</body>
</html>

