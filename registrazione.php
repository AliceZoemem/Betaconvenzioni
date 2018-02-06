<?php
	$cookie_name= "auth_betaconvenzioni";
	if(isset($_COOKIE[$cookie_name])) 			 				
		header("Location: homepage.php");
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Betacom</title>
    </head>
    <body>
		<h2>Form di Registrazione</h2>
        <form action="registrazione.php" method="post">   
            <input type="text" name="nome" placeholder="Nome" required></br>
            <input type="text" name="cognome" placeholder="Cognome" required></br>
            <input type="text" name="email" placeholder="Email" required></br>
            <input type="password" name="password" placeholder="Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required></br>
            <input type="text" name="indirizzo" placeholder="Via, numero civico e città" required><br>
            <br><input type="submit" name="submit" value="Registrati">
        </form>
		<br>
		<a href="/login.php" >Accedi</a>
	</body>
</html>

<?php
	require_once('functions/functions.php');

	$servername = "localhost";
	$db_username = "root";
	$db_pw = "";
	$db_name = "db_betaconvenzioni";
    $conn = new mysqli($servername, $db_username, $db_pw, $db_name);

	if ( isset($_POST['submit'] )){
		$nome=$_POST['nome'];
		$cognome=$_POST['cognome'];
		$email=$_POST['email'];
		$password=$_POST['password'];
		$new_pw = md5($password);
		$indirizzo=$_POST['indirizzo'];
		$lat_log = GetCoordinates($indirizzo);
		if($lat_log == false){
			echo 'Indirizzo non trovato';
			exit;
		}else{
			$array_coordinate = explode('|', $lat_log);
			// print_r ($array_coordinate);
			$sql_control="SELECT * FROM tbl_utenti WHERE Email = '". $email ."'";
			$result_control = $conn->query($sql_control);
			if ($result_control->num_rows <= 0){
				echo "<script>alert(".$array_coordinate[0].")</script>";
				echo "<script>console.log(".$array_coordinate[0].")</script>";
				$sql_insert = "INSERT INTO tbl_utenti (Cognome, Nome, Email, Password, Lat, Lng, IsAmminstratore, Attivo)VALUES('".$cognome."', '".$nome."', '".$email."', '".$new_pw."', ".$array_coordinate[0].", ".$array_coordinate[1].", 0 , 1)";
				
				$conn->query($sql_insert);
				echo "<script>alert('Registrazione completata')</script>";
				$sql = "SELECT * FROM tbl_utenti WHERE password = '" . $new_pw ."' AND email = '" . $email ."' AND attivo = '1'" ;
				$result = $conn->query($sql);
					
				if ($result->num_rows > 0) {
					$row = mysqli_fetch_row($result);
					
					$cookie_value = Encryption($row[0], 'e');
					$cookie_name = 'auth_betaconvenzioni';
					
					setcookie ($cookie_name, $cookie_value, time() + (86400 * 30), '/');
					header("Location: homepage.php");
				}
			}else{
				echo 'Email già in uso.';
				exit;
			}
		}
		
	}
	