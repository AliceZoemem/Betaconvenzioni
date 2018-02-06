<?php
	$cookie_name= "auth_betaconvenzioni";
	if(isset($_COOKIE[$cookie_name])){
		$cookie_value = $_COOKIE['auth_betaconvenzioni'];
		setcookie ($cookie_name, cookie_value, time()-1);
		header("Location: login.php");
	}else
		header("Location: login.php");
		
?>