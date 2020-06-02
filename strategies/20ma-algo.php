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
// TO-DO : We should return only first buy signal & first sell signal
foreach ($stockvals as $stockval) {
  // BUY signal
  // check the price_close with 20 sma value
  // if the price_close is above 20 sma line(price_close greater than 20 sma value), it's buy signal
  if($stockval['price_close'] > $stockval['20sma']){
    echo "<p style='color:green;'>";
    echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . "<br>";
    echo "</p>";
  }
  // SELL signal
  // if the price_close is below 20 sma line(price_close less than 20 sma value), it's sell signal
  if($stockval['price_close'] < $stockval['20sma']){
    echo "<p style='color:red;'>";
    echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . "<br>";
    echo "</p>";
  }
}




?>