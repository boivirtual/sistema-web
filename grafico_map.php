<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>Exemplo de gráfico</title>

        <!-- Carregar a API do google -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- Preparar a geracao do grafico -->
        <script type="text/javascript">
            var objarr = [
                {
                    "Fazenda": "Fazenda Casa Blanca",
                    "qtd_animais": 389,
                },
                {
                    "Fazenda": "Fazenda Pedra Bonita",
                    "qtd_animais": 736,
                },
                {
                    "Fazenda": "Fazenda Santa Helena",
                    "qtd_animais": 505,
                },
                {
                    "Fazenda": "Fazenda Nova Casa Blanca",
                    "qtd_animais": 250,
                },
                {
                    "Fazenda": "Fazenda Boa Vista",
                    "qtd_animais": 1105,
                },
                {
                    "Fazenda": "Fazenda Mantiqueira",
                    "qtd_animais": 103,
                },
                {
                    "Fazenda": "Fazenda São Tiago",
                    "qtd_animais": 258,
                },
                {
                    "Fazenda": "Fazenda Trincheira",
                    "qtd_animais": 32,
                }
            ]

            var arrArr = [
                ['Mapa de Gado','parent', 'Animais'],
                ['Mapa de Gado', '', ''],
            ]

            var n = objarr.length;

            for ( i = 0; i < n; i++){
                arrArr[i+2] = [objarr[i].Fazenda, 'Mapa de Gado', objarr[i].qtd_animais]
            }

            google.charts.load('current', 
                {packages: ['treemap']}).then(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(arrArr);
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
                    generateTooltip: showFullTooltip
                };

                var tree = new google.visualization.TreeMap(document.getElementById('mapa_gado'));
                tree.draw(data, options);

                function showFullTooltip(row, size, value) {
                    return '<div style="z-index:1000000; background:#9daab6; padding:10px;">'   + '<span><b>' + data.getValue(row, 0) + '</b>'+ '</span>' + '<br>'   + 'Total de ' + data.getColumnLabel(2) + ': ' + size + '<br>' 
                   + '</div>';

                }
            }  
        </script>
  </head>

  <body>
    <div id="mapa_gado" style="width: 50%"></div>
  </body>

</html>