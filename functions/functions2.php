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
		$luogo = $_POST['luogo'];
		$scadenza = $_POST['scadenza'];
		$id_categoria = $_POST['categoria'];
		$id_convenzione = $_POST['id_convenzione'];
		$coordinates = GetCoordinates($luogo);
		if(isset($_POST['vett_elimina']))
			$vett_eliminati = $_POST['vett_elimina'];
			
		if($coordinates){
			$lat = explode("|", $coordinates)[0];
			$lng = explode("|", $coordinates)[1];
		}
		else{
			$lat = 0;
			$lng = 0;
		}

		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$query = "UPDATE tbl_convenzioni SET Titolo = '". $titolo ."', Descrizione = '". $descrizione ."', Luogo = '". $luogo ."', Lat= ". $lat .", Lng=". $lng .", DataScadenza = '". $scadenza. "', IdCategoria = ". $id_categoria ." WHERE IdConvenzione = ".$id_convenzione;
		$conn->query($query);
		AbbattiConnessione($conn);	
		
		if(isset($_POST['vett_elimina'])){
			foreach ($vett_eliminati as $id_eliminato){
				$conn = InstauraConnessione();
				$query = "DELETE FROM tbl_immagini WHERE IdImmagine = ". $id_eliminato ;
				$conn->query($query);
				AbbattiConnessione($conn);		
			}	
		}		
		return;
	}
	
	function AddAttachments() {
		if(isset($_FILES['FileUploader'])){
			$conn = InstauraConnessione();
			
			$id = $_POST['id'];
			$res = [];
			echo 'entra';
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
			
					$sql = "INSERT INTO tbl_immagini (NomeFile, Ordine, IdConvenzione) VALUES ('$filename', 0, $id)";

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
	
	