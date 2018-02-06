<?php
	$cookie_name = 'auth_betaconvenzioni';
	if(isset($_COOKIE[$cookie_name])){
		$id_convenzione = $_GET['convenzione'];
		$servername = "localhost";
		$db_username = "root";
		$db_pw = "";
		$db_name = "db_betaconvenzioni";
		$conn = new mysqli($servername, $db_username, $db_pw, $db_name);
		require_once('functions/functions.php');
	}else{
		header("Location: login.php");
	}
	
?>	
<!DOCTYPE HTML>  
<html lang="it">
	<head>
		<title>Betacom_Convenzione</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/css-stars.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
		<script src="js/jquery.barrating.js"></script>
		<script src="js/examples.js"></script>	
		<link rel="stylesheet" type="text/css" href="css/stile.css">		
		
		
		<style> 
			.carousel-item{
				width:100%;
				height:400px;
				background-position-x:center;
				background-position-y:center;
				background-repeat:no-repeat;
				background-size:cover;
			}
			.right{
				float:right;
				margin: 1% 7%;
			}
		</style>
		
		
		
		
	</head>
	<body>
		<button type="button" class="right" onclick="window.location.href='/logout.php'">Logout</button>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6" id="contenuto_img">
					<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
						<ol class="carousel-indicators">
							<?php
								$sql_immagini = "SELECT * FROM tbl_immagini WHERE IdConvenzione = " . $id_convenzione;
								$result_immagini = $conn->query($sql_immagini);

								foreach ($result_immagini as $key => $item)
								{
									if($key != 0)
										echo "<li data-target='#carouselExampleIndicators' data-slide-to='" .$key. "'></li>";	
									else
										echo "<li data-target='#carouselExampleIndicators' data-slide-to='" .$key. "' class='active'></li>";	
								}								
							?>
						</ol>
						<div class="carousel-inner">
							<?php
								foreach ($result_immagini as $key => $item)
								{
									if($key == 0)
										echo "<div class='carousel-item active' style='background-image:url(img/convenzioni/".$item['NomeFile'].")'></div>";
									else
										echo "<div class='carousel-item' style='background-image:url(img/convenzioni/".$item['NomeFile'].")'></div>";									
								}								
							?>						
							
						</div>
						<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
						</a>
						<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
						</a>
					</div>
				</div>
				<div class="col-sm-6" id="contenuto_testo">
					<h2 id="title_convenction" >Convenzione 1</h2>
					<div>
						<p id="descrizione">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
					</div>
					<form action = "" method = "post">
						<div class="stars stars-example-css">
							<select id="example-css" name="rating" autocomplete="off">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
							</select> 
							<input type="submit" name="feedback" value="VOTA">							
						</div>  
					</form>
					<?php
						$id_utente = $_COOKIE['auth_betaconvenzioni'];
						$id_utente = Encryption($id_utente, 'd');							
						$sql_control_insert = "SELECT * FROM tbl_feedback WHERE IdUtente = " . $id_utente ." AND IdConvenzione = ". $id_convenzione;
						$result_control_insert = $conn->query($sql_control_insert);
						if ($result_control_insert->num_rows <= 0)
							$already_rating = 0;
						else{
							echo "<script>
								$(document).ready(function () {
								$('select').barrating('clear');
								$('select').barrating('set', ".mysqli_fetch_row($result_control_insert)[2].");
							});
							</script>";	
							$already_rating = 1;
						}
						if(isset ($_POST['feedback'])){														
							if ($already_rating == 0) {								
								$option = isset ($_POST['rating']) ? $_POST['rating'] : "";
								$voto = intval($option);
								$sql_insert = "INSERT INTO tbl_feedback (IdUtente, IdConvenzione, Voto)VALUES(" . $id_utente.",".$id_convenzione.",".$voto.")";
								// mysqli_query($mysqli, $php);
								$conn->query($sql_insert);
								echo "<script>
									$(document).ready(function () {
									$('select').barrating('clear');
									$('select').barrating('set', ".$voto.");
								});
								</script>";									
							}else
								echo 'Convenzione già votata';
							
						}
					?>
				</div>
			</div>
			<div class="row">
				<div class="col-4 col-md-auto" id="contenuto_allegati">
					<ul id="elenco_allegati">
						<?php
							$sql_allegati = "SELECT * FROM tbl_allegati WHERE IdConvenzione = " . $id_convenzione;
							$result_allegati = $conn->query($sql_allegati);

							foreach ($result_allegati as $key => $item)
							{
								echo "<li><a href='img/allegati/". $item['NomeFile'] ."'>". $item['NomeFile'] ."</a></li>";								
							}
							
						?>

						
					</ul>
				</div>
			</div>
		</div>	

	
	
	<script>
		$(document).ready(function () {
			$('.carousel').carousel();
			$($('form')[0]).attr('action', window.location.href);
		});
	</script>
	
	
		<?php	
			$sql = "SELECT * FROM tbl_convenzioni WHERE IdConvenzione = " . $id_convenzione;
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				foreach ($result as $row) {
					$toRender = $row['Descrizione'];
					$toRender = str_replace("'", "\'", $toRender);
					$toRender = preg_replace( "/\r|\n/", "<br/>", $toRender);
					
					echo 
					"<script>
						document.getElementById('descrizione').innerHTML = '" . $toRender ."';
						document.getElementById('title_convenction').innerHTML = '" . $row ['Titolo']."';
					</script>";
				}
			}
		?>
	
	
	</body>
	
	
</html>
