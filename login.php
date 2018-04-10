<?php
	$cookie_name= "auth_betaconvenzioni";
	if(isset($_COOKIE[$cookie_name])) 			 				
		header("Location: homepage.php");
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>BetaConvenzioni — Login</title>
		<script src="js/jquery-3.3.1.min.js"></script> 
		<script src="js/bootstrap/bootstrap.min.js"></script> 
		<link rel="stylesheet" href="css/bootstrap/bootstrap.min.css" />

		<style>
			
			body{
				padding-top:70px;
			}
		
			.main-form{
				width:60%;
				margin-left:20%;
				text-align:center;
			}
			.main-form .form-control{
				margin-bottom:5px;
			}

			.main-form .logo{
				max-width:50%;
				max-height:250px;
				display:inline-block;
			}

			/* ~ ~ Responsiveness ~ ~ */
			@media all and (max-width: 600px) {
				.main-form{
					width:90%;
					margin-left:5%;
				}
			}
		</style>
		
	</head>
	<body> 
		<form method="post" action="login.php" class="main-form">  
			<img class="logo" src="img/logo.png" />
			<br/><br/><br/>
			<input type="text" name="email" class="form-control" placeholder="Email">			
			<input type="password" name="password" class="form-control" placeholder="Password">
			<input type="submit" name="submit" class="btn btn-primary" value="Login">
			<br/><br/>
			Non sei ancora registrato? <a href="registrazione.php" >Registrati</a>
		</form>

		<!-- Modal alert -->
		<div class="modal fade" id="ModalAlert" tabindex="-1" role="dialog" aria-labelledby="titleLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="titleLabel">Attenzione</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				Credenziali errate. Riprovare.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
			</div>
			</div>
		</div>
		</div>
	</body>
</html>
<?php
	require_once('functions/functions.php');
	$conn = InstauraConnessione();
	
	if ( isset($_POST['submit'] )){
		$email = $_POST['email'];
		$pw = $_POST['password'];		
	
		$new_pw = md5($pw);
		
		$sql = "SELECT * FROM tbl_utenti WHERE password = '" . $new_pw ."' AND email = '" . $email ."' AND attivo = '1'" ;
		$result = $conn->query($sql);
			
		if ($result->num_rows > 0) {			
			$row = mysqli_fetch_row($result);
			$cookie_value = Encryption($row[0], 'e');
			$cookie_name = 'auth_betaconvenzioni';
			
			setcookie ($cookie_name, $cookie_value, time() + (86400 * 30), '/');
			AbbattiConnessione($conn);
			header("Location: betaconvenzioni.php");
			
			//se il cookie e settato e torno a login reindirizza su homepage
		}else{
			echo "<script>$('#ModalAlert').modal('show');</script>";
			AbbattiConnessione($conn);
		}
	}
?>	