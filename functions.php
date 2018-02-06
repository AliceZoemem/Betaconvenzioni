<html>
<head>
	<script src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>
</head>
<body>
</body>
</html>

<?php

if(isset($_GET['function'])) {
    $function = $_GET['function'];
    $function();
}

function Encryption($string, $action = 'e' ) { //parametri: string (stringa da criptare/decriptare), action (se action = 'e' --> encrypt (cripta), se action = 'd' --> decryption (decripta))
    // you may change these values to your own
    $secret_key = 'my_simple_secret_key';
    $secret_iv = 'my_simple_secret_iv';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}



function LoadList() { //Parametri da query string: categoria (?), cerca (?), utente (!)
	if(!(isset($_GET['utente']))){
		return;
	}

	$utente = $_GET['utente'];
	$utente = Encryption($utente, 'd');

    $link = mysqli_connect("localhost", "root", "", "db_betaconvenzioni");

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $res = "";
    $today = date('Y-m-d');
	
	$categoria = $_GET['categoria'];
	$cerca = $_GET['cerca'];
	//$query = "SELECT * FROM tbl_convenzioni WHERE (DataScadenza > '$today' OR DataScadenza = '0000-00-00') ";

	
	$query = "SELECT tbl_convenzioni.*, (SELECT sp_CalculateDistance(tbl_convenzioni.Lat, tbl_convenzioni.Lng, tbl_utenti.Lat, tbl_utenti.Lng) AS sp_CalculateDistance) AS Distanza 
			FROM tbl_convenzioni, tbl_utenti 
			WHERE IdUtente = $utente
			ORDER BY Distanza ASC";
	
	if($categoria != ""){
		$query = $query . " AND IdCategoria = '$categoria' ";
	}	
	
	if($cerca != ""){
		$query = $query . " AND (Descrizione LIKE '%$cerca%' OR Titolo LIKE '%$cerca%' ) ";
	}	
	
    if ($result = mysqli_query($link, $query)) {
    
        /* fetch associative array */
        while ($row = mysqli_fetch_array($result)) {
			
            $idConvenzione = $row["IdConvenzione"];
            $titolo = $row['Titolo'];
            $descrizione = $row['Descrizione'];
            $lat = $row['Lat'];
			$lng = $row['Lng'];
            $scadenza = $row['DataScadenza'];
            $idCategoria = $row['IdCategoria'];
            
            /* Percorso immagine */

            $q = mysqli_query($link, "SELECT NomeFile FROM tbl_immagini WHERE IdConvenzione = $idConvenzione ORDER BY Ordine ASC LIMIT 1");
            $row = mysqli_fetch_assoc($q);
            $FileIMG = $row["NomeFile"];

            $url = "";


            $isExternal = false;
            if(strpos($FileIMG, "http") != false)
                $isExternal = true;

            if(!$isExternal)
                $url = "img/convenzioni/" . $FileIMG;
            else
                $url = $FileIMG;

            /* END Percorso immagine */

            
            /* Nome Categoria */

            $q = mysqli_query($link, "SELECT Nome FROM tbl_categorie WHERE IdCategoria = $idConvenzione LIMIT 1");
            $row = mysqli_fetch_assoc($q);
            $NomeCategoria = $row["Nome"];

            /* END Nome Categoria*/



            /* Gestione scadenza */

            if($scadenza == 0000-00-00)
                $scadenza = "Nessuna scadenza";
            else 
                $scadenza = date("d/m/Y", strtotime($scadenza));

            /* END Gestione scadenza */


            $res = $res . " 
                <div class='conv-wrapper' data-conv-target='$idConvenzione'>
                    <div class='conv-cover' style=\"background-image: url('$url')\">
                    </div>
                    <div class='conv-content'>
                        <h2 class='conv-ti+tle'>$titolo</h2>
                        <b class='conv-category'>$NomeCategoria</b><br/>
                        <i class='conv-expiration'>Scadenza: $scadenza</i>
                        <div class='conv-description'>
                            $descrizione
                        </div>
                    </div>
                </div>
            ";

        }

        /* free result set */
        mysqli_free_result($result);
    }

    echo $res;
    
    /* close connection */
    mysqli_close($link);
}


function GetCoordinates($address) { //parametri: indirizzo
	$address = urlencode($address);
	 
	// google map geocode api url
	$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
 
	// get the json response
	$resp_json = file_get_contents($url);
	 
	// decode the json
	$resp = json_decode($resp_json, true);
 
	// response status will be 'OK', if able to geocode given address 
	if($resp['status']=='OK'){	 
		// get the important data
		$lati = $resp['results'][0]['geometry']['location']['lat'];
		$longi = $resp['results'][0]['geometry']['location']['lng'];
		// $formatted_address = $resp['results'][0]['formatted_address'];
		echo $lati ."|". $longi ;	
	}
	else {
		return false;
	}
}


?>
