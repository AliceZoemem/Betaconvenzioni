<html>
<head>
	<!-- <script src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script> -->
</head>
<body>
</body>
</html>

<?php

if(isset($_GET['function'])) {
    $function = $_GET['function'];
    $function();
}

function InstauraConnessione(){
	$servername = "localhost";
	$db_username = "root";
	$db_pw = "";
	$db_name = "db_betaconvenzioni";
	$conn = new mysqli($servername, $db_username, $db_pw, $db_name);
	return $conn;
}	

function AbbattiConnessione($conn){
	mysqli_close($conn);
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
    $conn = InstauraConnessione();

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $res = "";
    $today = date('Y-m-d');

    $categoria = "";
    $cerca = "";
    $localita = "";

    if(isset($_GET['categoria']))
	    $categoria = $_GET['categoria'];
    
    if(isset($_GET['cerca']))
        $cerca = $_GET['cerca'];

    if(isset($_GET['localita']))
        $localita = $_GET['localita'];

	$query = "SELECT tbl_convenzioni.*, (SELECT sp_CalculateDistance(tbl_convenzioni.Lat, tbl_convenzioni.Lng, tbl_utenti.Lat, tbl_utenti.Lng) AS sp_CalculateDistance) AS Distanza 
			FROM tbl_convenzioni, tbl_utenti 
			WHERE IdUtente = $utente 
            AND (tbl_convenzioni.DataScadenza >=  '$today' OR tbl_convenzioni.DataScadenza = '0000-00-00') ";
			
	if($categoria != ""){
		$query = $query . " AND IdCategoria = '$categoria' ";
	}	
	
	if($cerca != ""){
		$query = $query . " AND (Descrizione LIKE '%$cerca%' OR Titolo LIKE '%$cerca%' ) ";
    }	
    
    if($localita != ""){
        $coordinates = GetCoordinates($localita);

        $lat = explode("|", $coordinates)[0];
        $lng = explode("|", $coordinates)[1];
        
		$query = str_replace("tbl_utenti.Lat", $lat, $query);
		$query = str_replace("tbl_utenti.Lng", $lng, $query);
    }	
    
    if($localita != ""){
        $query = $query . " HAVING Distanza < 5";
    }

    $query = $query . " ORDER BY Distanza ASC";

    if ($result = mysqli_query($conn, $query)) {
    
        /* fetch associative array */
        while ($row = mysqli_fetch_array($result)) {
			
            $idConvenzione = $row["IdConvenzione"];
            $titolo = $row['Titolo'];
            $descrizione = $row['Descrizione'];
            $lat = $row['Lat'];
			$lng = $row['Lng'];
            $scadenza = $row['DataScadenza'];
            $idCategoria = $row['IdCategoria'];
            $distanza = $row['Distanza'];
            $distanza = round($distanza, 2);
            $displayLoc = "te";
            if($localita != "")
                $displayLoc = $localita;
            
            /* Percorso immagine */

            $q = mysqli_query($conn, "SELECT NomeFile FROM tbl_immagini WHERE IdConvenzione = $idConvenzione ORDER BY Ordine ASC LIMIT 1");
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

            $q = mysqli_query($conn, "SELECT Nome FROM tbl_categorie WHERE IdCategoria = $idConvenzione LIMIT 1");
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
                        <i>A <b>$distanza km</b> da $displayLoc</i><br><br>
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
	AbbattiConnessione($conn);
}


function GetCoordinates($address) { //parametri: indirizzo
    header('Content-Type: text/plain');
    $address = urlencode($address);
	// google map geocode api url
	$url = "https://maps.google.com/maps/api/geocode/json?sensor=false&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38&address=$address";
 
	// get the json response
	$resp_json = file_get_contents($url);
	 
	// decode the json
	$resp = json_decode($resp_json, true);
 
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){	 
		// get the important data
		$lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        
		return $lati ."|". $longi ;	
	}
	else {
		return false;
	}
}


?>
