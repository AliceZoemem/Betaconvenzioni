<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Prova</title>
    <script src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>
</head>
<body>
	<form method="post" action="index.php">  
		<input type="text" name="indirizzo" value="" placeholder="Indirizzo">
		<input type="submit" name="submit" value="Login">		
	</form>
    

</body>
</html>
<?php
		
	if ( isset( $_POST['submit'] )){
		$address = $_POST['indirizzo'];
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
		}else{
			return false;
		}
	}

?>