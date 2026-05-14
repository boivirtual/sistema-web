<!DOCTYPE html>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
        var data = google.visualization.arrayToDataTable([
                ['Categoria', 'Macho', 'Fêmea', '%'],
                ['00 a 07 meses', 215, 233, 23.69],
                ['08 a 12 meses', 17, 24, 2.22],
                ['13 a 24 meses', 298, 300, 31.62],
                ['25 a 36 meses', 13, 267, 14.7],
                ['> 36 meses', 2, 522, 27.71]
              ]);

        var options = {
            //title : 'Monthly Coffee Production by Country',
            seriesType: 'bars',
            series: {2: 
                    {type: 'line', targetAxisIndex: 1, color: '#ed7c31'}
            },

            vAxis: {
              minValue: 0,
              maxValue: 1,
              gridlines: { count: 1 },
              minorGridlines: { count: 0 },
            },

            vAxes:  {
                  1: {format: '##,##%'}
                },

            hAxis: {
                //slantedText: true,
            },


            backgroundColor: 'transparent',
            tooltip: { isHtml: true, trigger: 'visible' },
            colors:['#2f5597', '#dae3f3'],
            isStacked: true,
            legend: { position: 'bottom'},
            fontName: 'Futura Std Light',
            fontSize: 12,
            chartArea:{left:50, top:20,width:"100%",height:"70%"},
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    };

    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 800px; height: 600px;"></div>
  </body>
</html>

