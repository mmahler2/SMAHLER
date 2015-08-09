<?php

require_once('config.php');



function redirect($uri) {
	//clean up the URL a bit- removes trailing slashes
	$uri_hash = str_replace('/'.SUB_URI,'',$uri);
	
	//connect to the database
	$cn = new mysqli(DB_HOST, DB_UN, DB_PW, DB_NAME);
	if ($cn->connect_error)
		die("Connection failed: " . $cn->connect_error);
	
	//form our query
	$q = "select redirect,custom from ".DB_TABLE." where hash = '$uri_hash'" ;
	$res = $cn->query($q);
	while($row = $res->fetch_assoc()) {
		//extra bonus! analytics!
		if ($row['custom'] == 1) {
			$sql = "UPDATE ".DB_TABLE." SET hits=hits+1 WHERE hash = '$uri_hash'";
			$cn->query($sql);
		}
		//and they're out of here!
		header("Location: ".$row['redirect']);
	}	
}


//determine where we are.
$uri = $_SERVER['REQUEST_URI'];
if ($uri == "/".SUB_URI) {
	//if the current URL matches the install path, show them the body.
	include('body.php');
}
else {
	//otherwise, redirect them since that's what they want.
	redirect($uri);
}

?>