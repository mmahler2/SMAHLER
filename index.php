<?php

require_once('config.php');

function redirect($uri) {
	$uri_hash = str_replace('/'.SUB_URI,'',$uri);
	$cn = new mysqli(DB_HOST, DB_UN, DB_PW, DB_NAME);
	if ($cn->connect_error)
		die("Connection failed: " . $cn->connect_error);
	$q = "select redirect,custom from ".DB_TABLE." where hash = '$uri_hash'" ;
	$res = $cn->query($q);
	while($row = $res->fetch_assoc()) {
		if ($row['custom'] == 1) {
			$sql = "UPDATE ".DB_TABLE." SET hits=hits+1 WHERE hash = '$uri_hash'";
			$cn->query($sql);
		}
		header("Location: ".$row['redirect']);
	}	
}



$uri = $_SERVER['REQUEST_URI'];
if ($uri == "/".SUB_URI) {
	include('body.php');
}
else {
	redirect($uri);
}

?>