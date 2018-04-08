<html>
<head>
	<!-- <script src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script> -->
</head>
<body>
</body>
</html>

<?php
	require_once('functions/functions.php');
	if(isset($_GET['function'])) {
		$function = $_GET['function'];
		$function();
	}
	function fill_content_convenction($id_convenzione, $typeadmin){
		$conn = InstauraConnessione();
		$sql_info_testo = "SELECT * FROM tbl_convenzioni WHERE IdConvenzione = " . $id_convenzione;
		$result_info_testo = $conn->query($sql_info_testo);

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
			$scadenza = '';
			if( ($new_scad - $new_today)< 7 && $new_scad != '00000000'){
				$scadenza = $new_scad;
				echo "<p id='scadenza' class='red'> scadenza : ". $new_scad ."</p>";
			}else{
				if($new_scad == '00000000'){
					$scadenza = 'nessuna scadenza';
					echo "<p id='scadenza'> scadenza infinita</p>";
				}else{
					$scadenza = $new_scad;
					echo "<p id='scadenza'> scadenza : ". $array[7] ."</p>";
				}
			}								
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
			
			echo "<h2 id='title_convenction' >". $array[1] ."</h2>";
			
			echo  "<div>". $array[2] ."</div>";
			
			AbbattiConnessione($conn);
		}
		
	}
	function manage_feedback($id_convenzione, $id_utente, $already_rating, $vettore_info){
		$pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) &&($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 
		if($pageRefreshed == 1){
			$conn = InstauraConnessione();									
			$sql_control_insert = "SELECT * FROM tbl_feedback WHERE IdUtente = " . $id_utente ." AND IdConvenzione = ". $id_convenzione;
			$result_control_insert = $conn->query($sql_control_insert);
			if ($result_control_insert->num_rows <= 0){
				$already_rating = 0;	
			}else{
				$vettore_info = mysqli_fetch_row($result_control_insert);
				$already_rating = 1;
			}
		}
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
	}
	function other_comments($id_convenzione, $id_utente){
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
	}
	function manage_log($id_convenzione, $id_utente){
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
	}
	
	function ChangeCoupon(){
		$titolo = $_POST['titolo'];
		$descrizione = $_POST['descrizione'];
		$descrizione = mysql_real_escape_string($descrizione);
		$luogo = $_POST['luogo'];
		$scadenza = $_POST['scadenza'];
		$categoria = $_POST['categoria'];
		$today = date("Y-m-d");
		$coordinates = GetCoordinates($luogo);
		$vett_eliminati = $_POST['vett_elimina'];

		if($coordinates){
			$lat = explode("|", $coordinates)[0];
			$lng = explode("|", $coordinates)[1];
		}
		else{
			$lat = 0;
			$lng = 0;
		}
		
		$conn = InstauraConnessione();
		$query = "SELECT IdCategoria FROM tbl_categorie WHERE Nome = ". $categoria ;
		$result_categoria = $conn->query($query);
		$vettore = mysqli_fetch_row($result_categoria);
		$id_categoria = $vettore[0];
		echo $id_categoria;
		AbbattiConnessione($conn);
		// $conn = InstauraConnessione();

		// /* check connection */
		// if (mysqli_connect_errno()) {
			// printf("Connect failed: %s\n", mysqli_connect_error());
			// exit();
		// }

		// $query = "UPDATE tbl_categorie SET Titolo = '". $titolo ."', Descrizione = '". $descrizione ."', Luogo = '". $luogo ."', Lat= ". $lat .", Lng=". $lng .", DataScadenza =". $scadenza. ", IdCategoria = ". $id_categoria ;

		// $conn->query($sql_update_log);
		// AbbattiConnessione($conn);

		return;
	}