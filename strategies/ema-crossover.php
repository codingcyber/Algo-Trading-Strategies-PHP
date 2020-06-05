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
// checking bullish & bearish candles - here in case of ema crossover, we don't need to check the bullish & bearish candles
foreach ($stockvals as $stockval) {
  // BUY signal
  // check with previous candle, if there is a previous candle
  if(!empty($previousval)){
    // for buy signal, previous candle's 8 ema value should be less than 15 ema value
    // for buy signal, current candle's 8 ema value should be greater than 15 ema value
    if(($previousval['8ema'] < $previousval['15ema']) && ($stockval['8ema'] > $stockval['15ema'])){
      echo "<p style='color:green;'>";
      echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 1<br>";
      echo "</p>";
    }
  }else{
    // if no previous candle, only check the current candle
    if($stockval['8ema'] > $stockval['15ema']){
      echo "<p style='color:green;'>";
      echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 2<br>";
      echo "</p>";
    }
  }

  // SELL signal
  if(!empty($previousval)){
  // for sell signal, previous candle's 8 ema value should be greater than 15 ema value
  // for sell signal, 15 ema value should be greater than 8 ema (or 8 ema value should be less than 15 ema)
    if(($previousval['8ema'] > $previousval['15ema']) && ($stockval['8ema'] < $stockval['15ema'])){
      echo "<p style='color:red;'>";
      echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 3<br>";
      echo "</p>";
    }
  }else{
    // if no previous candle, only check the current candle
    if($stockval['8ema'] < $stockval['15ema']){
      echo "<p style='color:red;'>";
      echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 4<br>";
      echo "</p>";
    }
  }
  $previousval = $stockval;

} // foreach loop end




?>