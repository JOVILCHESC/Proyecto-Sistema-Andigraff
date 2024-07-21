<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h2>Reporte de Ventas</h2>

    <!-- Formulario para seleccionar el tipo de reporte -->
    <form id="reportForm">
        <label for="reportType">Seleccionar reporte por:</label>
        <select id="reportType" name="reportType">
            <option value="year">Año</option>
            <option value="month">Mes</option>
        </select>

        <label for="year">Año:</label>
        <input type="number" id="year" name="year" min="2000" max="2100">

        <label for="month">Mes:</label>
        <select id="month" name="month">
            <option value="">--Seleccionar mes--</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>

        <button type="submit">Generar Reporte</button>
    </form>

    <table id="reporteVentasTable" class="display">
        <thead>
            <tr>
                <th>Año</th>
                <th>Nombre Producto</th>
                <th>Código Producto</th>
                <th>Total Vendido</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se llenarán automáticamente con DataTables -->
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            // Función para actualizar el DataTable
            function updateTable(reportType, year, month) {
                $('#reporteVentasTable').DataTable({
                    "ajax": {
                        "url": "./obtener_reporte.php",
                        "data": {
                            "reportType": reportType,
                            "year": year,
                            "month": month
                        },
                        "dataSrc": "data",
                        "error": function (xhr, error, thrown) {
                            console.error("Error al cargar los datos: ", error);
                            console.error("Detalles del error: ", thrown);
                        }
                    },
                    "columns": [
                        { "data": "año" },
                        { "data": "nombre_producto" },
                        { "data": "cod_producto" },
                        { "data": "total_vendido" }
                    ]
                });
            }

            // Inicializar el DataTable con parámetros vacíos
            updateTable('year', '', '');

            // Manejo del formulario
            $('#reportForm').on('submit', function(e) {
                e.preventDefault();
                const reportType = $('#reportType').val();
                const year = $('#year').val();
                const month = $('#month').val();
                
                // Reinitialize DataTable with new parameters
                $('#reporteVentasTable').DataTable().destroy();
                updateTable(reportType, year, month);
            });
        });
    </script>
</body>
</html>
