<?php

function redirect($uri) {
	$uri_hash = str_replace('/smahler/','',$uri);
	$cn = new mysqli("localhost", "m2", "Maximus1", "m2");
	if ($cn->connect_error)
		die("Connection failed: " . $cn->connect_error);
	$q = "select redirect,custom from smahler where hash = '$uri_hash'" ;
	$res = $cn->query($q);
	while($row = $res->fetch_assoc()) {
		if ($row['custom'] == 1) {
			$sql = "UPDATE smahler SET hits=hits+1 WHERE hash = '$uri_hash'";
			$cn->query($sql);
		}
		header("Location: ".$row['redirect']);
	}	
}



$uri = $_SERVER['REQUEST_URI'];
if ($uri == "/smahler/") {
	include('body.php');
}
else {
	redirect($uri);
}

?>