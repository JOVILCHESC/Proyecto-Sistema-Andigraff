<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h2>Listado de Ventas</h2>
    <table id="ventasTable" class="display">
        <thead>
            <tr>
                <th>Cod Venta</th>
                <th>Numero Factura</th>
                <th>RUT Cliente</th>
                <th>RUT Trabajador</th>
                <th>Total Venta</th>
                <th>Hora Venta</th>
                <th>Sub Total</th>
                <th>IVA Venta</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se llenarán automáticamente con DataTables -->
        </tbody>
    </table>

    <button id="reporteButton">Generar Reporte</button>

    <script>
        $(document).ready(function() {
            $('#ventasTable').DataTable({
                "ajax": "./obtener_ventas.php",
                "columns": [
                    { "data": "cod_venta" },
                    { "data": "numero_factura" },
                    { "data": "rut" },
                    { "data": "tra_rut" },
                    { "data": "total_venta" },
                    { "data": "hora_venta" },
                    { "data": "sub_total" },
                    { "data": "iva_venta" }
                ]
            });

            $('#reporteButton').click(function() {
                window.location.href = './reporte_ventas.php';
            });
        });
    </script>
</body>
</html>
