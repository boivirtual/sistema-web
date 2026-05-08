<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawStuff);

google.charts.load('current', {
  packages: ['controls', 'corechart', 'table']
}).then(function () {
  var data = new google.visualization.DataTable();
  data.addColumn('number', 'X');
  data.addColumn('number', 'y0');
  data.addRows([
    [0, 0],   [1, 10],  [2, 23],  [3, 17],  [4, 18],  [5, 9],
    [6, 11],  [7, 27],  [8, 33],  [9, 40],  [10, 32], [11, 35],
    [12, 30], [13, 40], [14, 42], [15, 47], [16, 44], [17, 48],
    [18, 52], [19, 54], [20, 42], [21, 55], [22, 56], [23, 57],
    [24, 60], [25, 50], [26, 52], [27, 51], [28, 49], [29, 53],
    [30, 55], [31, 60], [32, 61], [33, 59], [34, 62], [35, 65],
    [36, 62], [37, 58], [38, 55], [39, 61], [40, 64], [41, 65],
    [42, 63], [43, 66], [44, 67], [45, 69], [46, 69], [47, 70],
    [48, 72], [49, 68], [50, 66], [51, 65], [52, 67], [53, 70],
    [54, 71], [55, 72], [56, 73], [57, 75], [58, 70], [59, 68],
    [60, 64], [61, 60], [62, 65], [63, 67], [64, 68], [65, 69]
  ]);

  var view = new google.visualization.DataView(data);
  view.setColumns([0, 1, {
    calc: function (dt, row) {
      var columnStyle = '';
      if (row % 2 === 0) {
        columnStyle = 'fill-color: #f44336; stroke-color: #d50000; stroke-opacity: 0.35; stroke-width: 5;';
      }
      return columnStyle;
    },
    role: 'style',
    type: 'string'
  }]);

  var container = document.getElementById('chart_div');
  var chartColumn = new google.visualization.ColumnChart(container);
  var options = {
    chartArea: {
      bottom: 24,
      left: 36,
      right: 16,
      top: 24,
      width: '100%',
      height: '100%'
    },
    colors: ['#f44336'],
    height: '100%',
    legend: {
      position: 'none'
    },
    width: '100%'
  };

  window.addEventListener('resize', function () {
    chartColumn.draw(view, options);
  });
  chartColumn.draw(view, options);
});    
</script>
  </head>
  <body>
    <div id="chart_div" style="width: 800px; height: 500px;"></div>
  </body>
</html>
