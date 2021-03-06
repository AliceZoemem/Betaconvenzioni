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
        // "SELECT tbl_convenzioni.*, tbl_indirizzi.Luogo AS CnvLuogo, tbl_indirizzi.Lat AS CnvLat, tbl_indirizzi.Lng AS CnvLng, (SELECT sp_CalculateDistance(tbl_indirizzi.Lat, tbl_indirizzi.Lng, tbl_utenti.Lat, tbl_utenti.Lng) AS sp_CalculateDistance) AS Distanza, 
        // (SELECT sp_CalculateCouponScore(tbl_convenzioni.IdConvenzione) AS sp_CalculateCouponScore) AS Score 
        // FROM tbl_convenzioni, tbl_utenti, tbl_indirizzi
        // WHERE IdUtente = $utente 
        // AND tbl_convenzioni.IdConvenzione IN (SELECT IdConvenzione FROM tbl_indirizzi WHERE tbl_indirizzi.IdRegione = tbl_utenti.IdRegione)
        // AND (tbl_convenzioni.DataScadenza >=  '$today' OR tbl_convenzioni.DataScadenza = '0000-00-00')";

        "SELECT tbl_convenzioni.*, 
		(
            CASE 
         		WHEN tbl_indirizzi.Luogo = '[ovunque]' && tbl_indirizzi.Lat = 0 && tbl_indirizzi.Lng = 0 THEN 0 
         		ELSE
                (SELECT sp_CalculateDistance(
                    tbl_indirizzi.Lat, 
                    tbl_indirizzi.Lng, 
                    (SELECT tbl_utenti.Lat FROM tbl_utenti WHERE tbl_utenti.IdUtente = $utente), 
                    (SELECT tbl_utenti.Lng FROM tbl_utenti WHERE tbl_utenti.IdUtente = $utente)
                )
            )
            END
        ) AS Distanza,  
        (SELECT sp_CalculateCouponScore(tbl_convenzioni.IdConvenzione) AS sp_CalculateCouponScore) AS Score, 
        tbl_indirizzi.Lat AS CnvLat, tbl_indirizzi.Lng AS CnvLng, tbl_indirizzi.Luogo AS CnvLuogo  
        FROM tbl_convenzioni
        INNER JOIN tbl_indirizzi ON tbl_indirizzi.IdConvenzione = tbl_convenzioni.IdConvenzione
        WHERE tbl_indirizzi.IdRegione = (SELECT IdRegione FROM tbl_utenti WHERE tbl_utenti.IdUtente = $utente) ";
        
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
           
            $luogo = $row['CnvLuogo'];
            $lat = $row['CnvLat'];
            $lng = $row['CnvLng'];

            if($luogo != "")
                $displayLoc = $luogo;
            
            $finalPlace = "A <b>$distanza km</b> da $displayLoc";

            if($luogo == "[ovunque]" && $lat == 0 && $lng == 0)
                $finalPlace = "Tutte le sedi";

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

            $q = mysqli_query($conn, "SELECT Nome FROM tbl_categorie WHERE IdCategoria = $idCategoria LIMIT 1");
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
                        <img src='img/trash.png' class='btn-delete-coupon' onclick='DeleteCoupon($idConvenzione)' />
                    </div>
                    <div class='conv-cover' style=\"background-image: url('$url')\" data-conv-target='$idConvenzione'>
                    </div>
                    <div class='conv-content'>
                        <h2 class='conv-ti+tle'>$titolo</h2>
                        <i>$finalPlace</i><br><br>
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
    if(!isset($_POST['categoria'])){
        echo "no_category";
        return;        
    }

    if(!isset($_POST['indirizzi'])){
        echo "no_addresses";
        return; 
    }

    $titolo = $_POST['titolo'];
    $descrizione = $_POST['descrizione'];
    $descrizione = mysqli_real_escape_string($conn, $descrizione);
    $categoria = $_POST['categoria'];
    $today = date("Y-m-d");
    $indirizzi = $_POST['indirizzi'];

    if(isset($_POST['scadenza']))
        $scadenza = $_POST['scadenza'];
    else    
        $scadenza = "0000-00-00";


    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $query = "INSERT INTO tbl_convenzioni (Titolo, Descrizione, IdCategoria, DataCreazione, DataScadenza) VALUES ('$titolo', '$descrizione', $categoria, '$today', '$scadenza')";

    if ($conn->query($query) === TRUE) {
        $sql = "SELECT @@IDENTITY AS Id";

        $q = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($q);

        $identity = $row['Id'];

        $sql = "INSERT INTO tbl_indirizzi (IdRegione, IdConvenzione, Luogo, Lat, Lng) VALUES ";
        $valuesSql = "";
        $count = 0;
        foreach ($indirizzi as $key => $value) {
            $regione = $value['regione'];
            $luogo = $value['indirizzo'];
            if($luogo != ""){
                $count++;
                $coordinates = GetCoordinates($luogo);
                $lat = 0;
                $lng = 0;
                if($coordinates){
                    $lat = explode("|", $coordinates)[0];
                    $lng = explode("|", $coordinates)[1];
                }
                if($valuesSql == "")
                    $valuesSql = $valuesSql . " ($regione, $identity, '$luogo', $lat, $lng)";  
                else    
                    $valuesSql = $valuesSql . ", ($regione, $identity, '$luogo', $lat, $lng)";            
            }
        }

        if($count > 0){
            $sql = $sql . $valuesSql;
            if ($conn->query($sql) === TRUE) {
                //Okay.
            }
            else {
                echo "Error: " . $query . "<br>" . $conn->error;
            }
        }

        echo $identity;
    }
    else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }

    /* close connection */
    AbbattiConnessione($conn);
    return;
}

function AttachImages() {
    $res = [];
    if(isset($_FILES['FileUploader'])){
        $conn = InstauraConnessione();
        
        $id = $_POST['id'];

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
    }
    if(isset($_FILES['Attachments'])){
        $conn = InstauraConnessione();
        
        $id = $_POST['id'];

        $uploaddir = '../allegati/';
        $length = count($_FILES['Attachments']['name']);
        
        for($i = 0; $i < $length; $i++) {
            $tmpname = basename($_FILES['Attachments']['tmp_name'][$i]);
            $path_parts = pathinfo($tmpname);
            
            $filename = $path_parts['filename'];

            $path_parts = pathinfo($_FILES['Attachments']['name'][$i]);
            $filename = $filename . "." . $path_parts['extension'];

            $uploadfile = $uploaddir . $filename;

            if (move_uploaded_file($_FILES['Attachments']['tmp_name'][$i], $uploadfile)) {
        
                $sql = "INSERT INTO tbl_allegati (NomeFile, IdConvenzione) VALUES ('$filename', $id)";

                if ($conn->query($sql) === TRUE) {
                    $res = array_push_assoc($res, $i, array('code' => '200', 'file' => $_FILES['Attachments']['name'][$i], 'query' => '200'));
                } else {
                    $res = array_push_assoc($res, $i, array('code' => '200', 'file' => $_FILES['Attachments']['name'][$i], 'query' => '500'));
                }

            } else {
                $res = array_push_assoc($res, $i, array('code' => '500', 'file' => $_FILES['Attachments']['name'][$i]));
            }
        }

        AbbattiConnessione($conn);
    }

    if(!(isset($_FILES['Attachments'])) && !(isset($_FILES['FileUploader']))){
        $res = array('code' => '404', 'file' => '', 'message' => 'no_files');
    }

    echo json_encode($res);
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

function AddCategory(){
    if(isset($_POST['Categoria'])){
        $conn = InstauraConnessione();

        $categoria = $_POST['Categoria'];
        $sql = "INSERT INTO tbl_categorie (Nome) VALUES ('$categoria') ";

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
        $res = array('code' => '404', 'message' => 'no_category');
        echo json_encode($res);
    }
}


function EditCategory(){
    if(isset($_POST['Categoria']) && isset($_POST['Id'])){
        $conn = InstauraConnessione();

        $id = $_POST['Id'];
        $nome = $_POST['Categoria'];
        
        $sql = "UPDATE tbl_categorie SET Nome = '$nome' WHERE IdCategoria = $id ";

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
        $res = array('code' => '404', 'message' => 'no_category');
        echo json_encode($res);
    }
}


function DeleteCategory(){
    if(isset($_POST['Categoria'])){
        $conn = InstauraConnessione();

        $categoria = $_POST['Categoria'];
        $sql = "DELETE FROM tbl_categorie WHERE IdCategoria = $categoria ";

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
        $res = array('code' => '404', 'message' => 'no_category');
        echo json_encode($res);
    }
}

function GetUserType() {
    if(isset($_GET['user'])){
        $utente = $_GET['user'];
        $utente = Encryption($utente, 'd');

        
        $conn = InstauraConnessione();
        
        $sql = "SELECT IsAmministratore FROM tbl_utenti WHERE IdUtente = $utente";

        if ($result = mysqli_query($conn, $sql)) {
            if ($row = mysqli_fetch_array($result)) {
                $admin = $row["IsAmministratore"];
                $adminBool = $admin == 1 ? true : false;
                $res = array('admin' => $adminBool);
                echo json_encode($res);
            }
            else{
                $res = array('admin' => false);
                echo json_encode($res);
            }
        }
        else{
            $res = array('admin' => false);
            echo json_encode($res);
        }

        AbbattiConnessione($conn);
    }
    else{
        $res = array('admin' => false);
        echo json_encode($res);
    }
}


function GetProfileInfo(){
    if(isset($_GET['user'])){
        $utente = $_GET['user'];
        $utente = Encryption($utente, 'd');
        
        $conn = InstauraConnessione();

        $sql = "SELECT Nome, Cognome, IdRegione FROM tbl_utenti WHERE IdUtente = $utente";
        if ($result = mysqli_query($conn, $sql)) {
            if ($row = mysqli_fetch_array($result)) {
                $nome = $row["Nome"];
                $cognome = $row["Cognome"];
                $regione = $row["IdRegione"];
                
                $res = array('nome' => $nome, 'cognome' => $cognome, 'regione' => $regione);
                echo json_encode($res);
            }
            else{
                $res = array('nome' => '', 'cognome' => '', 'regione' => '');
                echo json_encode($res);
            }
        }
        else{
            $res = array('nome' => '', 'cognome' => '', 'regione' => '');
            echo json_encode($res);
        }

        AbbattiConnessione($conn);
    }
    else{
        $res = array('nome' => '', 'cognome' => '', 'regione' => '');
        echo json_encode($res);
    }
}

function UpdateProfileInfo(){
    if(isset($_POST['user'])){
        $utente = $_POST['user'];
        $utente = Encryption($utente, 'd');

        $nome = "";
        $cognome = "";
        $psw = "";
        $indirizzo = "";
        $regione = "";

        if(isset($_POST['nome']))
            $nome = $_POST['nome'];

        if(isset($_POST['cognome']))
            $cognome = $_POST['cognome'];
    
        if(isset($_POST['psw']))
            $psw = $_POST['psw'];

        if(isset($_POST['indirizzo']))
            $indirizzo = $_POST['indirizzo'];
            
        if(isset($_POST['regione']))
            $regione = $_POST['regione'];

        $conn = InstauraConnessione();

        $sql = "UPDATE tbl_utenti SET IdUtente = $utente";

        if($nome != "")
            $sql = $sql . " , Nome = '$nome' ";

        if($cognome != "")
            $sql = $sql . " , Cognome = '$cognome' ";
            
        if($psw != ""){
            $psw = md5($psw);
            $sql = $sql . " , Password = '$psw' ";
        }
        
        if($indirizzo != ""){
            $coordinates = GetCoordinates($indirizzo);
            if($coordinates){
                $lat = explode("|", $coordinates)[0];
                $lng = explode("|", $coordinates)[1];
            }
            else{
                $lat = 0;
                $lng = 0;
            }

            $sql = $sql . " , Lat = '$lat', Lng = '$lng' ";
        }

        if($regione != "")
            $sql = $sql . " , IdRegione = '$regione' ";
      

        $sql = $sql . " WHERE IdUtente = $utente ";
        
        if ($conn->query($sql) === TRUE) {
            $res = array('code' => '200', 'message' => 'success');
            echo json_encode($res);
        } 
        else {
            $res = array('code' => '500', 'message' => 'query_failed');
            echo json_encode($res);
        }

        AbbattiConnessione($conn);
    }
    else{
        $res = array('code' => '404', 'message' => 'no_user');
        echo json_encode($res);
    }
}

function SignUp(){
    $nome = "";
    $cognome = "";
    $email = "";
    $psw = "";
    $indirizzo = "";
    $regione = "";

    if(isset($_POST['nome']))
        $nome = $_POST['nome'];

    if(isset($_POST['cognome']))
        $cognome = $_POST['cognome'];

    if(isset($_POST['email']))
        $email = $_POST['email'];
    
    if(isset($_POST['psw']))
        $psw = $_POST['psw'];

    if(isset($_POST['indirizzo']))
        $indirizzo = $_POST['indirizzo'];

        
    if(isset($_POST['regione']))
        $regione = $_POST['regione'];
        
    if($nome == ""){
        $res = array('code' => '404', 'message' => 'no_name');
        echo json_encode($res);
        return;
    }

    if($cognome == ""){
        $res = array('code' => '404', 'message' => 'no_surname');
        echo json_encode($res);
        return;
    }

    if($email == ""){
        $res = array('code' => '404', 'message' => 'no_email');
        echo json_encode($res);
        return;
    }

    if($psw == ""){
        $res = array('code' => '404', 'message' => 'no_psw');
        echo json_encode($res);
        return;
    }

    if($indirizzo == ""){
        $res = array('code' => '404', 'message' => 'no_address');
        echo json_encode($res);
        return;
    }

    if($regione == ""){
        $res = array('code' => '404', 'message' => 'no_region');
        echo json_encode($res);
        return;
    }

    //Ottengo coordinate
    $coordinates = GetCoordinates($indirizzo);
    $lat = 0;
    $lng = 0;
    if($coordinates){
        $lat = explode("|", $coordinates)[0];
        $lng = explode("|", $coordinates)[1];
    }
    
    //Cripto Psw
    $psw = md5($psw);
        
    //Controllo email format
    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
    if (!(preg_match($pattern, $email) === 1)) {
        $res = array('code' => '500', 'message' => 'invalid_email');
        echo json_encode($res);
        return;
    }


    $conn = InstauraConnessione();
    
    //Controllo email redundance
    $sql = "SELECT IdUtente FROM tbl_utenti WHERE Email = '$email' ";
    if ($result = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_array($result)) {
            $res = array('code' => '409', 'message' => 'email_exists');
            echo json_encode($res);
            return;
        }
    }

    //Inserimento
    $sql = "INSERT INTO tbl_utenti (Nome, Cognome, Email, Password, Lat, Lng, IdRegione, IsAmministratore, Attivo) VALUES ('$nome', '$cognome', '$email', '$psw', $lat, $lng, $regione, 0, 1)";

    if ($conn->query($sql) === TRUE) {
        $res = array('code' => '200', 'message' => 'success');
        echo json_encode($res);
    } 
    else {
        $res = array('code' => '500', 'message' => 'query_error');
        echo json_encode($res);
    }


    $sql = "SELECT IdUtente FROM tbl_utenti WHERE Email = '$email'";

    $q = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($q);

    $id = $row['IdUtente'];
        
    $cookie_value = Encryption($id, 'e');
    $cookie_name = 'auth_betaconvenzioni';
    setcookie ($cookie_name, $cookie_value, time() + (86400 * 30), '/');

    AbbattiConnessione($conn);
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
