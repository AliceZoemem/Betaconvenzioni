<?php
	require_once('functions/functions.php');
	$cookie_name = 'auth_betaconvenzioni';
	if(isset($_COOKIE[$cookie_name])){
		if(isset($_GET['cancel']))
		{
			echo "safasf";
		}
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
		<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
		<script>tinymce.init({ selector:'textarea', height: '50vh' });</script>
		
		<style>
			.hidden{
				display:none;
			}
			.box:hover{
				background-color: #ccc;
			}
			.container_comments {
				width: 75%;
				margin-left:15%;
				border: 2px solid black;
			}
			.box{
				box-sizing: border-box;
				width: 100%;
				border-bottom: 2px solid black;
				text-align:center;
			}
			
			.box1 {
				box-sizing: border-box;
				width: 80%;
				float: left;
				border-right: 2px solid black;
				padding: 1%
			}
			.box2 {
				box-sizing: border-box;
				width: 20%;
				float: left;
				padding: 1%
			}
			#commento{
				width: 100%;
			}
			#response{
				text-align: left;
				margin-top: 5%;
			}
			#title_convenction{
				margin-top:2%;
			}
			.left{
				float : left;
			}
			.right{
				float : right;
			}
			#img_convenction{
				width:60%;
			}
			#contenuto_testo{
				right:5%;
				margin-top:3%;
			}
			#contenuto_img{
				left: 5%;
				margin-top: 2%;
			}
			#contenuto_allegati{
				left:5%;
			}
			ul{	
				list-style-type: none;
			}
			#carouselExampleIndicators{
				width:80%;
			}	
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
			.cancel{
				visiblity: visible;
				display: inline;
				width: 3rem;
				position: absolute;
				right: 15%;
				top: 5%;
			}
			.mce-widget.mce-notification.mce-notification-warning.mce-has-close.mce-in{
				display:none;
			}
			.back{
				margin-left: 2%;
				margin-top: 1vh;
			}
			.red{
				color: red;
			}
			p{
				margin: 0%;
			}
			.back:hover {
			   filter: brightness(50%);
			}
			.logout:hover{
				color:brown;
			}
		</style>
		
	</head>
	<body>
		<img class='back' src='/img/back.png' onclick="window.location.href='/homepage.php'"> </img>
		<button type="button" class="right logout" onclick="window.location.href='/logout.php'">Logout</button>
		<?php 
			$conn = InstauraConnessione();
			$id_utente = $_COOKIE['auth_betaconvenzioni'];
			$id_utente = Encryption($id_utente, 'd');
			$sql_isAdmin = "SELECT * FROM tbl_utenti WHERE IdUtente = " . $id_utente;
			$result_isAdmin = $conn->query($sql_isAdmin);
			$typeadmin =  mysqli_fetch_row($result_isAdmin);
			if($typeadmin[7] != 0){
				echo("<img class='pencil' src='/img/pencil.png' data-toggle='modal' data-target='#exampleModal'></img>");
				echo("<img class='cancel' src='/img/X.png' onClick='cancel()' ></img>");
			}
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
			//popup mantiene testo vecchio se non ri refresha
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
						/*
							lista commenti
							tbl_log visualizzazioni
						*/
						if ($result_info_testo->num_rows > 0){
							//Remove tags strip_tags($string, 'tags')	Non funziona
							$array =  mysqli_fetch_row($result_info_testo);
							
							AbbattiConnessione($conn);
							
							$conn = InstauraConnessione();
							$sql_categoria = "SELECT Nome FROM tbl_categorie INNER JOIN tbl_convenzioni ON tbl_categorie.IdCategoria = tbl_convenzioni.IdCategoria WHERE IdConvenzione = " . $id_convenzione;
							$result_categoria = $conn->query($sql_categoria);
							$nome =  mysqli_fetch_row($result_categoria);
							echo "<p> Nome Categoria : ".$nome[0] ."</p>";
							AbbattiConnessione($conn);
							
							$lon = $typeadmin[6];
							$lat = $typeadmin[5];
							$conn = InstauraConnessione();
							$sql_distanza = "SELECT sp_calculatedistance(".$array[4].",".$array[5].",".$lat.",".$lon.") AS Distanza";
							$result_distanza = $conn->query($sql_distanza);
							$dis =  mysqli_fetch_row($result_distanza);
							echo "Distanza : ".round($dis[0], 1) ." km";
							 
							echo "<p> Luogo : ". $array[3] ."</p>";
							$today = date("Y-m-d"); 
							$new_scad = str_replace("-","",$array[7]);
							$new_today = str_replace("-","",$today);
							if( ($new_scad - $new_today)< 7 && $new_scad != '00000000'){
								echo "<p class='red'> scadenza : ". $new_scad ."</p>";
							}else{
								if($new_scad == '00000000')
									echo "<p> scadenza infinita</p>";
								else
									echo "<p> scadenza : ". $new_scad ."</p>";
							}			
							echo "<p> Media Voti"."</p>";
							echo "<h2 id='title_convenction' >". $array[1] ."</h2>";
							echo  "<div>". $array[2] ."</div>";
							
						}
					?>
					<div id="response">	
						<form action = "" method = "post">						
							<input id="commento" type="text" name="commento" value="" placeholder="Lascia un commento">	
							<div class="stars stars-example-css">
								<select id="example-css" name="rating" autocomplete="off">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
								<p id="commento"></p>
								<?php
									$conn = InstauraConnessione();
									$id_utente = $_COOKIE['auth_betaconvenzioni'];
									$id_utente = Encryption($id_utente, 'd');							
									$sql_control_insert = "SELECT * FROM tbl_feedback WHERE IdUtente = " . $id_utente ." AND IdConvenzione = ". $id_convenzione;
									$result_control_insert = $conn->query($sql_control_insert);
									if ($result_control_insert->num_rows <= 0)
										$already_rating = 0;							
									else{
										$voto_stelle = mysqli_fetch_row($result_control_insert)[2];
										$commento = mysqli_fetch_row($result_control_insert)[3]; 
										//$commento rimane vuoto
										echo ($commento);
										echo "<script>
											$('#commento').text('Commento : ".$commento."');
											$(document).ready(function () {
												$('select').barrating('clear');
												$('select').barrating('set', ".$voto_stelle .");
												
											});
										</script>
										";	
										$already_rating = 1;
									}
								?>
							</div>  
							<input type="submit" name="feedback" value="VOTA">
						</form>
						<?php
							if(isset ($_POST['feedback'])){		
								$option = isset ($_POST['rating']) ? $_POST['rating'] : "";
								$voto = intval($option);
								$commento = $_POST['commento'];
								if ($already_rating == 0) {	
									if($commento != "")
										$sql_insert = "INSERT INTO tbl_feedback (IdUtente, IdConvenzione, Voto, Commento)VALUES(" . $id_utente.",".$id_convenzione.",".$voto.",'".$commento."')";
									else
										$sql_insert = "INSERT INTO tbl_feedback (IdUtente, IdConvenzione, Voto)VALUES(" . $id_utente.",".$id_convenzione.",".$voto.",'')";
									
									$conn->query($sql_insert);
																	
								}else{
									AbbattiConnessione($conn);
									$conn = InstauraConnessione();
									$sql_update = "UPDATE tbl_feedback SET Voto = ". $voto ." , Commento = '". $commento ."' WHERE IdConvenzione = " .$id_convenzione ." AND IdUtente = " . $id_utente;
									$conn->query($sql_update);
								}
								echo "<script>
									$(document).ready(function () {
									$('select').barrating('clear');
									$('select').barrating('set', ".$voto.");
								});
								</script>
								";	
							}
							AbbattiConnessione($conn);
						?>
					</div>
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
		<div class="container_comments">
			<div class="box" onClick="makevisible()">COMMENTI</div>
			<div id="box_appear" class="hidden">
				<div class="box1">This div occupies theTThis div occupies thThis div occupies thThis div occupies thThis div occupies thhis div occupies thThis div occupies th left half</div>
				<div class="box2">This div occupies the right half</div>
				
				<div class="box1">This div occupies theTThis div occupies thThis div occupies thThis div occupies thThis div occupies thhis div occupies thThis div occupies th left half</div>
				<div class="box2">This div occupies the right half</div>
				
				<div style="clear:both;"></div>
			</div>
		</div>

	
	
	<script>
	var hidden = 1;
		$(document).ready(function () {
			$('.carousel').carousel();
			$($('form')[0]).attr('action', window.location.href);
		});
		function cancel() {
			// var currentLocation = window.location;
			// window.location.assign(currentLocation+"?cancel=true" );
			alert('cos');
		}
		function makevisible(){
			if(hidden == 1){
				hidden = 0;
				$('#box_appear').show("slow");
			}else{
				hidden = 1;
				$('#box_appear').hide("slow");
			}
		}
	</script>
	
	</body>
	
	
</html>
