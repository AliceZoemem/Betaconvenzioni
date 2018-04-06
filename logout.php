<?php
	if (isset($_COOKIE['auth_betaconvenzioni'])) {
		unset($_COOKIE['auth_betaconvenzioni']);
		setcookie('auth_betaconvenzioni', '', time() - 3600, '/'); // empty value and old timestamp
	}

	header("Location: login.php");
	
?>