<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $contacto_principal = substr($_POST['contacto_principal'], 0, 255);
    $ciudad = substr($_POST['ciudad'], 0, 255);
    $pais = substr($_POST['pais'], 0, 255);
    $email_proveedor = substr($_POST['email_proveedor'], 0, 15); // Ajustar la longitud máxima
    $telefono_proveedor = substr($_POST['telefono_proveedor'], 0, 15); // Ajustar la longitud máxima
    $codigo_postal = substr($_POST['codigo_postal'], 0, 255);
    $cod_pais = substr($_POST['cod_pais'], 0, 255);
    $nombre_proveedor = substr($_POST['nombre_proveedor'], 0, 255);
    $estado_proveedor = isset($_POST['estado_proveedor']) ? (bool) $_POST['estado_proveedor'] : false;

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL sin el campo id_proveedor
    $sql = "INSERT INTO proveedor (contacto_principal, ciudad, pais, email_proveedor, telefono_proveedor, codigo_postal, cod_pais, nombre_proveedor, estado_proveedor)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

    $params = array($contacto_principal, $ciudad, $pais, $email_proveedor, $telefono_proveedor, $codigo_postal, $cod_pais, $nombre_proveedor, $estado_proveedor);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);
    
    if ($result) {
        header("Location: ../Lista Proveedores/lista_proveedor.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al registrar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
?>
