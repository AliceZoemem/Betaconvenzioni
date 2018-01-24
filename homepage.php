<!DOCTYPE HTML>  
<html>
	<head>
		<title>Betacom_Homepage</title>
	</head>
	<body> 
		<h2>Betacom Login</h2>
		<form method="post" action="login.php">  
			<input type="text" name="email" value="" placeholder="Email">			
			<br><br>
			<input type="password" name="password" value="" placeholder="Password">
			<br><br>
			<input type="submit" name="submit" value="Login">
		</form>
	</body>
</html>
<?php
	$servername = "localhost";
	$db_username = "root";
	$db_pw = "";
	$db_name = "db_betaconvenzioni";
	if ( isset( $_POST['submit'] )){
		$email = $_POST['email'];
		$pw = $_POST['password'];		
	
		$new_pw = md5($pw);
		
		echo $db_name;
		// $conn = new mysqli($servername, $db_username, $db_pw, $db_name);
		
		// $sql = "SELECT email, password FROM tbl_utenti WHERE password = '" . $new_pw ."' AND email = '" . $email ."' AND attivo = '1'" ;
		// $result = $conn->query($sql);

		// if ($result->num_rows > 0) {
			// echo "Login effettuato con successo";
		// }
	}
		 
?>