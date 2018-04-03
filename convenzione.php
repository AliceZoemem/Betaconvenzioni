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
		<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
		<script>tinymce.init({ selector:'textarea', height: '50vh' });</script>
		
		<style>
			.mod1{
				float: right;
				margin-right: 50%;
			}
			.mod2{
				float: right;
				margin-right: 50%;
				padding-top: 3%;
			}
			.mod3{
				margin-left: 75%;
				margin-bottom: 1%;
			}
			.mod4{
				float: right;
				margin-right: 50%;
			}
			#altri_commenti{
				margin-left:1%;		
			}
			#box_appear{
				position: relative;
				margin-left: 20%;
				width: 250%;
				margin-top:5vh;
			}
			.hidden{
				display:none;
			}
			.container_comments {
				width:30%;
				margin-top:2vh;
			}
			.box1 {
				box-sizing: border-box;
				width: 80%;
				float: left;
				padding: 1%;
			}
			.box2 {
				box-sizing: border-box;
				width: 20%;
				float: left;
				padding: 1%;
			}
			.box3{
				position:absolute;
				margin-top:1vh;
			}
			#commento{
				width: 100%;
			}
			#title_convenction{
				margin-top:4%;
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
			#media_voti{
				display: inline-block;
				width: 50%;
				padding-bottom: 2%;
				padding-left: 1%;
			}
			#media_voti .br-theme-css-stars .br-widget a{
				font-size: 230%;
				margin-right: 3%;
			}
			#media{
				display: inline-block;
			}
			.br-fractional{
				content: '\f123';
				color: #50E3C2;
			}
			#prova{
				background: url(img/pencil.png) no-repeat; 
				border: none;
				width: 5%;
				height: 6%;
				background-size: 100%;
				float: right;
				margin-right: 50%;
			}
			#prova2{
				background: url(img/pencil.png) no-repeat; 
				border: none;
				width: 5%;
				height: 6%;
				background-size: 100%;
				margin-left: 75%;
			}
			#scadenza{
				margin-top:2%;
			}
			#remove_photo{
				position: absolute;
				left: 30%;
				bottom: 70%;
			}
		</style>
		
	</head>
	<body>
		<?php
			$id_utente = $_COOKIE['auth_betaconvenzioni'];
			$id_utente = Encryption($id_utente, 'd');
			$pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) &&($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 
			if($pageRefreshed == 0){
				$sql_check_log = "SELECT * FROM tbl_log WHERE IdConvenzione = ".$id_convenzione. " AND IdUtente = ".$id_utente;
				$conn = InstauraConnessione();
				$result_check_log = $conn->query($sql_check_log);
				if ($result_check_log->num_rows > 0){
					$info_log =  mysqli_fetch_row($result_check_log);
					$last_visit = str_replace("-","",$info_log[3]);
					$last_visit = str_replace(" ","",$last_visit);
					$last_visit = str_replace(":","",$last_visit);
					$today = date("Ymd").date("His");
					if(($today - $last_visit)> 100000){
						$new_contatore = $info_log[2] + 1;
						$new_today = date("Y-m-d"). " ".date("H:i:s");
						$conn = InstauraConnessione();
						$sql_update_log = "UPDATE tbl_log SET Contatore = ". $new_contatore .", UltimaVisualizzazione = '".$new_today."' WHERE IdConvenzione = ".$id_convenzione. " AND IdUtente = ".$id_utente;
						$conn->query($sql_update_log);
						AbbattiConnessione($conn);
					}
				}else{
					AbbattiConnessione($conn);	
					$sql_insert_log ="INSERT INTO tbl_log (IdUtente, IdConvenzione, Contatore, UltimaVisualizzazione)VALUES(" . $id_utente.",".$id_convenzione.",1, CURRENT_TIMESTAMP())";
					$conn = InstauraConnessione();
					$conn->query($sql_insert_log);
					AbbattiConnessione($conn);	
				}
			}
			
			
		?>
		<img class='back' src='/img/back.png' onclick="window.location.href='/homepage.php'"> </img>
		<button type="button" class="right logout" onclick="window.location.href='/logout.php'">Logout</button>
		<?php 
			$conn = InstauraConnessione();
			$sql_isAdmin = "SELECT * FROM tbl_utenti WHERE IdUtente = " . $id_utente;
			$result_isAdmin = $conn->query($sql_isAdmin);
			$typeadmin =  mysqli_fetch_row($result_isAdmin);
			if($typeadmin[7] != 0){
				echo("<img class='cancel' src='/img/X.png' onClick='cancel()' ></img>");
			}
			AbbattiConnessione($conn);	
			$conn = InstauraConnessione();
			$sql_text = "SELECT * FROM tbl_convenzioni WHERE Idconvenzione = " . $id_convenzione;
			$result_text = $conn->query($sql_text);
			$text=  mysqli_fetch_row($result_text);			
		?>
		
		
		
		
		<div class="container-fluid">
			<div class="row">
				
				<div class="col-sm-6" id="contenuto_img"><?php
					if($typeadmin[7] != 0)
						echo "<button id='prova2' data-toggle='modal' onclick='myFunction()'></button>";
				?>
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
				<script>
					var i = 0;
					var y = 0;
					function myFunction() {
						if(i == 0){
							i = 1;
							var btn = document.createElement("BUTTON");
							btn.id = "remove_photo";
							var t = document.createTextNode("X");
							btn.appendChild(t);
							document.body.appendChild(btn);
						}else{
							if(y == 0){
								y = 1;
								document.getElementById("remove_photo").style.display = "none";
								document.getElementById("remove_photo").style.visibility = "hidden";
							}
							else{
								y = 0;
								document.getElementById("remove_photo").style.display = "block";
								document.getElementById("remove_photo").style.visibility = "visible";
							}
						}
						
					}
				</script>
				<div class="col-sm-6" id="contenuto_testo">
					<?php
						$conn = InstauraConnessione();
						$sql_info_testo = "SELECT * FROM tbl_convenzioni WHERE IdConvenzione = " . $id_convenzione;
						$result_info_testo = $conn->query($sql_info_testo);
						/*
							elimina convenzione
						*/
						if ($result_info_testo->num_rows > 0){
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
							if($typeadmin[7] != 0)
								echo "<button id='prova' data-toggle='modal' data-target='#exampleModal' onclick='myFunction()'></button>";
							if( ($new_scad - $new_today)< 7 && $new_scad != '00000000'){
								echo "<p id='scadenza' class='red'> scadenza : ". $new_scad ."</p>";
							}else{
								if($new_scad == '00000000')
									echo "<p id='scadenza'> scadenza infinita</p>";
								else
									echo "<p id='scadenza'> scadenza : ". $new_scad ."</p>";
							}		
							echo '<br/>';							
							$conn = InstauraConnessione();
							$sql_tot_voti = "SELECT AVG(Voto) FROM tbl_feedback WHERE IdConvenzione = ".$id_convenzione;
							$result_tot_voti = $conn->query($sql_tot_voti);
							$media = mysqli_fetch_row($result_tot_voti)[0];
							echo "<p id='media'> Media Voti : ". round($media, 1). "</p>";
							echo "<div id='media_voti'>
								<div class='stars stars-example-css'>
									<div class='br-wrapper br-theme-css-stars'>
										<div class='br-widget'>";							
							
							for($i=1; $i< 6; $i++){
								if($i > $media)
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class='br-fractional'></a>";
								else{
									// echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
								}
							}
							echo "</div>
									</div>
								</div>
							</div>";
							if($typeadmin[7] != 0)
								echo "<button id='prova' data-toggle='modal' data-target='#exampleModal' onclick='myFunction()'></button>";
							echo "<h2 id='title_convenction' >". $array[1] ."</h2>";
							if($typeadmin[7] != 0)
								echo "<button id='prova' data-toggle='modal' data-target='#exampleModal' onclick='myFunction()'></button>";
								// echo("<img class='change mod1' src='/img/pencil.png' data-toggle='modal' data-target='#exampleModal'></img>");
							echo  "<div>". $array[2] ."</div>";
							
							AbbattiConnessione($conn);
						}
					?>
					
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
		
			
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form class="modal-content" action = "" method = "post">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Modifica</h5>
					</div>
					<div class="modal-body">
						<textarea name="txtarea" id="text_fill">
							<?php
								// $pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) &&($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 
								// if($pageRefreshed == 1){
									// AbbattiConnessione($conn);	
									// $conn = InstauraConnessione();
									// $sql_text = "SELECT Descrizione FROM tbl_convenzioni WHERE Idconvenzione = " . $id_convenzione;
									// $result_text = $conn->query($sql_text);
									// $text=  mysqli_fetch_row($result_text);	
									// echo $text[0];
									// AbbattiConnessione($conn);	
								// }else{
									// $var = $html->find('img[class=change]');
									// echo $var;
									// switch($var){
										
									// }
									// echo $text[2];
									// AbbattiConnessione($conn);	
								// }
							?>	
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
				$var = mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x27\x5C]', '\\\0', $_POST['txtarea']);		
				// $var = $_POST['txtarea'];		
				$conn = InstauraConnessione();
				$sql_insert = "UPDATE tbl_convenzioni SET Descrizione = '". $var ."' WHERE IdConvenzione = " .$id_convenzione;
				$conn->query($sql_insert);
				AbbattiConnessione($conn);
			}
		?>
		<div class="container_comments">
			<div id="box_appear" >
				<form action = "" method = "post">						
					<div class="box1"><input id="commento" type="text" name="commento" value="" placeholder="Lascia un commento"></div>
					<div class="stars stars-example-css box2">
						<select id="example-css" name="rating" autocomplete="off">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>  
					<input class="box3" type="submit" name="feedback" value="VOTA">
				</form>
				
				<?php
					$conn = InstauraConnessione();
					$id_utente = $_COOKIE['auth_betaconvenzioni'];
					$id_utente = Encryption($id_utente, 'd');							
					$sql_control_insert = "SELECT * FROM tbl_feedback WHERE IdUtente = " . $id_utente ." AND IdConvenzione = ". $id_convenzione;
					$result_control_insert = $conn->query($sql_control_insert);
					if ($result_control_insert->num_rows <= 0){
						$already_rating = 0;	
					}else{
						$vettore_info = mysqli_fetch_row($result_control_insert);
						$already_rating = 1;
					}
				?>
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
						
						
					}
					AbbattiConnessione($conn);
					if($already_rating == 0){
						echo "<div class='box1'>Convenzione non ancora votata</div>";	
					}else{
						$pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) &&($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 
						if($pageRefreshed == 1){
							$conn = InstauraConnessione();
							$sql_control_insert = "SELECT * FROM tbl_feedback WHERE IdUtente = " . $id_utente ." AND IdConvenzione = ". $id_convenzione;
							$result_control_insert = $conn->query($sql_control_insert);
							$vettore_info = mysqli_fetch_row($result_control_insert);
							echo "<div class='box1'>".$vettore_info[3]."</div>";
							echo "<div class='stars stars-example-css box2'>
									<div class='br-wrapper br-theme-css-stars'>
										<div class='br-widget'>";
							for($i=1; $i< 6; $i++){
								if($i > $vettore_info[2])
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class=''></a>";
								else
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
							}
							echo"	</div>
								</div>
							</div>";
							AbbattiConnessione($conn);
						}else{							
							echo "<div class='box1'>".$vettore_info[3]."</div>";
							echo "<div class='stars stars-example-css box2'>
									<div class='br-wrapper br-theme-css-stars'>
										<div class='br-widget'>";
							for($i=1; $i< 6; $i++){
								if($i > $vettore_info[2])
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class=''></a>";
								else
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
							}
							echo"	</div>
								</div>
							</div>";
						}
					}
				?>
				
				<br/><br/><br/><br/>
				<h3 id="altri_commenti">ALTRI COMMENTI</h3>
				
				<?php
					$conn = InstauraConnessione();
					$sql_feedback = "SELECT * FROM tbl_feedback WHERE IdConvenzione = " . $id_convenzione;
					$result_feedback = $conn->query($sql_feedback);
					
					foreach ($result_feedback as $key => $item)
					{
						if($item['IdUtente'] == $id_utente & $item['IdConvenzione'] == $id_convenzione){							
						}else{
							echo "<div class='box1'>".$item['Commento']."</div>";
							echo "<div class='stars stars-example-css box2'>
									<div class='br-wrapper br-theme-css-stars'>
										<div class='br-widget'>";
							for($i=1; $i< 6; $i++){
								if($i > $item['Voto'])
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class=''></a>";
								else
									echo "<a href='#' data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
							}
							echo"	</div>
								</div>
							</div>";
						}
						
					}
					AbbattiConnessione($conn);
				?>
				
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
			$.ajax({
				url : "yourScript.php", // the resource where youre request will go throw
				type : "POST", // HTTP verb
				data : { action: 'myActionToGetHits', param2 : myVar2 },
				dataType: "json"
			});
		}
		
	</script>
	</body>
	
</html>
