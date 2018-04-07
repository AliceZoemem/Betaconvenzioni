<html>
<head>

</head>
<body>
</body>
</html>

<?php

if(isset($_GET['function'])) {
    $function = $_GET['function'];
    $function();
}

if(isset($_POST['function'])) {
    $function = $_POST['function'];
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

    $query = 
            "SELECT tbl_convenzioni.*, (SELECT sp_CalculateDistance(tbl_convenzioni.Lat, tbl_convenzioni.Lng, tbl_utenti.Lat, tbl_utenti.Lng) AS sp_CalculateDistance) AS Distanza, 
            (SELECT sp_CalculateCouponScore(tbl_convenzioni.IdConvenzione) AS sp_CalculateCouponScore) AS Score 
            FROM tbl_convenzioni, tbl_utenti 
            WHERE IdUtente = $utente 
            AND (tbl_convenzioni.DataScadenza >=  '$today' OR tbl_convenzioni.DataScadenza = '0000-00-00')";
			
	if($categoria != ""){
		$query = $query . " AND IdCategoria = '$categoria' ";
	}	
	
	if($cerca != ""){
		$query = $query . " AND (Descrizione LIKE '%$cerca%' OR Titolo LIKE '%$cerca%' ) ";
    }	
    
    if($localita != ""){
        $coordinates = GetCoordinates($localita);

        if($coordinates){
            $lat = explode("|", $coordinates)[0];
            $lng = explode("|", $coordinates)[1];
        }
        else{
            $lat = 0;
            $lng = 0;
        }

		$query = str_replace("tbl_utenti.Lat", $lat, $query);
		$query = str_replace("tbl_utenti.Lng", $lng, $query);
    }	
    
    if($localita != ""){
        $query = $query . " HAVING Distanza < 10";
    }

    if(isset($_GET['order_by'])){
        $orderBy = $_GET['order_by'];

        if($orderBy == "rating")
            $query = $query . " ORDER BY Score DESC";
    
        if($orderBy == "expiry")
            $query = $query . " ORDER BY DataCreazione DESC";
            
        if($orderBy == "distance")
            $query = $query . " ORDER BY Distanza ASC";
    }
    else {
        $query = $query . " ORDER BY Score DESC";
    }

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
            $score = $row["Score"];

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
                <div class='conv-wrapper'>
                    <div class='conv-delete-bar'>
                        <img src='img/trash.png' onclick='DeleteCoupon($idConvenzione)' />
                    </div>
                    <div class='conv-cover' style=\"background-image: url('$url')\" data-conv-target='$idConvenzione'>
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

function AddCoupon(){
    $conn = InstauraConnessione();
    
    if(!isset($_POST['titolo'])){
        echo "no_title";
        return;
    }
    if(!isset($_POST['descrizione'])){
        echo "no_description";    
        return;
    }
    if(!isset($_POST['luogo'])){
        echo "no_place";
        return;
    }
    if(!isset($_POST['categoria'])){
        echo "no_category";
        return;        
    }

    $titolo = $_POST['titolo'];
    $descrizione = $_POST['descrizione'];
    $descrizione = mysqli_real_escape_string($conn, $descrizione);
    $luogo = $_POST['luogo'];
    $categoria = $_POST['categoria'];
    $today = date("Y-m-d");
    $coordinates = GetCoordinates($luogo);

    if($coordinates){
        $lat = explode("|", $coordinates)[0];
        $lng = explode("|", $coordinates)[1];
    }
    else{
        $lat = 0;
        $lng = 0;
    }

    if(isset($_POST['scadenza']))
        $scadenza = $_POST['scadenza'];
    else    
        $scadenza = "0000-00-00";


    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $query = "INSERT INTO tbl_convenzioni (Titolo, Descrizione, Luogo, IdCategoria, Lat, Lng, DataCreazione, DataScadenza) VALUES ('$titolo', '$descrizione', '$luogo', $categoria, $lat, $lng, '$today', '$scadenza')";

    if ($conn->query($query) === TRUE) {
        $sql = "SELECT @@IDENTITY AS Id";

        $q = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($q);

        $identity = $row['Id'];
        echo $identity;

    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }

    /* close connection */
    AbbattiConnessione($conn);
    return;
}

function AttachImages() {
    if(isset($_FILES['FileUploader'])){
        $conn = InstauraConnessione();
        
        $id = $_POST['id'];
        $res = [];

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

function DeleteCoupon(){
    if(isset($_POST['CouponId'])){
        $conn = InstauraConnessione();

        $id = $_POST['CouponId'];
        $sql = "DELETE FROM tbl_convenzioni WHERE IdConvenzione = '$id'";

        if ($conn->query($sql) === TRUE) {
            $res = array('code' => '200', 'message' => 'success');
            echo json_encode($res);
        } else {
            $res = array('code' => '500', 'message' => 'query_failed');
            echo json_encode($res);
        }

        AbbattiConnessione($conn);
    }
    else{
        $res = array('code' => '404', 'message' => 'no_coupon');
        echo json_encode($res);
    }
}


function GetCoordinates($address) { //parametri: indirizzo
    header('Content-Type: text/plain');
    $address = str_ireplace(",", "", $address);
    $address = urlencode($address);

	// google map geocode api url
	$url = "https://maps.google.com/maps/api/geocode/json?sensor=false&language=it&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38&address=$address";

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

function array_push_assoc($array, $key, $value){
    $array[$key] = $value;
    return $array;
}

function adjust_file_name($filename){
    $filename = preg_replace( '/[^a-z0-9]+/', '-', strtolower($filename));
    return $filename;
}


?>
