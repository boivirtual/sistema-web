<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Conta', 'Valor'],
          ['Pessoa',     2000],
          ['Nutrição',      2300],
          ['Medicamentos',  300],
          ['Vacinas', 900],
          ['Pastagem',    120]
        ]);

        var options = {
          title: '% Gastos por Conta',

            legend: {
                position: 'bottom'
            },
            fontName: 'Futura Std Light',
            fontSize: 12,
            chartArea: {
                left: 100,
                top: 20,
                width: "70%",
                height: "auto"
            },

        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
  
    <script type="text/javascript">

    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawBasic);

    function drawBasic() {

          var data = google.visualization.arrayToDataTable([
            ['Conta', 'Valor',],
            ['Pastagens', 120],
            ['Vacinas', 900],
            ['Medicamentos', 300],
            ['Nutrição', 2300],
            ['Pessoas', 2000],
          ]);

          var options = {
            title: 'Custo/Cabeça',
            chartArea: {width: '50%'},
            hAxis: {
              //title: 'Total Population',
              minValue: 0
            },
            vAxis: {
              //title: 'Contas'
            },

            legend: {
                position: 'bottom'
            },
            fontName: 'Futura Std Light',
            fontSize: 12,
            chartArea: {
                left: 100,
                top: 20,
                width: "40%",
                height: "auto"
            },

        };

          var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

          chart.draw(data, options);
        }
    </script>
  </head>
  <body>
    <div id="piechart" style="width: 900px; height: 500px;"></div>

    <div id="chart_div" style="width: 70%; height: 70%"></div>
  </body>
</html>
