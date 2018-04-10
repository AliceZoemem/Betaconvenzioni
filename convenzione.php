<?php
	require_once('functions/functions.php');
	require_once('functions/functions2.php');
	$cookie_name = 'auth_betaconvenzioni';
	if(isset($_COOKIE[$cookie_name])){
		if($_GET['convenzione']){
			$id_convenzione = $_GET['convenzione'];
			$conn = InstauraConnessione();
			$sql_info_testo = "SELECT * FROM tbl_convenzioni WHERE IdConvenzione = " . $id_convenzione;
			$result_info_testo = $conn->query($sql_info_testo);
			$array =  mysqli_fetch_row($result_info_testo);
			AbbattiConnessione($conn);			
		}else{
			header("Location: homepage.php");
		}
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
		<script>tinymce.init({ selector:'#id_new_descrizione',width:'100%'});</script>
		
		<style>
			#allegati{
				margin-top:2%;
				display:inline-block;
			}
			.wrong-form-control{
				border:1px solid #f00;
			}
			.add{
				padding-top: 2%;
				padding-bottom: 2%;
				margin-left: 25%;
				width:5%;
			}
			#popup{
				text-align: center;
				position: absolute;
				left: 35%;
				top: 35%;
				background-color: #eee;
				z-index: 1;
				padding: 3%;
				border-radius: 1%;
				border: 1px solid black;
			}
			#altri_commenti{
				margin-left:1%;	
				margin-top: 2%;				
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
				display: inline;
				width: 3%;
				position: absolute;
				right: 20%;
				top: 5%;
				background-size: 100%;
				border: none;
			}
			#annulla{
				right:25%;
			}			
			.cancel{
				visiblity: visible;
				display: inline;
				width: 3%;
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
			.popup .popuptext {
				visibility: hidden;
			}
			.popup .show {
				visibility: visible;
				background-color:red;
			}
			.form-control{
				display: inline-block;
			}
			#mceu_11{
				display:inline-block;
				width: 80%;
			}
			.img_change{
				width: 60%;
				height: 20%;
			}
			.attachments_change{
				width: 60%;
				height: 20%;
			}
			.trash{
				width: 5%;
			}
			ul#orizontal_list{	
				display: inline-block;
				padding-top:2%;
			}
			ul#orizontal_list li{
				display:inline;
				margin-right: 7%;
			}
		</style>
		<script>
		var vett_elimina_attachments = new Array();	
		var vett_elimina_images = new Array();	
			$(document).ready(function () {
				$('.carousel').carousel();
				$($('form')[0]).attr('action', window.location.href);	
			});			
		</script>
	</head>
	<body id="all">
		<?php
			$id_utente = $_COOKIE['auth_betaconvenzioni'];
			$id_utente = Encryption($id_utente, 'd');
			manage_log($id_convenzione, $id_utente);
		?>
		<img class='back' src='/img/back.png' onclick="window.location.href='/homepage.php'"> </img>
		<button type="button" class="btn btn-secondary right logout" onclick="window.location.href='/logout.php'">Logout</button>
		<?php 
			$conn = InstauraConnessione();
			$sql_isAdmin = "SELECT * FROM tbl_utenti WHERE IdUtente = " . $id_utente;
			$result_isAdmin = $conn->query($sql_isAdmin);
			$typeadmin =  mysqli_fetch_row($result_isAdmin);
			if($typeadmin[7] != 0){
				echo("<img class='cancel' src='/img/trash.png' onClick='DeleteCoupon(".$id_convenzione.")' ></img>");
				echo "<img class='pencil' src='img/pencil.png' onclick='ShowChangePopup()'>";
			}
			// data-toggle='modal' data-target='#exampleModal'
			AbbattiConnessione($conn);	
			$conn = InstauraConnessione();
			$sql_text = "SELECT * FROM tbl_convenzioni WHERE Idconvenzione = " . $id_convenzione;
			$result_text = $conn->query($sql_text);
			$text=  mysqli_fetch_row($result_text);			
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
						<div class="row">
						<div class="col-12" id="contenuto_allegati">
							<ul id="elenco_allegati">
								<?php
									$conn = InstauraConnessione();
									$sql_allegati = "SELECT * FROM tbl_allegati WHERE IdConvenzione = " . $id_convenzione;
									$result_allegati = $conn->query($sql_allegati);
									if ($result_allegati->num_rows > 0){
										echo "<h5 id='allegati'> Allegati: </h5>";
										echo "<ul id='orizontal_list'>";
										foreach ($result_allegati as $key => $item)
										{
											echo "<li><a download href='img/allegati/". $item['NomeFile'] ."'>". $item['NomeFile'] ."</a></li>";								
										}
										echo "</ul>";
										AbbattiConnessione($conn);
									}
									else
										echo "<h5 id='allegati'> Non ci sono allegati </h5>";
								?>
							</ul>
						</div>
					</div>
					</div>
				</div>
				<div class="col-sm-6" id="contenuto_testo">
					<?php
						$pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) &&($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 
						if($pageRefreshed == 1)
							fill_content_convenction($id_convenzione, $typeadmin);
						else
							fill_content_convenction($id_convenzione, $typeadmin);		
					?>
							
					
				</div>
			</div>
		</div>
		<div class="modal fade" id="DeletePopup" tabindex="-1" role="dialog" aria-labelledby="titleLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="titleLabel">Attenzione</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body">
				Sei sicuro di voler eliminare questa convenzione?
			  </div>
			  <div class="modal-footer">
				<button type="button" id="btnDeleteCoupon" class="btn btn-danger" onclick="ConfirmDeleteCoupon();">Elimina</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#btnDeleteCoupon').data('id', '');">Annulla</button>
			  </div>
			</div>
		  </div>
		</div>
		<div class="modal fade" id="modalChangeCoupon" tabindex="-1" role="dialog" aria-labelledby="modalAddCoupon" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form id="UploadForm" class="modal-content" enctype="multipart/form-data">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Modifica convenzione</h5>
					</div>
					<div class="modal-body new-form">
						<?php 
							
							$anno = substr ( $array[7] , 0, 4 );
							$mese = substr ( $array[7]  , strlen ($anno) +1, 2 );
							$giorno = substr ( $array[7]  , (strlen ($anno) + strlen ($mese+2)), 2 );
							$scadenza_formatted = strval ($giorno) . "/" . strval ($mese) . "/" . strval ($anno) ;
							
						?>
						<input type="text" id="id_new_scadenza" name="new_scadenza" class="form-control" type="text" onblur="(this.type='text')" onfocus="(this.type='date')" placeholder="<?php echo $scadenza_formatted;?>" value="<?php echo $array[7];?>"/>
						<input type="text" id="id_new_luogo" name="new_luogo" class="form-control" placeholder="<?php echo $array[3];?>" value="<?php echo $array[3];?>"/>
						<input id="id_new_titolo" name="new_titolo" type="text" class="form-control" placeholder="<?php echo $array[1];?>"  value="<?php echo $array[1];?>"> 
						<?php
							$conn = InstauraConnessione();
							$query_categoria = "SELECT * FROM tbl_categorie INNER JOIN tbl_convenzioni ON tbl_convenzioni.IdCategoria = tbl_categorie.IdCategoria";
							$result = mysqli_query($conn, $query_categoria);		
							$categoria = mysqli_fetch_array($result);								
							AbbattiConnessione($conn);		
						?>
						<select id="id_new_categoria" name="new_categoria" class="form-control" value="<?php echo ($categoria['Nome']);?>">
							<option value='<?php echo ($categoria['IdCategoria']);?>'><?php echo ($categoria['Nome']);?></option>

							<?php
								$conn = InstauraConnessione();
								if (mysqli_connect_errno()) {
									printf("Connect failed: %s\n", mysqli_connect_error());
									exit();
								}
								
								$query = "SELECT * FROM tbl_categorie ORDER BY Nome ASC";
								
								if ($result = mysqli_query($conn, $query)) {
									
									while ($row = mysqli_fetch_array($result)) {
										$idCategoria = $row['IdCategoria'];
										$nome = $row['Nome'];
										if($nome != $categoria['Nome'])
											echo "<option value='$idCategoria'>$nome</option>";
									}
								}
								AbbattiConnessione($conn);
							?>
						</select>
						<h5>Allegati</h5>
						<?php					
							$conn = InstauraConnessione();
							$sql_img = "SELECT * FROM tbl_allegati WHERE IdConvenzione = " . $id_convenzione ;
							if ($result_img = mysqli_query($conn, $sql_img)) {
								$x = 0;
								echo "<img class='add' src='img/add.png' onClick='ShowAttachmentsUploader()'></img><br/>";
								echo "<input type='file' id='FileUploader2' accept='image/*' class='hidden' multiple />";
								while ($row = mysqli_fetch_array($result_img)) {									
									echo "<img class='attachments_change ".$row['IdAllegato']."' src='img/allegati/". $row['NomeFile']."'></img>";	
									echo "<img class='trash_att ".$row['IdAllegato']."' src='img/trash.png' onclick='DeleteAttachment(".$row['IdAllegato'].")'></img>";	
								}
							}
							AbbattiConnessione($conn);
						?>
						<h5>Immagini</h5>
						<?php					
							$conn = InstauraConnessione();
							$sql_img = "SELECT * FROM tbl_immagini WHERE IdConvenzione = " . $id_convenzione ." ORDER BY Ordine";
							if ($result_img = mysqli_query($conn, $sql_img)) {
								$x = 0;
								echo "<img class='add' src='img/add.png' onClick='ShowFileUploader()'></img><br/>";
								echo "<input type='file' id='FileUploader' accept='image/*' class='hidden' multiple />";
								while ($row = mysqli_fetch_array($result_img)) {
									
									echo "<img class='img_change ".$row['IdImmagine']."' src='img/convenzioni/". $row['NomeFile']."'></img>";	
									echo "<img class='trash_img ".$row['IdImmagine']."' src='img/trash.png' onclick='DeleteImg(".$row['IdImmagine'].",".$x.")'></img>";	
								}
							}
							AbbattiConnessione($conn);
						?>
						<textarea name="textarea" id="id_new_descrizione" >
							<?php echo $array[2];?>
						</textarea>
					</div>
					<div class="modal-footer">
						<input type="button" name="change" value="Modifica" class="btn btn-primary" onclick="ChangeCoupon(<?php echo ($id_convenzione);?>);" />
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
					</div>
				</form>
			</div>
		</div>
		<?php
			if(isset ($_POST['change'])){
				$var = mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x27\x5C]', '\\\0', $_POST['txtarea']);		
				$var = $_POST['txtarea'];				
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
					<input class="btn btn-primary box3" type="submit" name="feedback" value="VOTA">
				</form>
				
				<?php
					$conn = InstauraConnessione();
					$id_utente = $_COOKIE['auth_betaconvenzioni'];
					$id_utente = Encryption($id_utente, 'd');							
					$sql_control_insert = "SELECT * FROM tbl_feedback WHERE IdUtente = " . $id_utente ." AND IdConvenzione = ". $id_convenzione;
					$result_control_insert = $conn->query($sql_control_insert);
					if ($result_control_insert->num_rows <= 0){
						$already_rating = 0;	
						$vettore_info = mysqli_fetch_row($result_control_insert);
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
								$sql_insert = "INSERT INTO tbl_feedback (IdUtente, IdConvenzione, Voto, Commento)VALUES(" . $id_utente.",".$id_convenzione.",".$voto.",'')";
							
							$conn->query($sql_insert);
															
						}else{
							AbbattiConnessione($conn);
							$conn = InstauraConnessione();
							$sql_update = "UPDATE tbl_feedback SET Voto = ". $voto ." , Commento = '". $commento ."' WHERE IdConvenzione = " .$id_convenzione ." AND IdUtente = " . $id_utente;
							$conn->query($sql_update);
						}
					}
					AbbattiConnessione($conn);
					manage_feedback($id_convenzione, $id_utente, $already_rating, $vettore_info);
				?>
				
				<br/><br/><br/><br/>
				<h3 id="altri_commenti">ALTRI COMMENTI</h3>
				
				<?php
					other_comments($id_convenzione, $id_utente);
				?>
				
				<div style="clear:both;"></div>
			</div>
		</div>
		<script>		
		$('#FileUploader').hide();
		$('#FileUploader2').hide();
			function ShowAttachmentsUploader(){
				$('#FileUploader2').show();
				$('#FileUploader2').addClass('form-control');
			}
			function ShowFileUploader(){
				$('#FileUploader').show();
				$('#FileUploader').addClass('form-control');				
			}
			function DeleteImg(i){
				$('.img_change.'+i).hide();
				$('.trash_img.'+i).hide();
				vett_elimina_images.push(i);
			}
			function DeleteAttachment(i){
				$('.attachments_change.'+i).hide();
				$('.trash_att.'+i).hide();
				vett_elimina_attachments.push(i);
			}
			function DeleteCoupon(id){
				$('#btnDeleteCoupon').data('id', id);
				$('#DeletePopup').modal('show');
			}
			function getHtmlFreeResponse(data){
				data = data.replace(/(\r\n\t|\n|\r\t)/gm,"");
				data = data.replaceAll(" ", "");
				data = data.replaceAll("<html>", "");
				data = data.replaceAll("</html>", "");
				data = data.replaceAll("<head>", "");
				data = data.replaceAll("</head>", "");
				data = data.replaceAll("<body>", "");
				data = data.replaceAll("</body>", "");
				return data;
			}
			String.prototype.replaceAll = function(str1, str2, ignore) 
			{
				return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
			} 
			function ConfirmDeleteCoupon() {
				var couponId = $('#btnDeleteCoupon').data('id'); 

				if(couponId){
					console.log('I got called');

					$.ajax({
						url : 'functions/functions.php?function=DeleteCoupon',
						type : 'POST',
						data : {
							CouponId: couponId
						},
						success : function(data) { 
							data = getHtmlFreeResponse(data);
							console.log(data);
							window.location.href = '/homepage.php';
						},
						error : function(request, error) {
							console.log("Error", request, error);
						}
					});
				}
			}
			function ChangeCoupon(id) {				
				var titolo = $('#id_new_titolo').val();
				var luogo = $('#id_new_luogo').val();
				var scadenza = $('#id_new_scadenza').val();
				var categoria = $('#id_new_categoria').val();
				var descrizione = tinyMCE.get('id_new_descrizione').getContent();
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1; //January is 0!

				var yyyy = today.getFullYear();
				if(dd<10){
					dd='0'+dd;
				} 
				if(mm<10){
					mm='0'+mm;
				} 
				var today =yyyy+mm+dd;
				var confronto = scadenza.replace("-", "");
				confronto = confronto.replace("-", "");
				if(confronto < today){					
					$('#id_new_scadenza').addClass('wrong-form-control');
					var flashInterval = setInterval(function() {
						$('#id_new_scadenza').removeClass('wrong-form-control');
					}, 500);
					return;
				}else{
					$.ajax({
						url : 'functions/functions2.php?function=ChangeCoupon',
						type : 'POST',
						data : {
							titolo: titolo, 
							luogo: luogo, 
							scadenza: scadenza, 
							categoria: categoria, 
							descrizione: descrizione,
							vett_elimina: vett_elimina_images,
							id_convenzione : id,
						},
						success : function(data) { 
							data = getHtmlFreeResponse(data);
							console.log(data);
							var fd = new FormData(document.getElementById('UploadForm'));
							var ins = document.getElementById('FileUploader').files.length;
							for (var x = 0; x < ins; x++) 
								fd.append("FileUploader[]", document.getElementById('FileUploader').files[x]);

							if(ins <= 0)
								fd.append("FileUploader[]", null);

							fd.append("id", id);
							
							console.log(fd);
							var formdata = new FormData(document.getElementById('UploadForm'));
							var number_files = document.getElementById('FileUploader2').files.length;
							for (var x = 0; x < number_files; x++) 
								formdata.append("FileUploader2[]", document.getElementById('FileUploader2').files[x]);

							if(number_files <= 0)
								formdata.append("FileUploader2[]", null);

							formdata.append("id", data);
							$.ajax({
								type: "POST",
								url: "functions/functions2.php?function=AddImagesConvenction",
								data: fd,             
								cache: false,
								contentType: false, //must, tell jQuery not to process the data
								processData: false,
								success: function(data) {
									data = getHtmlFreeResponse(data);
									console.log(data);
									window.location.href = window.location.href;
								}, 
								error: function(error){
									console.log("error", error);
								}
							});
							$.ajax({
								type: "POST",
								url: "functions/functions2.php?function=AddAttachments",
								data: formdata,             
								cache: false,
								contentType: false, //must, tell jQuery not to process the data
								processData: false,
								success: function(data) {
									data = getHtmlFreeResponse(data);
									console.log(JSON.parse(data));
									window.location.href = window.location.href;
								}, 
								error: function(error){
									console.log("error", error);
								}
							});
						},
						error : function(request, error)
						{
							console.log("Error", request, error);
						}
					});
				}
			}
			function ShowChangePopup(){
				$('#modalChangeCoupon').modal('show');
			}
			
		</script>
	</body>
	
</html>
