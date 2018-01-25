<?php
	// session_start();
	// $id_convenzione = $_GET['convenzione'];
	$id_convenzione = 1;
	$servername = "localhost";
	$db_username = "root";
	$db_pw = "";
	$db_name = "db_betaconvenzioni";
	$conn = new mysqli($servername, $db_username, $db_pw, $db_name);
	$sql = "SELECT * FROM tbl_convenzioni WHERE IdConvenzione = " . $id_convenzione;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		foreach ($result as $row) {
			// print_r ($row['Titolo']);
			echo "<script>
				document.getElementById('descrizione').innerHTML = '" .$row['Descrizione'] ."';
				document.getElementById('title_convenction').innerHTML = '" .$row['Titolo']."';
			</script>";
		}
	}
	
?>	
<!DOCTYPE HTML>  
<html lang="it">
<html>
	<head>
		<title>Betacom_Convenzione</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="/css/css-stars.css">
		<script src="/js/jquery/jquery.min.js"></script>
		<script src="/js/jquery/jquery-1.11.2.min.js"></script> 
		<script src="/js/jquery/jquery.barrating.js"></script>
		<script src="/js/examples.js"></script>	
		<link rel="stylesheet" type="text/css" href="/css/stile.css">		
		<link href="/css/bootstrap.css" rel="stylesheet" type="text/css">
		<script src="/js/bootstrap.js"></script>
		<script>$('.carousel').carousel();</script>
		<link rel="stylesheet" href="/css/bootstrap.min.css">		
		<script src="/js/bootstrap.min.js"></script>
		
		
		
		<style>
			<!--.carousel-item{
				width:50%;
				height:400px;
				background-position-x:center;
				background-position-y:center;
				background-repeat:no-repeat;
				background-size:cover;
			}-->
		</style>
		
		
		
		
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6" id="contenuto_img">
					<!--<img id="img_convenction" class="slider" src="/img/e.png"/>-->
					<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
						<ol class="carousel-indicators">
							<?php
								// $sql_immagini = "SELECT * FROM tbl_immagini WHERE IdConvenzione = " . $id_convenzione;
								// $result_immagini = $conn->query($sql_immagini);

								// foreach ($result_immagini->result() as $row)
								// {
									// if($row != 0)
										// echo '<li data-target="#carouselExampleIndicators" data-slide-to="' .$row. '"></li>';	
									// else
										// echo '<li data-target="#carouselExampleIndicators" data-slide-to="' .$row. '" class="active"></li>'
								// }
								
							?>
							<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
							<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
							<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
						</ol>
						<div class="carousel-inner">
							<div class="carousel-item active">
								<img class="d-block w-100" src="/img/i1.jpg" alt="First slide">
							</div>
							<div class="carousel-item">
								<img class="d-block w-100" src="/img/i2.jpg" alt="Second slide">
							</div>
							<div class="carousel-item">
								<img class="d-block w-100" src="/img/i3.jpg" alt="Second slide">
							</div>
							<div class="carousel-item">
								<img class="d-block w-100" src="/img/i4.jpg" alt="Second slide">
							</div>
							
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
					<div class="stars stars-example-css">
						<select id="example-css" name="rating" autocomplete="off">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select> 
					</div>  
				</div>
			</div>
			<div class="row">
				<div class="col-4 col-md-auto" id="contenuto_allegati">
					<ul id="elenco_allegati">
						<?php
							// $sql_allegati = "SELECT * FROM tbl_allegati WHERE IdConvenzione = " . $id_convenzione;
							// $result_allegati = $conn->query($sql_allegati);

							// foreach ($result_allegati->result() as $row)
							// {
								// echo '<li><a href="/allegati/'. $row->NomeFile .'">'. $row->NomeFile .'</a></li>';								
							// }
							
						?>

						
					</ul>
				</div>
			</div>
		</div>	
	</body>
</html>
