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
<script>tinymce.init({ selector:'#txtDescrizione' });</script>


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

<h1 style="display:inline">Convenzioni</h1>
<button type="button" class="right" onclick="window.location.href='logout.php'">Logout</button>
    <div class="add-bar">
        <img src="img/add.png" onclick="ShowAddPopup();" />
    </div>

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

            <select name="order_by" id="ddlOrder">
                <option disabled value="">Ordina per...</option>
                <option value="rating" selected>Più popolari</option>
                <option value="expiry">Più recenti</option>
                <option value="distance">Più vicini</option>
            </select>

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


<div class="modal fade" id="modalAddCoupon" tabindex="-1" role="dialog" aria-labelledby="modalAddCoupon" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="UploadForm" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi convenzione</h5>
            </div>
            <div class="modal-body new-form">
                <input type="text" id="txtTitolo" name="newtitolo" class="form-control" placeholder="Titolo" />
                <input type="text" id="txtLuogo" name="newluogo" class="form-control" placeholder="Luogo" />
                <input id="txtScadenza" name="newscadenza" class="form-control" type="text" placeholder="Scadenza" onblur="(this.type='text')" onfocus="(this.type='date')"  > 
                <select id="ddlNewCategoria" name="newcategoria" class="form-control">
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
                <input type="file" id="FileUploader" accept="image/*" class="form-control" multiple />
                <textarea name="txtarea" id="txtDescrizione" placeholder="Descrizione">
                </textarea>
            </div>
            <div class="modal-footer">
                <input type="button" name="change" value="Aggiungi" class="btn btn-primary" onclick="AddCoupon();" />
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
            </div>
        </form>
    </div>
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

    if($('#ddlOrder').val()){
        var orderBy = encodeURI($('#ddlOrder').val());
        url += "&order_by=" + orderBy;
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
        $('#ddlOrder').val('rating');

        $('.conv-wrapper').click(function() {
            var targetCoupon = $(this).data('conv-target');
            window.location = "convenzione.php?convenzione=" + targetCoupon;
        });
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

function ShowAddPopup(){
    $('#modalAddCoupon').modal('show');
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