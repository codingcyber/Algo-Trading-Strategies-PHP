<?php
require_once('includes/connect.php');

$sql = "DELETE FROM watchlist_stocks WHERE watchlistid=?";
$result = $db->prepare($sql);
$res = $result->execute(array($_GET['id'])) or die(print_r($result->errorInfo(), true));
if($res){
	$sql = "DELETE FROM watchlist WHERE id=?";
	$result = $db->prepare($sql);
	$res = $result->execute(array($_GET['id'])) or die(print_r($result->errorInfo(), true));
    header("location: watchlist.php?id={$_GET['watchlistid']}");
}else{
    header("location: watchlist.php?id={$_GET['watchlistid']}");
}