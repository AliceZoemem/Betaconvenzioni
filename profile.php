<?php
	require_once('functions/functions.php');
	if(!(isset($_COOKIE['auth_betaconvenzioni']))){
		echo "<script>window.location.href='login.php'</script>";
	}
?>

<html>
<head>

<meta charset="UTF-8">
<script src="js/jquery-3.3.1.min.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<link rel="stylesheet" href="css/bootstrap.min.css" />
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&language=it&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>


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

    .add-bar{
        width:100%;
        text-align:center;
        display:block;
        height:30px;
    }

    .add-bar img{
        max-height:100%;
        -webkit-transition: 0.2s;
        -moz-transition: 0.2s;
        transition: 0.2s;
    }

    .add-bar img:hover{
        cursor:pointer;
        -ms-transform: scale(1.1); /* IE 9 */
        -webkit-transform: scale(1.1); /* Safari */
        transform: scale(1.1);
    }

    .mce-notification-inner, #mceu_31{
        display:none;
    }

    .new-form .form-control{
        margin-bottom:5px;
    }

    .wrong-form-control{
        border:1px solid #f00;
    }

    .conv-delete-bar{
        width:100%;
        height:40px;
        text-align:right;
        padding-top:10px;
    }

    .conv-delete-bar img{
        display:inline-block;
        max-height:100%;
        transition:0.2s;
    }

    .conv-delete-bar img:hover{
        cursor:pointer;
        -ms-transform: scale(1.1); /* IE 9 */
        -webkit-transform: scale(1.1); /* Safari */
        transform: scale(1.1);
    }

</style>

</head>
<body>

<h1 style="display:inline">Profilo</h1>
    <button type="button" class="right" onclick="window.location.href='logout.php'">Logout</button>

    <div class="form-wrapper" style="width:60%;margin-left:20%;">
        Nome<br/>
        <input type="text" id="txtNome" class="form-control" />
        <br/>
        Cognome<br/>
        <input type="text" id="txtCognome" class="form-control" />
        <br/>
        Password<br/>
        <input type="password" id="txtPsw" class="form-control" />
        <br/>      
        Indirizzo<br/>  
        <input type="text" id="txtIndirizzo" class="form-control" />
        <br/><br/>
        <div id="startEditBar">
            <button class="btn btn-primary" onclick="StartEdit();">Modifica</button>
        </div>
        <div id="editBar">
            <button class="btn btn-success" onclick="ConfirmEdit();">Conferma</button>
            <button class="btn btn-secondary" onclick="CancelEdit();">Annulla</button>
        </div>
    </div>


<!-- Modal -->
<div class="modal fade" id="EditMessagePopup" tabindex="-1" role="dialog" aria-labelledby="titleLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleLabel">Esito modifica informazioni</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="lblEditMessage"></p>
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

    var utente = getCookie('auth_betaconvenzioni');

    $('.form-wrapper .form-control').each(function(){
        $(this).addClass('disabled');
        $(this).attr('disabled', 'disabled');
    });

    $('#editBar').hide();

    GetProfileInfo();
});

function GetProfileInfo(){
    var utente = getCookie('auth_betaconvenzioni');
    
    $.ajax({
        url : 'functions/functions.php?function=GetProfileInfo',
        type : 'GET',
        data : {
            user: utente
        },
        success : function(data) { 
            data = getHtmlFreeResponse(data);
            data = JSON.parse(data);
            console.log(data);

            $("#txtNome").val(data.nome);
            $("#txtCognome").val(data.cognome);
        },
        error : function(request, error)
        {
            console.log("Error", request, error);
        }
    });
}

function StartEdit(){
    $('#startEditBar').hide();
    $('#editBar').slideDown();
    $('.form-wrapper .form-control').each(function(){
        $(this).removeClass('disabled');
        $(this).removeAttr('disabled');
    });
}

function ConfirmEdit(){
    var nome = $('#txtNome').val();
    var cognome = $('#txtCognome').val();
    var psw = $('#txtPsw').val();
    var indirizzo = $('#txtIndirizzo').val();
    var utente = getCookie('auth_betaconvenzioni');

    $.ajax({
        url : 'functions/functions.php?function=UpdateProfileInfo',
        type : 'POST',
        data : {
            nome: nome, 
            cognome: cognome, 
            psw: psw, 
            indirizzo: indirizzo, 
            user: utente
        },
        success : function(data) { 
            data = getHtmlFreeResponse(data);
            data = JSON.parse(data);
            console.log(data);

            if(data.code == '200'){
                $('#lblEditMessage').html('Modifica effettuata!');
                $('#EditMessagePopup').modal('show');
            }
            else{
                $('#lblEditMessage').html('Ops! Qualcosa è andato storto, ricaricare la pagina e riprovare.');
                $('#EditMessagePopup').modal('show');
            }
        },
        error : function(request, error)
        {
            console.log("Error", request, error);
        }
    });
}


function CancelEdit(){
    $('#editBar').hide();
    $('#startEditBar').slideDown();
    $('.form-wrapper .form-control').each(function(){
        $(this).addClass('disabled');
        $(this).attr('disabled', 'disabled');
    });
}

function AddCoupon() {
    var titolo = $('#txtTitolo').val();
    var luogo = $('#txtLuogo').val();
    var scadenza = $('#txtScadenza').val();
    var categoria = $('#ddlNewCategoria').val();
    var descrizione = tinyMCE.get('txtDescrizione').getContent();

    if(!titolo){
        $('#txtTitolo').addClass('wrong-form-control');
        var flashInterval = setInterval(function() {
            $('#txtTitolo').removeClass('wrong-form-control');
        }, 500);
        return;
    }

    if(!luogo){
        $('#txtLuogo').addClass('wrong-form-control');
        var flashInterval = setInterval(function() {
            $('#txtLuogo').removeClass('wrong-form-control');
        }, 500);
        return;
    }
    
    if(!categoria){
        $('#ddlNewCategoria').addClass('wrong-form-control');
        var flashInterval = setInterval(function() {
            $('#ddlNewCategoria').removeClass('wrong-form-control');
        }, 500);
        return;
    }
    
    if(!descrizione){
        tinyMCE.get('txtDescrizione').getWin().document.body.style.backgroundColor = 'rgba(255,0,0,0.5)';
        var flashInterval = setInterval(function() {
            tinyMCE.get('txtDescrizione').getWin().document.body.style.backgroundColor = '#fff';
        }, 500);
        return;
    }

    $.ajax({
        url : 'functions/functions.php?function=AddCoupon',
        type : 'POST',
        data : {
            titolo: titolo, 
            luogo: luogo, 
            scadenza: scadenza, 
            categoria: categoria, 
            descrizione: descrizione
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

            fd.append("id", data);

            $.ajax({
                type: "POST",
                url: "functions/functions.php?function=AttachImages",
                data: fd,             
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