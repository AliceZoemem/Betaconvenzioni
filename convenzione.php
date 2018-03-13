<?php
	require_once('functions/functions.php');
	$cookie_name = 'auth_betaconvenzioni';
	if(isset($_COOKIE[$cookie_name])){
		if($_GET['convenzione'])
			$id_convenzione = $_GET['convenzione'];
		else
			header("Location: homepage.php");
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
		<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
		<script>tinymce.init({ selector:'textarea', height: '50vh' });</script>
		
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
		
			.pencil{
				visiblity: visible;
				display: inline;
				width: 3rem;
				position: absolute;
				right: 20%;
				top: 5%;
			}
			
			.mce-widget.mce-notification.mce-notification-warning.mce-has-close.mce-in{
				display:none;
			}
		</style>
		
	</head>
	<body>
		<button type="button" class="right" onclick="window.location.href='/logout.php'">Logout</button>
		<?php 
			$conn = InstauraConnessione();
			$id_utente = $_COOKIE['auth_betaconvenzioni'];
			$id_utente = Encryption($id_utente, 'd');
			$sql_isAdmin = "SELECT * FROM tbl_utenti WHERE IdUtente = " . $id_utente;
			$result_isAdmin = $conn->query($sql_isAdmin);
			$typeadmin =  mysqli_fetch_row($result_isAdmin);
			if($typeadmin[7] != 0)
				echo("<img class='pencil' src='/img/pencil.png' data-toggle='modal' data-target='#exampleModal'></img>");
			AbbattiConnessione($conn);	
			$conn = InstauraConnessione();
			$sql_text = "SELECT * FROM tbl_convenzioni WHERE Idconvenzione = " . $id_convenzione;
			$result_text = $conn->query($sql_text);
			$text=  mysqli_fetch_row($result_text);			
		?>
		
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form class="modal-content" action = "" method = "post">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><?php echo $text[1];?></h5>
					</div>
					<div class="modal-body">
						<textarea name="txtarea" id="text_fill">
							<?php echo $text[2];?>	
						</textarea>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<input type="submit" name="change" value="Cambia" class="btn btn-primary"/>
					</div>
				</form>
			</div>
		</div>
		<?php
			if(isset ($_POST['change'])){
				$var = $_POST['txtarea'];		
				$conn = InstauraConnessione();
				$sql_insert = "UPDATE tbl_convenzioni SET Descrizione = '". $var ."' WHERE IdConvenzione = " .$id_convenzione;
				$conn->query($sql_insert);
				AbbattiConnessione($conn);
			}
		?>
		
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6" id="contenuto_img">
					<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
						<ul class="carousel-indicators">
							<?php
								$conn = InstauraConnessione();
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
						</ul>
						<div class="carousel-inner">
							<?php
								foreach ($result_immagini as $key => $item)
								{
									if($key == 0)
										echo "<div class='carousel-item active' style='background-image:url(img/convenzioni/".$item['NomeFile'].")'></div>";
									else
										echo "<div class='carousel-item' style='background-image:url(img/convenzioni/".$item['NomeFile'].")'></div>";									
								}
								AbbattiConnessione($conn);								
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
					<?php
						$conn = InstauraConnessione();
						$sql_info_testo = "SELECT * FROM tbl_convenzioni WHERE IdConvenzione = " . $id_convenzione;
						$result_info_testo = $conn->query($sql_info_testo);
						if ($result_info_testo->num_rows > 0){
							//Remove tags strip_tags($string, 'tags')	Non funziona
							$array =  mysqli_fetch_row($result_info_testo);
							echo "<h2 id='title_convenction' >". $array[1] ."</h2>
							<div>
								". $array[2] ."
							</div>
							";
						}else{
							echo "<h2 id='title_convenction' >Titolo non presente</h2>
							<div>
								<p id='descrizione'>Descrizione non presente</p>
							</div>
							";
							
						}
						AbbattiConnessione($conn);
					?>
						
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
						$conn = InstauraConnessione();
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
								echo "<script>alert('La convenzione è già stata votata')</script>";;
							
						}
						AbbattiConnessione($conn);
					?>
				</div>
			</div>
			<div class="row">
				<div class="col-4 col-md-auto" id="contenuto_allegati">
					<ul id="elenco_allegati">
						<?php
							$conn = InstauraConnessione();
							$sql_allegati = "SELECT * FROM tbl_allegati WHERE IdConvenzione = " . $id_convenzione;
							$result_allegati = $conn->query($sql_allegati);

							foreach ($result_allegati as $key => $item)
							{
								echo "<li><a href='img/allegati/". $item['NomeFile'] ."'>". $item['NomeFile'] ."</a></li>";								
							}
							AbbattiConnessione($conn);
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
	
	</body>
	
	
</html>
