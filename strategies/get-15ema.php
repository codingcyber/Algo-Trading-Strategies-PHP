<?php
require_once('../includes/connect.php');
require_once('../includes/config.php');
//1. Get the list of stocks from stocks table
//2. Get the stock's values from API for current function (SMA)
//3. Check the values exists in stock_daily_values table with trade date
//4. If the record exists with trade date, insert these SMA function values in stock_daily_values table with update query

$sql = "SELECT * FROM stocks";
$result = $db->prepare($sql);
$result->execute() or die(print_r($result->errorInfo(), true));
$stocks = $result->fetchAll(PDO::FETCH_ASSOC);
foreach ($stocks as $stock) {
	$stockid = $stock['id'];
	$symbol = $stock['symbol'];
	$exchange = $stock['exchange'];
	$curlsymbol = $symbol.".".$exchange;

	$curl = curl_init();
	//https://www.alphavantage.co/query?function=EMA&symbol=$curlsymbo&interval=daily&time_period=8&series_type=close&apikey=$key
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.alphavantage.co/query?function=EMA&symbol=$curlsymbol&interval=daily&time_period=15&series_type=close&apikey=$key",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if($err){
        echo "cURL Error :" . $err;
    }else{
        echo $response;
        // After that loop through the daily values and insert those values in daily_values table
        // here we can get the weekly & monthly response and insert into respective tables, will do it in a seperate PHP page
        $data = json_decode($response, true);
        $dates = array_keys($data['Technical Analysis: EMA']);
        foreach ($dates as $date) {
        	// Check the values exists in stock_daily_values table with trade date
		    $sql = "SELECT * FROM stock_daily_values WHERE stockid=:stockid AND trade_date=:trade_date";
		    $result = $db->prepare($sql);
		    $values = array(':stockid'		=> $stock['id'],
		    				':trade_date'	=> $date
		    				);
		    $result->execute($values);
		    $count = $result->rowCount();
		    if($count == 1){
		    	// Update the Values into stock_daily_values table
		    	if(isset($data['Technical Analysis: EMA'][$date]) & !empty($data['Technical Analysis: EMA'][$date])){
		                //if($data['Time Series (Daily)'][$date]['1. open'] != '0.0000'){

		                    // Update into stock_daily_values table
		                    $smasql = "UPDATE stock_daily_values SET 15ema=:ema WHERE stockid=:stockid AND trade_date=:trade_date";
		                    $smaresult = $db->prepare($smasql);
		                    $values = array(':stockid'      => $stockid,
		                                    ':ema'      => $data['Technical Analysis: EMA'][$date]['EMA'],
		                                    ':trade_date'   => $date
		                                    );

		                    $smares = $smaresult->execute($values) or die(print_r($smaresult->errorInfo(), true));
		                    echo $date ." - " . $stock['symbol'] . " 15 EMA Added<br>";

		                //}
		            }
		        }
		    }
        //$messages[] = "Stock Added Successfully";
    }
} // FOR EACH LOOP END

?>