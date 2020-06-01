<?php
require_once('includes/config.php');

$curl = curl_init();
$symbol = "INFY.NS";

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.worldtradingdata.com/api/v1/stock?symbol=$symbol&api_token=$token",
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
	//echo $response;
}

// convert the response to php array or object
$name = json_decode($response, true);
echo "<br><pre>";
print_r($array);
echo "</pre>";
//echo $array->data[0]->name;
echo $name['data'][0]['name'];







?>