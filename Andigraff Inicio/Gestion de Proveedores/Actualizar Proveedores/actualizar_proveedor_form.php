<?php
session_start();

// Verificar si se ha proporcionado el ID del proveedor
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID de proveedor no proporcionado.');
}

$id_proveedor = intval($_GET['id']); // Sanitizar la entrada

// Incluir el archivo de configuración para obtener la conexión
require_once(__DIR__ . '/../../config/config.php');

// Conectar a la base de datos
$conn = getDBConnection();

// Verificar conexión
if (!$conn) {
    die("Error en la conexión: " . pg_last_error());
}

// Obtener los datos del proveedor por ID
$query = "SELECT contacto_principal, ciudad, pais, email_proveedor, telefono_proveedor, codigo_postal, cod_pais, nombre_proveedor, estado_proveedor FROM proveedor WHERE id_proveedor = $1";
$result = pg_query_params($conn, $query, array($id_proveedor));

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conn));
}

$proveedor = pg_fetch_assoc($result);

if (!$proveedor) {
    die('Proveedor no encontrado.');
}

// Cerrar la conexión
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Proveedor</title>
    <link rel="stylesheet" href="../styles/crear_proveedor.css">
</head>
<body>
    <form action="actualizar_proveedor.php" method="post">
        <h2>Actualizar Proveedor</h2>
        
        <input type="hidden" name="id_proveedor" value="<?php echo htmlspecialchars($id_proveedor); ?>">

        <div class="form-group">
            <label for="contacto_principal">Contacto Principal</label>
            <input type="text" id="contacto_principal" name="contacto_principal" value="<?php echo htmlspecialchars($proveedor['contacto_principal']); ?>" required>
        </div>

        <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($proveedor['ciudad']); ?>" required>
        </div>

        <div class="form-group">
            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" value="<?php echo htmlspecialchars($proveedor['pais']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email_proveedor">Email</label>
            <input type="email" id="email_proveedor" name="email_proveedor" value="<?php echo htmlspecialchars($proveedor['email_proveedor']); ?>" required>
        </div>

        <div class="form-group">
            <label for="telefono_proveedor">Teléfono</label>
            <input type="text" id="telefono_proveedor" name="telefono_proveedor" value="<?php echo htmlspecialchars($proveedor['telefono_proveedor']); ?>" required>
        </div>

        <div class="form-group">
            <label for="codigo_postal">Código Postal</label>
            <input type="text" id="codigo_postal" name="codigo_postal" value="<?php echo htmlspecialchars($proveedor['codigo_postal']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cod_pais">Código del País</label>
            <input type="text" id="cod_pais" name="cod_pais" value="<?php echo htmlspecialchars($proveedor['cod_pais']); ?>" required>
        </div>

        <div class="form-group">
            <label for="nombre_proveedor">Nombre del Proveedor</label>
            <input type="text" id="nombre_proveedor" name="nombre_proveedor" value="<?php echo htmlspecialchars($proveedor['nombre_proveedor']); ?>" required>
        </div>

        <div class="form-group">
            <label for="estado_proveedor">Estado del Proveedor</label>
            <input type="text" id="estado_proveedor_mostrar" value="Activo" readonly>
        </div>

        <input type="submit" value="Actualizar">
    </form>
</body>
</html>




