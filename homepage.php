<?php
	require_once('functions/functions.php');
	if(!(isset($_COOKIE['auth_betaconvenzioni']))){
		echo "<script>window.location.href='login.php'</script>";
	}
?>

<style>

    .filters-bar{
        width:100%;
        padding:25px 0;
        margin:0;
        display:inline-block;
        text-align:center;
    }

    .filters-controls .form-control{
        max-width:20%;
        display:inline-block;
        margin-bottom:3px;
    }

    .filter-buttons{
        width:85%;
        text-align:right;
        display:inline-block;
        padding-right:2%;
        padding-top:20px;
    }

    .conv-list{
        height:100%;
        text-align:center;
        padding:0 10%;
    }

    .conv-wrapper{
        min-height:30%;
        height:auto;
        margin-bottom:20px;
        transition:0.2s;
        padding: 30px;
    }

    .conv-wrapper:hover{
        background-color:#eee;
    }

    .conv-cover{
        box-shadow:0 2px 6px rgba(0,0,0,0.8);
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
        transition:0.2s;
    }

    .conv-cover:hover{
        cursor:pointer;
        -webkit-transform: scale(1.03);
		transform: scale(1.03);        
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
        width:80%;
        margin-left:10%;
        display:block;
        height:30px;
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

    .form-control.location.address{
        width:70%;
        display:inline-block;
    }

    .pac-container {
        background-color: #FFF;
        z-index: 50;
        position: fixed;
        display: inline-block;
    }

    .modal{
        z-index: 20;   
    }

    .modal-backdrop{
        z-index:0;
    }

    
    /* ~ ~ Responsiveness ~ ~ */
    @media all and (max-width: 1000px) {    
        .conv-list{
            padding: 0 5%;
        }

        .conv-cover{
            width:80%;
        }

        .filters-controls .form-control{
            max-width:45%;
        }
    }

</style>
    <div class="add-bar">
        <button class="btn btn-warning" onclick="ShowAddPopup();" id="btnAddCoupon">Aggiungi convenzione</button>
        <button class="btn btn-secondary" onclick="ShowCategoryPopup();" id="btnManageCategory">Gestisci categorie</button>
    </div>

<div class='filters-bar'>
    <form method='get' id="MainForm" action='homepage.php'>
    
        <div class='filters-controls'>
            <input type='text' id='txtLocalita' name='localita' class="form-control" placeholder='Località...' />

            <select id='ddlCategoria' name='categoria' class="form-control" >
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

            <input type='text' id='txtRicerca' name='cerca' placeholder='Cerca...' class="form-control" />

            <select name="order_by" id="ddlOrder" class="form-control">
                <option disabled value="">Ordina per...</option>
                <option value="rating" selected>Più popolari</option>
                <option value="expiry">Più recenti</option>
                <option value="distance">Più vicini</option>
            </select>

        </div>

        <div class='filter-buttons'>
            <input type='submit' id='btnCerca' value='Cerca' onclick="Cerca();" class="btn btn-primary" />
            <input type='submit' id='btnRimuoviFiltri' value='Rimuovi filtri' onclick="RimuoviFiltri();" class="btn btn-secondary" />
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
                            $idRegione = $row['Id'];
                            $nome = $row['Nome'];
                            
                            echo "
                            <input type='text' class='form-control location address' data-region='$idRegione' placeholder='Indirizzo $nome' id='address$idRegione' />
                            <label><input type='checkbox' class='check-everywhere' data-region='$idRegione' />Ovunque</label>";
                        }
                    }
                    
                    /* close connection */
                    AbbattiConnessione($conn);
                ?>
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
                Immagini<br/>
                <input type="file" id="FileUploader" accept="image/*" class="form-control" multiple />
                Allegati<br/>
                <input type="file" id="AttachmentsUploader" class="form-control" multiple />
                <textarea name="txtarea" id="txtDescrizione" placeholder="Descrizione">
                </textarea>
            </div>
            <div class="modal-footer">
                <img src="img/throbber.gif" id="Throbber" style="display:none; max-height:100px;" />
                <input type="button" name="change" value="Aggiungi" class="btn btn-primary" onclick="AddCoupon();" />
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Manage cagegories -->
<div class="modal fade" id="modalCategory" tabindex="-1" role="dialog" aria-labelledby="titleLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleLabel">Gestisci categorie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="divAddCategory" style="display:block;">
            <input type="text" class="form-control" id="txtNewCategory" placeholder="Nuova categoria..." />
            <br/>
            <button class="btn btn-primary" onclick="AddCategory()">Aggiungi</button>
        </div>
        <div id="divEditCategory" style="display:none;">
            <input type="text" class="form-control" id="txtEditCategory" />
            <br/>
            <button class="btn btn-primary" onclick="EditCategory()">Conferma</button>
            <button class="btn btn-secondary" onclick="CancelEditCategory()">Annulla</button>
        </div>

        <br/><br/>
        <h3>Categorie esistenti </h3>
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
                    
                    echo "$nome  <b class='btn btn-link' onclick=\"StartEditCategory($idCategoria, '$nome')\">[modifica]</b><b class='btn btn-link' onclick='DeleteCategory($idCategoria)'>[elimina]</b><br/>";
                }
            }
            
            /* close connection */
            AbbattiConnessione($conn);
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal delete alert -->
<div class="modal fade" id="DeletePopup" tabindex="-1" role="dialog" aria-labelledby="titleLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleLabel">Attenzione</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Sei sicuro di voler eliminare questa convenzione?
      </div>
      <div class="modal-footer">
        <button type="button" id="btnDeleteCoupon" class="btn btn-danger" onclick="ConfirmDeleteCoupon();">Elimina</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#btnDeleteCoupon').data('id', '');">Annulla</button>
      </div>
    </div>
  </div>
</div>

<script>

$(document).ready(function (){
    var input = document.getElementById('txtLocalita');
    var autocomplete = new google.maps.places.Autocomplete(input);

    $('.form-control.location.address').each(function () {
        var id = $($(this)[0]).attr('id');
        var input = document.getElementById(id);
        console.log(input);
        var autocomplete = new google.maps.places.Autocomplete(input);
    });

    var utente = getCookie('auth_betaconvenzioni');
    var url = "functions/functions.php?function=LoadList&utente=" + utente;
    
    $("#conv-list").load(url, function() {
        AdjustStyle();
        $('.conv-cover').click(function() {
            var targetCoupon = $(this).data('conv-target');
            window.location = "convenzione.php?convenzione=" + targetCoupon;
        });

        $('.check-everywhere').click(function (){
            var checked = $(this).is(":checked");
            if(checked){
                var region = $(this).data('region');
                var txt = $('input[type=text][data-region="' + region + '"]')[0];
                $(txt).val('[ovunque]');
                $(txt).attr('disabled', 'disabled');
            }
            else{
                var region = $(this).data('region');
                var txt = $('input[type=text][data-region="' + region + '"]')[0];
                $(txt).val('');
                $(txt).removeAttr('disabled');
            }
        });

        $.ajax({
            url : 'functions/functions.php?function=GetUserType',
            type : 'GET',
            data : {
                user: utente
            },
            success : function(data) { 
                data = getHtmlFreeResponse(data);
                data = JSON.parse(data);
                console.log(data);

                if(data.admin != 1){
                    $('#btnAddCoupon').remove();
                    $('#btnManageCategory').remove();
                    $('.btn-delete-coupon').each(function () {
                        $(this).remove();
                    });
                }
            },
            error : function(request, error) {
                console.log("Error", request, error);
            }
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
    $('#Throbber').show();

    var titolo = $('#txtTitolo').val();
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

    var indirizzi = [];

    $('.form-control.location.address').each(function() {
        var regione = $(this).data('region');
        var indirizzo = $(this).val();
        var record = { 'regione': regione, 'indirizzo': indirizzo};
        indirizzi.push(record);
    });

    $.ajax({
        url : 'functions/functions.php?function=AddCoupon',
        type : 'POST',
        data : {
            titolo: titolo, 
            scadenza: scadenza, 
            categoria: categoria, 
            descrizione: descrizione, 
            indirizzi: indirizzi
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

            var ins = document.getElementById('AttachmentsUploader').files.length;
            for (var x = 0; x < ins; x++) 
                fd.append("Attachments[]", document.getElementById('AttachmentsUploader').files[x]);

            if(ins <= 0)
                fd.append("Attachments[]", null);

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

function DeleteCoupon(id){
    $('#btnDeleteCoupon').data('id', id);
    $('#DeletePopup').modal('show');
}

function ConfirmDeleteCoupon() {
    var couponId = $('#btnDeleteCoupon').data('id'); 

    if(couponId){
        console.log('I got called');

        $.ajax({
            url : 'functions/functions.php?function=DeleteCoupon',
            type : 'POST',
            data : {
                CouponId: couponId
            },
            success : function(data) { 
                data = getHtmlFreeResponse(data);
                console.log(data);
                window.location.href = window.location.href;
            },
            error : function(request, error) {
                console.log("Error", request, error);
            }
        });
    }
}

function AddCategory() {
    var categoria = $('#txtNewCategory').val(); 

    if(categoria){
        $.ajax({
            url : 'functions/functions.php?function=AddCategory',
            type : 'POST',
            data : {
                Categoria: categoria
            },
            success : function(data) { 
                data = getHtmlFreeResponse(data);
                console.log(data);
                window.location.href = window.location.href;
            },
            error : function(request, error) {
                console.log("Error", request, error);
            }
        });
    }
}

function StartEditCategory(id, nome) {
    $('#divAddCategory').hide();
    $('#divEditCategory').fadeIn();

    $('#txtEditCategory').val(nome);
    $('#txtEditCategory').data('category', id);
}

function CancelEditCategory(id, nome) {
    $('#divEditCategory').hide();
    $('#divAddCategory').fadeIn();

    $('#txtEditCategory').val('');
    $('#txtEditCategory').data('category', '');
}

function EditCategory(id) {
    var id = $('#txtEditCategory').data('category');
    var nome = $('#txtEditCategory').val();

    if(id && nome){
        $.ajax({
            url : 'functions/functions.php?function=EditCategory',
            type : 'POST',
            data : {
                Id: id, 
                Categoria: nome
            },
            success : function(data) { 
                data = getHtmlFreeResponse(data);
                console.log(data);
                window.location.href = window.location.href;
            },
            error : function(request, error) {
                console.log("Error", request, error);
            }
        });
    }
}

function DeleteCategory(id) {
    if(id){
        $.ajax({
            url : 'functions/functions.php?function=DeleteCategory',
            type : 'POST',
            data : {
                Categoria: id
            },
            success : function(data) { 
                data = getHtmlFreeResponse(data);
                console.log(data);
                window.location.href = window.location.href;
            },
            error : function(request, error) {
                console.log("Error", request, error);
            }
        });
    }
}

function ShowAddPopup(){
    $('#modalAddCoupon').modal('show');
}

function ShowCategoryPopup(){
    $('#modalCategory').modal('show');
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

