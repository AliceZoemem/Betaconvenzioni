<?php
	$cookie_name= "auth_betaconvenzioni";
	if(isset($_COOKIE[$cookie_name])) 			 				
		header("Location: homepage.php");
?>
<html>
    <head>
        <title>Betacom</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/css-stars.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
		<script src="js/jquery.barrating.js"></script>
		<script src="js/examples.js"></script>		
		<script src="js/jquery-3.3.1.min.js"></script> 
		<script src="js/bootstrap.min.js"></script> 
		<script src="https://maps.googleapis.com/maps/api/js?libraries=places&language=it&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>		
		<style>
			.wrong-form-control{
				border:1px solid #f00;
			}
		</style>
    </head>
    <body>
		<h2>Registrati</h2>
        <form method="post">   
            <input type="text" class="form-control" id="in_nome" name="nome" placeholder="Nome" ></br>
            <input type="text" class="form-control" id="in_cognome" name="cognome" placeholder="Cognome" ></br>
            <input type="text" class="form-control" id="in_email" name="email" placeholder="Email" ></br>
            <input type="password" class="form-control" id="in_password" name="password" placeholder="Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" ></br>
            <input type="text" class="form-control" id="in_indirizzo" name="indirizzo" placeholder="Via, numero civico e città" ><br>
            <br><input type="submit" class="btn btn-primary" name="submit" value="Registrati">
        </form>
		<br>
		<a href="/login.php" >Accedi</a>
	</body>
</html>
<?php
	require_once('functions/functions.php');
	if (isset($_POST['submit'] )){
		
		if(!isset($_POST['email'])) {
			echo "
			<script>$('#in_email').addClass('wrong-form-control');
				var flashInterval = setInterval(function() {
					$('#in_email').removeClass('wrong-form-control');
				}, 500);
				return;
			</script>";
			exit;
		}
		
		$email = $_POST['email'];
		
		if(!isset($_POST['cognome']))
		{
			echo "<script>
			$('#in_cognome').addClass('wrong-form-control');
			var flashInterval = setInterval(function() {
				$('#in_cognome').removeClass('wrong-form-control');
			}, 500);
			return;
			</script>";
			exit;
		}
		
		$cognome=$_POST['cognome'];
		
		if(!isset($_POST['nome']))
		{
			echo "<script>
			$('#in_nome').addClass('wrong-form-control');
			var flashInterval = setInterval(function() {
				$('#in_nome').removeClass('wrong-form-control');
			}, 500);
			return;
			</script>";
			exit;
		}
		
		$nome=$_POST['nome'];
		
		if(!isset($_POST['password']))
		{
			echo "<script>
			$('#in_password').addClass('wrong-form-control');
			var flashInterval = setInterval(function() {
				$('#in_password').removeClass('wrong-form-control');
			}, 500);
			return;
			</script>";
			exit;
		}
		
		$password=$_POST['password'];
		$new_pw = md5($password);
		
		if(!isset($_POST['indirizzo']))
		{
			echo "<script>
			$('#in_indirizzo').addClass('wrong-form-control');
			var flashInterval = setInterval(function() {
				$('#in_indirizzo').removeClass('wrong-form-control');
			}, 500);
			return;
			</script>";
			exit;
		}

		$indirizzo=$_POST['indirizzo'];
		$lat_log = GetCoordinates($indirizzo);
		if($lat_log == false){
			$lat = 0;
			$lng = 0;
		}
		else{
			$array_coordinate = explode('|', $lat_log);
			$lat = $array_coordinate[0];
			$lng = $array_coordinate[1];			
		}
		$sql_control="SELECT * FROM tbl_utenti WHERE Email = '". $email ."'";
		$conn = InstauraConnessione();
		$result_control = $conn->query($sql_control);
		
		if ($result_control->num_rows <= 0){
			AbbattiConnessione($conn);
			$conn = InstauraConnessione();
			
			$sql_insert = "INSERT INTO tbl_utenti (Cognome, Nome, Email, Password, Lat, Lng, IsAmminstratore, Attivo) VALUES ('$cognome', '$nome', '$email', '$new_pw', $lat, $lng, 0 , 1)";
			$conn->query($sql_insert);
			
			$sql = "SELECT * FROM tbl_utenti WHERE password = '" . $new_pw ."' AND email = '" . $email ."' AND attivo = '1'" ;
			$result = $conn->query($sql);
				
			if ($result->num_rows > 0) {
				$row = mysqli_fetch_row($result);
				
				$cookie_value = Encryption($row[0], 'e');
				$cookie_name = 'auth_betaconvenzioni';
				
				setcookie ($cookie_name, $cookie_value, time() + (86400 * 30), '/');
				Abbatticonnessione($conn);
				// header("Location: homepage.php");
			}
		}
		else{
			echo "<script>
				$('#in_email').addClass('wrong-form-control');
				var flashInterval = setInterval(function() {
					$('#in_email').removeClass('wrong-form-control');
				}, 500);
				return;
			</script>";
			AbbattiConnessione($conn);
		}
	}
?>

	