<?php

require_once('config.php');



$cn = new mysqli(DB_HOST, DB_UN, DB_PW, DB_NAME);
if ($cn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}



function addNew($hash, $url, $isCustom) {
	global $cn;
	
	$q = "INSERT INTO  `".DB_TABLE."` (`hash`,`redirect`,`custom`,`hits`) VALUES ('$hash','$url','$isCustom',0)";
	if ($cn->query($q)) {
		echo("<h3>Your new <span class='tiny'>(smahl)</span> URL is: <a href='".SUB_URI."$hash'>".SUB_URI."$hash</a></h3>");
	}
	else {
		echo("FAILURE");
	}
	
}

function reHash($hash, $url){
	global $cn;
	
	$hash = substr(hash('adler32', $hash, false), 0, 5); 
	$q = "select * from ".DB_TABLE." where hash = '$hash'" ;
	$res = $cn->query($q);
	if ($res->num_rows > 0){
		rehash($hash, $url);
	}
	else
		addNew($hash, $url);
}


function cleanInput($input) {
	$search = array(
	  '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
	  '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
	  '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
	  '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	);
 
	$output = preg_replace($search, '', $input);
	return $output;
}


function go() {
	
	global $cn;
	$checkURL = "";
	$check = 1;
	$url = cleanInput($_POST['url']);
	$custom = cleanInput($_POST['custom']);
	$isCustom = (strlen($custom) > 0) ? 1 : 0;
	
	
	//not tiny enough.
	if (strlen($custom) >= 15) {
		echo('<h3>Custom URL too long! (Max 14 chars)</h3>');
		die();
	}
	

	//is custom or not?
	if ($isCustom)
		$hash = $custom;
	else
		$hash = substr(hash('adler32', $url, false), 2, 4); 
	
	
	//if the hash has been used, return the url it belongs to
	$q = "select redirect from ".DB_TABLE." where hash = '$hash'" ;
	$res = $cn->query($q);
	while($row = $res->fetch_assoc()) {
		$checkURL = $row['redirect'];
	}
	
	
	//url was already used, but wasn't custom
	if (($checkURL == $url) && ($url !== "") && $check == 1 && $isCustom == 0) {
		$check = 0;
		echo("<h3>Your new <span class='tiny'>(smahl)</span> URL is: <a class='tinyurl' href='".SUB_URI."$hash'>".SUB_URI."$hash</a></h3>");
		die();
	}
	
	//custom url already used, but it was the the same redirect
	if (($checkURL == $url) && ($url !== "") && $check == 1 && $isCustom == 1) {
		$check = 0;
		echo("<h3>Your new <span class='tiny'>(smahl)</span> URL is: <a class='tinyurl' href='".SUB_URI."$hash'>".SUB_URI."$hash</a></h3>");
		die();
	}
	
	//custom url already used, and it wasn't the same redirect
	if (($checkURL != $url) && ($res->num_rows > 0) && ($url !== "") && $check == 1 && $isCustom == 1) {
		$check = 0;
		echo("<h3>Custom URL already used :(</h3>");
		die();
	}
	
	
	
	
	
	if($res->num_rows > 0 && $check == 1 && $isCustom == 0){
		reHash($hash, $url);
	}
	else if (($url !== "") && ($check == 1)){
		addNew($hash, $url, $isCustom );
	}
	else if ($check == 1){
		echo("<h3 class='error'>You Missed Something....&uarr;</h3>");
		die();
	}
	
}





go();



?>