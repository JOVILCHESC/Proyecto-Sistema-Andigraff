<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['rut'])) {
        header("Location: login.php");
        exit();
    }

    $id_proveedor = intval($_POST['id_proveedor']);
    $contacto_principal = substr($_POST['contacto_principal'], 0, 255);
    $ciudad = substr($_POST['ciudad'], 0, 255);
    $pais = substr($_POST['pais'], 0, 255);
    $email_proveedor = substr($_POST['email_proveedor'], 0, 255);
    $telefono_proveedor = substr($_POST['telefono_proveedor'], 0, 15); // Ajustar la longitud máxima
    $codigo_postal = substr($_POST['codigo_postal'], 0, 255);
    $cod_pais = substr($_POST['cod_pais'], 0, 255);
    $nombre_proveedor = substr($_POST['nombre_proveedor'], 0, 255);
    $estado_proveedor = 'true'; // Siempre establecer en true

    // Incluir el archivo de configuración para obtener la conexión
    require_once(__DIR__ . '/../../config/config.php');

    // Conectar a la base de datos
    $conn = getDBConnection();

    // Preparar la consulta SQL
    $sql = "UPDATE proveedor SET contacto_principal = $1, ciudad = $2, pais = $3, email_proveedor = $4, telefono_proveedor = $5, codigo_postal = $6, cod_pais = $7, nombre_proveedor = $8, estado_proveedor = $9 WHERE id_proveedor = $10";

    $params = array($contacto_principal, $ciudad, $pais, $email_proveedor, $telefono_proveedor, $codigo_postal, $cod_pais, $nombre_proveedor, $estado_proveedor, $id_proveedor);

    // Ejecutar la consulta
    $result = pg_query_params($conn, $sql, $params);
    
    if ($result) {
        header("Location: ../Lista Proveedores/lista_proveedor.php"); // Redirigir a la vista de éxito
        exit();
    } else {
        echo "Error al actualizar: " . pg_last_error($conn);
    }

    // Cerrar la conexión
    pg_close($conn);
}
