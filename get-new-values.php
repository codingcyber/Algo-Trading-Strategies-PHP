<?php
require_once('includes/connect.php');
require_once('includes/config.php');
//1. Get the list of stocks from stocks table
//2. Get the stock values from API with compact output
//3. Check the values exists in stock_daily_values table with trade date
//4. If the values doesn't exist with trade date, insert these values into stock_daily_values table

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

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$curlsymbol&apikey=$key&outputsize=compact",
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
        $dates = array_keys($data['Time Series (Daily)']);
        foreach ($dates as $date) {
        	// Check the values exists in stock_daily_values table with trade date
		    $sql = "SELECT * FROM stock_daily_values WHERE stockid=:stockid AND trade_date=:trade_date";
		    $result = $db->prepare($sql);
		    $values = array(':stockid'		=> $stock['id'],
		    				':trade_date'	=> $date
		    				);
		    $result->execute($values);
		    $count = $result->rowCount();
		    if($count == 0){
		    	// Insert the Values into stock_daily_values table
		    	if(isset($data['Time Series (Daily)'][$date]) & !empty($data['Time Series (Daily)'][$date])){
		                if($data['Time Series (Daily)'][$date]['1. open'] != '0.0000'){

		                    // Insert into stock_daily_values table
		                    $dailysql = "INSERT INTO stock_daily_values (stockid, price_open, price_high, price_low, price_close, volume, trade_date) VALUES (:stockid, :price_open, :price_high, :price_low, :price_close, :volume, :trade_date)";
		                    $dailyresult = $db->prepare($dailysql);
		                    $values = array(':stockid'      => $stockid,
		                                    ':price_open'   => $data['Time Series (Daily)'][$date]['1. open'],
		                                    ':price_high'   => $data['Time Series (Daily)'][$date]['2. high'],
		                                    ':price_low'    => $data['Time Series (Daily)'][$date]['3. low'],
		                                    ':price_close'  => $data['Time Series (Daily)'][$date]['4. close'],
		                                    ':volume'       => $data['Time Series (Daily)'][$date]['5. volume'],
		                                    ':trade_date'   => $date
		                                    );

		                    $dailyres = $dailyresult->execute($values) or die(print_r($dailyresult->errorInfo(), true));
		                    echo $date ." - " . $stock['symbol'] . " Added<br>";

		                }
		            }
		        }
		    }
        //$messages[] = "Stock Added Successfully";
    }
}

?>