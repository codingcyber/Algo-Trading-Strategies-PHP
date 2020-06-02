<?php
require_once('../includes/connect.php');
// get all the daily values from tabled based on days 
$sql = "SELECT * FROM stock_daily_values sdv JOIN stocks s ON sdv.stockid=s.id WHERE s.symbol=? ORDER BY trade_date ";
if(isset($_GET['days']) & !empty($_GET['days'])){ $sql .= " DESC LIMIT {$_GET['days']}"; }else{ $sql .= " ASC";}
$result = $db->prepare($sql);
$res = $result->execute(array($_GET['scrip'])) or die(print_r($result->errorInfo(), true));
$stockvals = $result->fetchAll(PDO::FETCH_ASSOC);
// use array_reverst while $_GET['days'] is set
if(isset($_GET['days']) & !empty($_GET['days'])){
  $stockvals = array_reverse($stockvals);
}

// echo "<pre>";
// print_r($stockvals);
// echo "</pre>";
// TO DO : We should return only first buy & sell signal
foreach ($stockvals as $stockval) {
  // BUY signal
  // for buy signal, 8 ema value should be greater than 15 ema value
  if($stockval['8ema'] > $stockval['15ema']){
    echo "<p style='color:green;'>";
    echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 2<br>";
    echo "</p>";
  }

  // SELL signal
  // for sell signal, 15 ema value should be greater than 8 ema (or 8 ema value should be less than 15 ema)
  if($stockval['8ema'] < $stockval['15ema']){
    echo "<p style='color:red;'>";
    echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 2<br>";
    echo "</p>";
  }

} // foreach loop end




?>