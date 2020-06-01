<?php
require_once('includes/connect.php');

$sql = "DELETE FROM watchlist_stocks WHERE stockid=:stockid AND watchlistid=:watchlistid";
$result = $db->prepare($sql);
$values = array(':stockid'      => $_GET['stockid'],
                ':watchlistid'  => $_GET['watchlistid']
                );
$res = $result->execute($values) or die(print_r($result->errorInfo(), true));
if($res){
    header("location: watchlist.php?id={$_GET['watchlistid']}");
}else{
    header("location: watchlist.php?id={$_GET['watchlistid']}");
}