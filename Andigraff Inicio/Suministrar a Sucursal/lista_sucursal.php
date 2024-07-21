<?php
// Conectar a PostgreSQL
$host = "146.83.165.21";
$port = "5432";
$dbname = "jvilches";
$user = "jvilches";
$password = "wEtbEQzH6v44";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error en la conexión a la base de datos");
}

// Consultar los suministros
$query = "SELECT s.suc_cod_establecimiento, s.cod_establecimiento, s.cantidad_suministrada, s.fecha_suministra, b.nombre_establecimiento as bodega, su.nombre_establecimiento as sucursal 
          FROM suministra s
          JOIN bodega b ON s.cod_establecimiento = b.cod_establecimiento
          JOIN sucursal su ON s.suc_cod_establecimiento = su.cod_establecimiento";

$result = pg_query($conn, $query);

if (!$result) {
    echo "Error en la consulta: " . pg_last_error($conn);
    exit();
}

$suministros = pg_fetch_all($result);

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Suministros</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions {
            text-align: center;
        }
        .actions a {
            color: black;
            margin: 0 5px;
            text-decoration: none;
            font-size: 16px;
            padding: 10px;
            background-color: #4CAF50;
            border-radius: 5px;
        }
        .actions a:hover {
            background-color: #45a049;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Lista de Suministros</h2>

    <?php if (!$suministros): ?>
        <p>No hay suministros registrados.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Cantidad Suministrada</th>
                    <th>Fecha de Suministro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suministros as $suministro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($suministro['sucursal']); ?></td>
                        <td><?php echo htmlspecialchars($suministro['bodega']); ?></td>
                        <td><?php echo htmlspecialchars($suministro['cantidad_suministrada']); ?></td>
                        <td><?php echo htmlspecialchars($suministro['fecha_suministra']); ?></td>
                        <td class="actions">
                            <a href="ver_suministro.php?suc_cod_establecimiento=<?php echo $suministro['suc_cod_establecimiento']; ?>&cod_establecimiento=<?php echo $suministro['cod_establecimiento']; ?>" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="editar_suministro.php?suc_cod_establecimiento=<?php echo $suministro['suc_cod_establecimiento']; ?>&cod_establecimiento=<?php echo $suministro['cod_establecimiento']; ?>" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="eliminar_suministro.php?suc_cod_establecimiento=<?php echo $suministro['suc_cod_establecimiento']; ?>&cod_establecimiento=<?php echo $suministro['cod_establecimiento']; ?>" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este suministro?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
