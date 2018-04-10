<html>
<head>
	
</head>
<body>
</body>
</html>

<?php
	require_once('functions.php');
	if(isset($_GET['function'])) {
		$function = $_GET['function'];
		$function();
	}

	if(isset($_POST['function'])) {
		$function = $_POST['function'];
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
				echo "<p id='scadenza' class='red'> scadenza : ". $array[7]."</p>";
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
					echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class='br-fractional'></a>";
				else{
					echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
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
						echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class=''></a>";
					else
						echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
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
						echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class=''></a>";
					else
						echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
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
						echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class=''></a>";
					else
						echo "<a data-rating-value='".$i."' data-rating-text='".$i."' class='br-selected br-current'></a>";
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
	function AddImagesConvenction() {
		if(isset($_FILES['FileUploader'])){
			$id = $_POST['id'];
			$res = [];
			$conn = InstauraConnessione();
			$sql = "SELECT MAX(Ordine) FROM tbl_immagini WHERE IdConvenzione = ". $id;
			$result= $conn->query($sql);
			$ordine = mysqli_fetch_row($result);
			$ord = $ordine[0]+1;
			AbbattiConnessione($conn);
			$conn = InstauraConnessione();
			$uploaddir = '../img/convenzioni/';
			$length = count($_FILES['FileUploader']['name']);
			for($i = 0; $i < $length; $i++) {
				$tmpname = basename($_FILES['FileUploader']['tmp_name'][$i]);
				$path_parts = pathinfo($tmpname);				
				$filename = $path_parts['filename'];
				$path_parts = pathinfo($_FILES['FileUploader']['name'][$i]);
				$filename = $filename . "." . $path_parts['extension'];
				$uploadfile = $uploaddir . $filename;

				if (move_uploaded_file($_FILES['FileUploader']['tmp_name'][$i], $uploadfile)) {			
					$sql = "INSERT INTO tbl_immagini (NomeFile, Ordine, IdConvenzione) VALUES ('$filename', $ord, $id)";
					if ($conn->query($sql) === TRUE) {
						$res = array_push_assoc($res, $i, array('code' => '200', 'file' => $_FILES['FileUploader']['name'][$i], 'query' => '200'));
					} else {
						$res = array_push_assoc($res, $i, array('code' => '200', 'file' => $_FILES['FileUploader']['name'][$i], 'query' => '500'));
					}
				} else {
					$res = array_push_assoc($res, $i, array('code' => '500', 'file' => $_FILES['FileUploader']['name'][$i]));
				}
			}

			AbbattiConnessione($conn);
			echo json_encode($res);
		}
		else{
			$res = array('code' => '404', 'file' => '', 'message' => 'no_image');
			echo json_encode($res);
		}
	}
	function ChangeCoupon(){
		$conn = InstauraConnessione();
		$titolo = $_POST['titolo'];
		$descrizione = $_POST['descrizione'];
		$descrizione = mysqli_real_escape_string($conn, $descrizione);
		$indirizzi = $_POST['indirizzi'];
		$scadenza = $_POST['scadenza'];
		$id_categoria = $_POST['categoria'];
		$id_convenzione = $_POST['id_convenzione'];
		if(isset($_POST['vett_elimina_images']))
			$vett_eliminati_img = $_POST['vett_elimina_images'];
		if(isset($_POST['vett_elimina_attachments']))
			$vett_eliminati_att = $_POST['vett_elimina_attachments'];
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$query = "UPDATE tbl_convenzioni SET Titolo = '". $titolo ."', Descrizione = '". $descrizione ."', DataScadenza = '". $scadenza. "', IdCategoria = ". $id_categoria ." WHERE IdConvenzione = ".$id_convenzione;
		$conn->query($query);
		AbbattiConnessione($conn);	
		$conn = InstauraConnessione();
		if ($conn->query($query) === TRUE) {
			$sql = "DELETE FROM tbl_indirizzi WHERE IdConvenzione = ".$id_convenzione;
			$conn->query($sql);
			AbbattiConnessione($conn);	
			$conn = InstauraConnessione();
			$sql = "INSERT INTO tbl_indirizzi (IdRegione, IdConvenzione, Luogo, Lat, Lng) VALUES ";
			foreach ($indirizzi as $key => $value) {
				$regione = $value['regione'];
				$luogo = $value['indirizzo'];
				
				if($luogo != ""){
					// $conn = InstauraConnessione();
					$coordinates = GetCoordinates($luogo);
					$lat = 0;
					$lng = 0;
					if($coordinates){
						$lat = explode("|", $coordinates)[0];
						$lng = explode("|", $coordinates)[1];
					}
					$valuesSql = $sql . " ($regione, $id_convenzione, '$luogo', $lat, $lng)";
					echo "<script>console.log('$valuesSql')</script>";
					$conn->query($valuesSql);
				}
			}
		}
		else {
			echo "Error: " . $query . "<br>" . $conn->error;
		}

		if(isset($_POST['vett_elimina_images'])){
			foreach ($vett_eliminati_img as $id_eliminato_img){
				$conn = InstauraConnessione();
				$query = "DELETE FROM tbl_immagini WHERE IdImmagine = ". $id_eliminato_img ;
				$conn->query($query);
				AbbattiConnessione($conn);		
			}	
		}	
		if(isset($_POST['vett_elimina_attachments'])){
			foreach ($vett_eliminati_att as $id_eliminato_att){
				$conn = InstauraConnessione();
				$query = "DELETE FROM tbl_allegati WHERE IdAllegato = ". $id_eliminato_att ;
				$conn->query($query);
				AbbattiConnessione($conn);		
			}	
		}			
		return;
	}
	
	function AddAttachments() {
		if(isset($_FILES['FileUploader2'])){
			$conn = InstauraConnessione();
			
			$id = $_POST['id'];
			$res = [];
			echo 'entra';
			$uploaddir = '../allegati/';
			$length = count($_FILES['FileUploader2']['name']);
			
			for($i = 0; $i < $length; $i++) {
				$tmpname = basename($_FILES['FileUploader2']['tmp_name'][$i]);
				$path_parts = pathinfo($tmpname);
				
				$filename = $path_parts['filename'];

				$path_parts = pathinfo($_FILES['FileUploader2']['name'][$i]);
				$filename = $filename . "." . $path_parts['extension'];

				$uploadfile = $uploaddir . $filename;

				if (move_uploaded_file($_FILES['FileUploader2']['tmp_name'][$i], $uploadfile)) {
			
					$sql = "INSERT INTO tbl_allegati (NomeFile, IdConvenzione) VALUES ('$filename', $id)";

					if ($conn->query($sql) === TRUE) {
						$res = array_push_assoc($res, $i, array('code' => '200', 'file' => $_FILES['FileUploader2']['name'][$i], 'query' => '200'));
					} else {
						$res = array_push_assoc($res, $i, array('code' => '200', 'file' => $_FILES['FileUploader2']['name'][$i], 'query' => '500'));
					}

				} else {
					$res = array_push_assoc($res, $i, array('code' => '500', 'file' => $_FILES['FileUploader2']['name'][$i]));
				}
			}

			AbbattiConnessione($conn);
			echo json_encode($res);
		}
		else{
			$res = array('code' => '404', 'file' => '', 'message' => 'no_image');
			echo json_encode($res);
		}
		
	}
	
	