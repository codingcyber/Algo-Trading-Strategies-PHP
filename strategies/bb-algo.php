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

foreach ($stockvals as $stockval) {
	// BUY Signal
	// Generate BUY signal, when close price is above middle bollinger band (price close greater than bbm)
	if($stockval['price_close'] > $stockval['bbm']){
		echo "<p style='color:green;'>";
        echo "BUY " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 1<br>";
        echo "</p>";
	}

	// SELL Signal
	// Generate SELL signal, when close price is below middle bollinger band (price close less than bbm)

	if($stockval['price_close'] < $stockval['bbm']){
		echo "<p style='color:red;'>";
        echo "SELL " . $stockval['trade_date'] . " - " . $stockval['price_close'] . " - 2<br>";
        echo "</p>";
	}
}