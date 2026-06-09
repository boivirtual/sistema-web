<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>Exemplo de gráfico</title>

        <!-- Carregar a API do google -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- Preparar a geracao do grafico -->
        <script type="text/javascript">
            google.charts.load('current', 
                {packages: ['treemap']}).then(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                   ['Mapa', 'Fazenda', 'Total', 'Score'],
                   ['Mapa de Gado', '', '', ''],
                   ['Fazenda Casa Blanca', 'Mapa de Gado', 56, ''],
                   ['Fazenda Pedra Bonita', 'Mapa de Gado', '', ''],
                   ['Fazenda Pedra Santa Helena', 'Mapa de Gado', '', ''],
                   ['Fazenda Boa Vista', 'Mapa de Gado', '', ''],
                   ['Pasto 1', 'Fazenda Casa Blanca', 150, 56],
                   ['Pasto 2', 'Fazenda Casa Blanca', 200, 56],
                   ['Pasto 3', 'Fazenda Casa Blanca', 39, 56],
                   ['Pasto 4', 'Fazenda Pedra Bonita', 300, 0.45],
                   ['Pasto 5', 'Fazenda Pedra Bonita', 150, 0.39],
                   ['Pasto 6', 'Fazenda Pedra Bonita', 160, 0.35],
                   ['Pasto 7', 'Fazenda Pedra Santa Helena', 856, 0.3],
                   ['Pasto 8', 'Fazenda Boa Vista', 184, 0.3],
                  ]);

                var options = {
                    headerColor: '#dfe4e9',
                    highlightOnMouseOver: false,
                    maxDepth: 1,
                    maxPostDepth: 4,
                    minColor: '#aede3c',
                    midColor: '#799b2a',
                    maxColor: '#455818',
                    headerHeight: 25,
                    showScale: true,
                    useWeightedAverageForAggregation: true,
                    showTooltips: true,
                    generateTooltip: showFullTooltip,
                };

                var tree = new google.visualization.TreeMap(document.getElementById('mapa_gado'));

                tree.draw(data, options);

                function showFullTooltip(row, size, value) {
                    return '<div style="z-index:1000000; background:#9daab6; padding:10px;">' +
                    '<span><b>' + data.getValue(row, 0) + '</b>, ' + data.getValue(row, 1) + 
                    ', ' + data.getValue(row, 2) + ', Local: ' + data.getValue(row, 3) + '</span>' + 
                    '<br>' +
                    'Datatable row: ' + row + 
                    '<br>' +
                    data.getColumnLabel(2) + ' Animais: ' + size + 
                    '<br>' +
                    data.getColumnLabel(3) + ': ' + value + 
                    ' </div>';
                }
            }  
        </script>
  </head>

  <body>
    <div id="mapa_gado" style="width: 50%"></div>
  </body>

</html>