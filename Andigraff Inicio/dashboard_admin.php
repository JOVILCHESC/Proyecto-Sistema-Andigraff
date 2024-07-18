<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
</head>
<body>
<?php
    $file = '../responsive-sidebar-navigation-master/responsive-sidebar-navigation-master/index.html';

    if (file_exists($file)) {
        include($file);
    } else {
        echo "Error: No se encontró el archivo $file";
    }
?>
</body>
</html>
