<?php
	require_once('functions/functions.php');
	$cookie_name= "auth_betaconvenzioni";
	if(isset($_COOKIE[$cookie_name])) 			 				
		header("Location: betaconvenzioni.php");
?>

<html>
<head>
    <script src="js/jquery-3.3.1.min.js"></script> 
    <script src="js/bootstrap/bootstrap.min.js"></script> 
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css" />
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&language=it&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>

    <style>
        
        body{
            padding-top:70px;
        }
       
        .main-form{
            width:60%;
            margin-left:20%;
            text-align:center;
        }
        .main-form .form-control{
            margin-bottom:5px;
        }

        .main-form .logo{
            max-width:50%;
            max-height:250px;
            display:inline-block;
        }
            
        .wrong-form-control{
            border:1px solid #f00;
        }

        /* ~ ~ Responsiveness ~ ~ */
        @media all and (max-width: 600px) {
            .main-form{
                width:90%;
                margin-left:5%;
            }
        }

    </style>

</head>
<body>


    
    <form id="MainForm" class="main-form">
        <img class="logo" src="img/logo.png" />
        <br/><br/><br/>

        <input type="text" class="form-control" id="txtNome" placeholder="Nome*" />
        <input type="text" class="form-control" id="txtCognome" placeholder="Cognome*" />
        <input type="text" class="form-control" id="txtEmail" placeholder="Email*" />
        <input type="password" class="form-control" id="txtPsw" placeholder="Password*" />
        <input type="text" class="form-control" id="txtIndirizzo" placeholder="Indirizzo*" />
        
        <select id="ddlRegione" class="form-control">
            <option value="" disabled selected>Regione</option>
            <?php
                $conn = InstauraConnessione();
                
                /* check connection */
                if (mysqli_connect_errno()) {
                    printf("Connect failed: %s\n", mysqli_connect_error());
                    exit();
                }

                $query = "SELECT * FROM tbl_regioni ORDER BY Nome ASC";

                if ($result = mysqli_query($conn, $query)) {
                
                    /* fetch associative array */
                    while ($row = mysqli_fetch_array($result)) {
                        $id = $row['Id'];
                        $nome = $row['Nome'];

                        echo "<option value=$id>$nome</option>";
                    }
                }
                
                /* close connection */
                AbbattiConnessione($conn);
            ?>
        </select>

        <br/>
        <button class="btn btn-primary" onclick="SignUp();">Registrati</button>
        <br/><br/>
        Sei già registrato? <a class="btn btn-link" href="login.php">Effettua il login</a>
    </form>
</div>


<div id='conv-list' class='conv-list'>
    <!-- Elenco delle convenzioni -->
</div>


<!-- Modal delete alert -->
<div class="modal fade" id="ModalAlert" tabindex="-1" role="dialog" aria-labelledby="titleLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleLabel">Attenzione</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <p id="AlertMessage"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

<script>

$(document).ready(function (){
    var input = document.getElementById('txtIndirizzo');
    var autocomplete = new google.maps.places.Autocomplete(input);
});

$("#MainForm").submit(function(e){
    e.preventDefault();
});

function SignUp() {
    var nome = $('#txtNome').val(); 
    var cognome = $('#txtCognome').val(); 
    var email = $('#txtEmail').val(); 
    var psw = $('#txtPsw').val(); 
    var indirizzo = $('#txtIndirizzo').val(); 
    var regione = $('#ddlRegione').val(); 

    $.ajax({
        url : 'functions/functions.php?function=SignUp',
        type : 'POST',
        data : {
            nome: nome, 
            cognome: cognome, 
            email: email, 
            psw: psw, 
            indirizzo: indirizzo, 
            regione: regione 
        },
        success : function(data) { 
            data = getHtmlFreeResponse(data);
            data = JSON.parse(data);
            console.log(data);

            if(data.code == "200") {
                window.location.href = window.location.href;
            }
            else if(data.message == "no_name"){
                $('#txtNome').addClass('wrong-form-control');
                var flashInterval = setInterval(function() {
                    $('#txtNome').removeClass('wrong-form-control');
                }, 500);
            }
            else if(data.message == "no_surname"){
                $('#txtCognome').addClass('wrong-form-control');
                var flashInterval = setInterval(function() {
                    $('#txtCognome').removeClass('wrong-form-control');
                }, 500);
            }
            else if(data.message == "no_email"){
                $('#txtEmail').addClass('wrong-form-control');
                var flashInterval = setInterval(function() {
                    $('#txtEmail').removeClass('wrong-form-control');
                }, 500);
            }
            else if(data.message == "no_psw"){
                $('#txtPsw').addClass('wrong-form-control');
                var flashInterval = setInterval(function() {
                    $('#txtPsw').removeClass('wrong-form-control');
                }, 500);
            }
            else if(data.message == "no_address"){
                $('#txtIndirizzo').addClass('wrong-form-control');
                var flashInterval = setInterval(function() {
                    $('#txtIndirizzo').removeClass('wrong-form-control');
                }, 500);
            }
            else if(data.message == "no_region"){
                $('#ddlRegione').addClass('wrong-form-control');
                var flashInterval = setInterval(function() {
                    $('#ddlRegione').removeClass('wrong-form-control');
                }, 500);
            }
            else if(data.message == "invalid_email"){
                $('#AlertMessage').text("L'indirizzo e-mail inserito non è valido. Inserire un'e-mail nel formato corretto.");
                $('#ModalAlert').modal('show');
            }
            else if(data.message == "email_exists"){
                $('#AlertMessage').text("L'indirizzo e-mail inserito è già in uso da un altro utente. Sceglierne un altro.");
                $('#ModalAlert').modal('show');
            }
            else {
                $('#AlertMessage').text("Ops, qualcosa è andato storto. Aspettare qualche minuto e riprovare.");
                $('#ModalAlert').modal('show');
            }
        },
        error : function(request, error) {
            console.log("Error", request, error);
        }
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

</script>


</body>
</html>