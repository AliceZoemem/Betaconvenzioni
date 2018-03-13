<?php
	require_once('functions/functions.php');
	if(!(isset($_COOKIE['auth_betaconvenzioni']))){
		echo "<script>window.location.href='login.php'</script>";
	}
?>

<html>
<head>

<script src="js/jquery-3.3.1.min.js"></script> 
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&language=it&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>

<style>

    .filters-bar{
        width:100%;
        height:50px;
        padding:25px 0;
        margin:0;
        display:inline-block;
        text-align:center;
    }

    .filters-controls{
        width:60%;
        display:inline-block;
    }

    .filters-controls select, .filters-bar input[type='text']{
        padding:5px 7px;
        border-radius:3px;
        border:1px solid #555;
        width:31%;
        max-width:100%;
    }

    .filter-buttons{
        width:58%;
        text-align:right;
        display:inline-block;
        padding-right:2%;
        padding-top:20px;
    }

    .conv-list{
        width:80%;
        height:100%;
        text-align:center;
        padding:0 10%;
    }

    .conv-wrapper{
        width:90%;
        min-height:30%;
        height:auto;
        margin-bottom:20px;
        padding:50px;
        transition:0.2s;
    }

    .conv-wrapper:hover{
        background-color:#ddd;
        cursor:pointer;
    }

    .conv-cover{
        background-position-x:center;   
        background-position-y:center;
        background-size:cover;   
        background-repeat:no-repeat;
        width:20%;
        padding-top:20%;
        display:inline-block;
        position:relative;
        left:0;
        margin:0;
        padding-bottom:0;
        padding-left:0;
        padding-right:0;
        border:1px solid #333;
    }

    .conv-content{
        width:60%;
        display:inline-block;
        padding:0;
        vertical-align:top;
        text-align:left;
        overflow-y:hidden;
        max-height:100%;
        padding-left:1%;
    }

    .conv-expiration{
        color:#555;
    }

    .conv-description{
        padding-top:20px;
    }
	
	.right{
		float:right;
		margin: 1% 7%;
	}

</style>

</head>
<body>

<h1 style="display:inline">Convenzioni</h1>
<button type="button" class="right" onclick="window.location.href='logout.php'">Logout</button>

<div class='filters-bar'>

    <form method='get' id="MainForm" action='homepage.php'>
        <div class='filters-controls'>

            <input type='text' id='txtLocalita' name='localita' placeholder='Località...' />

            <select id='ddlCategoria' name='categoria' >
                <option value=''>Categoria...</option>

                <?php
                    $conn = InstauraConnessione();
					
                    /* check connection */
                    if (mysqli_connect_errno()) {
                        printf("Connect failed: %s\n", mysqli_connect_error());
                        exit();
                    }

                    $query = "SELECT * FROM tbl_categorie ORDER BY Nome ASC";

                    if ($result = mysqli_query($conn, $query)) {
                    
                        /* fetch associative array */
                        while ($row = mysqli_fetch_array($result)) {
                            $idCategoria = $row['IdCategoria'];
                            $nome = $row['Nome'];

                            echo "<option value='$idCategoria'>$nome</option>";
                        }
                    }
					
					/* close connection */
					AbbattiConnessione($conn);
                ?>
            </select>

            <input type='text' id='txtRicerca' name='cerca' placeholder='Cerca...' />

        </div>

        <div class='filter-buttons'>
            <input type='submit' id='btnCerca' value='Cerca' onclick="Cerca();" />
            <input type='submit' id='btnRimuoviFiltri' value='Rimuovi filtri' onclick="RimuoviFiltri();" />
        </div>
    </form>
	
	
	<?php
		$conn = InstauraConnessione($conn);

		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		$query = "SELECT sp_CalculateDistance (45.074902, 7.589834, 45.075410, 7.594630) AS sp_CalculateDistance";
		
		if ($result = mysqli_query($conn, $query)) {
                    
			/* fetch associative array */
			while ($row = mysqli_fetch_array($result)) {
				// echo "La distanza tra i due punti è " . $row[0] . " km";
			}
		}
		
		/* close connection */
		AbbattiConnessione($conn);
	?>
	
</div>


<div id='conv-list' class='conv-list'>
    <!-- Elenco delle convenzioni -->
</div>


<script>

$(document).ready(function (){
    var input = document.getElementById('txtLocalita');
    var autocomplete = new google.maps.places.Autocomplete(input);

    var utente = getCookie('auth_betaconvenzioni');
    var url = "functions/functions.php?function=LoadList&utente=" + utente;
    
    $("#conv-list").load(url, function() {
        AdjustStyle();
        $('.conv-wrapper').click(function() {
            var targetCoupon = $(this).data('conv-target');
            window.location = "convenzione.php?convenzione=" + targetCoupon;
        });
    });
});

$("#MainForm").submit(function(e){
    e.preventDefault();
});


function Cerca(){
    var utente = getCookie('auth_betaconvenzioni');
    var url = "functions/functions.php?function=LoadList&utente=" + utente;

    if($('#txtRicerca').val()){
        url += "&cerca=" + $('#txtRicerca').val();
    }

    if($('#ddlCategoria').val()){
        url += "&categoria=" + $('#ddlCategoria').val();
    }

    if($('#txtLocalita').val()){
        var localita = encodeURI($('#txtLocalita').val());
        url += "&localita=" + localita;
    }
    
    $("#conv-list").load(url, function(response) {
        AdjustStyle();
        $('.conv-wrapper').click(function() {
            var targetCoupon = $(this).data('conv-target');
            window.location = "convenzione.php?convenzione=" + targetCoupon;
        });
    });
}

function RimuoviFiltri(){
    var utente = getCookie('auth_betaconvenzioni');
    var url = "functions/functions.php?function=LoadList&utente=" + utente;
    
    $("#conv-list").load(url, function() {
        AdjustStyle();

        $('#ddlCategoria').val('');
        $('#txtRicerca').val('');
        $('#txtLocalita').val('');

        $('.conv-wrapper').click(function() {
            var targetCoupon = $(this).data('conv-target');
            window.location = "convenzione.php?convenzione=" + targetCoupon;
        });
    });
}


function AdjustStyle(){
    $('.conv-description').each(function() {
        $(this).html($(this).text());
    });
}


function getParameterByName(name){
    var regexS = "[\\?&]"+name+"=([^&#]*)", 
    regex = new RegExp( regexS ),
    results = regex.exec( window.location.search );
    if( results == null ) {
        return "";
    } 
    else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}


</script>



</body>
</html>