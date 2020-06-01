<?php
require_once('includes/connect.php');

if(isset($_GET['type']) & !empty($_GET['type'])){

  switch ($_GET['type']) {
    case 'weekly':
      $table = "stock_weekly_values";
      break;
    case 'monthly':
      $table = "stock_monthly_values";
      break;
    default:
      $table = "stock_daily_values";
      break;
  }
}else{
  $table = "stock_daily_values";
}

$sql = "SELECT * FROM $table sdv JOIN stocks s ON sdv.stockid=s.id WHERE s.symbol=? ORDER BY trade_date ";
if(isset($_GET['days']) & !empty($_GET['days'])){ $sql .= " DESC LIMIT {$_GET['days']}"; }else{ $sql .= " ASC";}
$result = $db->prepare($sql);
$res = $result->execute(array($_GET['scrip'])) or die(print_r($result->errorInfo(), true));
$stockvals = $result->fetchAll(PDO::FETCH_ASSOC);
// use array_reverst while $_GET['days'] is set
if(isset($_GET['days']) & !empty($_GET['days'])){
  $stockvals = array_reverse($stockvals);
}
?>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      <?php
        foreach ($stockvals as $stockval) {
      ?>
      // Date, Low, Open, Close, High
      ['<?php echo $stockval['trade_date']; ?>', <?php echo $stockval['price_low']; ?>, <?php echo $stockval['price_open']; ?>, <?php echo $stockval['price_close']; ?>, <?php echo $stockval['price_high']; ?>],
      <?php } ?>
      // Treat first row as data as well.
    ], true);

    var options = {
      legend:'none',
      candlestick: {
            fallingColor: { strokeWidth: 0, fill: '#a52714', stroke: '#a52714' }, // red
            risingColor: { strokeWidth: 0, fill: '#0f9d58', stroke: '#0f9d58' }   // green
          }
    };

    var chart = new google.visualization.CandlestickChart(document.getElementById('chart_div'));

    chart.draw(data, options);
  }
    </script>
  </head>
  <body>
    <h2><?php echo $_GET['scrip']; ?> - <?php if(isset($_GET['type']) & !empty($_GET['type'])){ echo $_GET['type']; }else{ echo "Daily";} ?><?php if(isset($_GET['days']) & !empty($_GET['days'])){ echo " - " .$_GET['days'] . " Days"; } ?> Candle Stick Chart</h2>
    <div id="chart_div" style="width: 100%; height: 550px;"></div>
    <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&days=30">30 Days</a> | <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&days=60">60 Days</a> | <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&days=90">90 Days</a> | <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&days=180">180 Days</a> | <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&days=360">360 Days</a> | <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&type=weekly">Weekly</a> | <a href="http://localhost/Stock-Market-Application/chart.php?scrip=<?php echo $_GET['scrip']; ?>&type=monthly">Monthly</a>
  </body>
</html>
