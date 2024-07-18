<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../responsive-sidebar-navigation-master/responsive-sidebar-navigation-master/assets/css/style.css">
</head>
<body>
<?php
    // $file = '../responsive-sidebar-navigation-master/responsive-sidebar-navigation-master/index.html';
    $file = './sidebar/sidebar.html';

    if (file_exists($file)) {
        include($file);
    } else {
        echo "Error: No se encontró el archivo $file";
    }

    // Directorio de imágenes
    $dir = '../responsive-sidebar-navigation-master/responsive-sidebar-navigation-master/assets/images/';
    $images = glob($dir . "*.{jpg,png,gif,jpeg}", GLOB_BRACE);

    if ($images) {
        echo '<div class="image-gallery">';
        foreach ($images as $image) {
            echo '<img src="' . $image . '" alt="' . basename($image) . '">';
        }
        echo '</div>';
    } else {
        echo 'No se encontraron imágenes en el directorio.';
    }
    ?>
    <!-- Incluir JS -->
    <script src="../responsive-sidebar-navigation-master/assets/js/script.js"></script>

</body>
</html>
