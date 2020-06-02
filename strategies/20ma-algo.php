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
// We should compare the previous value for getting only first buy or sell signal
// for buy signal, current candle should close above 20 sma (current candle price close greater than 20 sma) & previous candle should closed below 20 sma (previous candle price close less than 20 sma)
// for sell signal, current candle should close below 20 sma (current candle price close less than 20 sma) & previous candle should be closed above 20 sma (previous candle price close greater than 20 sma)
foreach ($stockvals as $stockval) {
  // BUY signal
  // check the price_close with 20 sma value
  // if the price_close is above 20 sma line(price_close greater than 20 sma value), it's buy signal
  // if previous value is not empty, we will check the current candle & previous candle. If the previous candle is empty, we will check only current candle
  // for BUY signal, we need only bullish candles (that is green candles)
  // for SELL signal, we need only bearish candles (that is red candle)
  if(!empty($previousval)){
    // how we can find the bullish & bearish candles?
    // for bullish candles open price is below the closing price, that forms the green candle (price close greater than price open) or (price open less than price close)
    // for bearish candles open price is above the closing price, that forms the red candle (price close less than price open) or (price open greater than price close)
    if(($previousval['price_close'] < $previousval['20sma']) && ($stockval['price_close'] > $stockval['20sma'])){
      if($stockval['price_close'] > $stockval['price_open']){
        echo "<p style='color:green;'>";
        echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 1<br>";
        echo "</p>";
      }
    }
  }else{
    if($stockval['price_close'] > $stockval['20sma']){
      if($stockval['price_close'] > $stockval['price_open']){
        echo "<p style='color:green;'>";
        echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 2<br>";
        echo "</p>";
      }
    }
  }
  // SELL signal
  // if the price_close is below 20 sma line(price_close less than 20 sma value), it's sell signal
  if(!empty($previousval)){
    if(($previousval['price_close'] > $previousval['20sma']) && ($stockval['price_close'] < $stockval['20sma'])){
      if($stockval['price_close'] < $stockval['price_open']){
        echo "<p style='color:red;'>";
        echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 3<br>";
        echo "</p>";
      }
    }
  }else{
    if($stockval['price_close'] < $stockval['20sma']){
      if($stockval['price_close'] < $stockval['price_open']){
        echo "<p style='color:red;'>";
        echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 4<br>";
        echo "</p>";
      }
    }
  }
  $previousval = $stockval;

} // foreach loop end




?>