<html>
<head>

<style>
    .conv-list{
        width:100%;
        height:100%;
        text-align:center;
    }


    .conv-wrapper{
        width:90vw;
        height:30vw;
        /* background-color:#f00; */
        margin-bottom:20px;
        border-bottom:1px solid #444;
        padding-bottom:50px;
    }

    .conv-cover{
        background-position-x:center;   
        background-position-y:center;
        background-size:cover;   
        background-repeat:no-repeat;
        width:30vw;
        padding-top:30vw;
        display:inline-block;
        position:relative;
        left:0;
        margin:0;
        padding-bottom:0;
        padding-left:0;
        padding-right:0;
    }

    .conv-content{
        width:60%;
        /* background-color:#0f0; */
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

</style>

</head>
<body>

<h1>Convenzioni</h1>
<div class='conv-list'>



<?php
$link = mysqli_connect("localhost", "root", "", "db_betaconvenzioni");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$query = "SELECT * FROM tbl_convenzioni";

if ($result = mysqli_query($link, $query)) {

    /* fetch associative array */
    while ($row = mysqli_fetch_array($result)) {
        $titolo = $row['Titolo'];
        $descrizione = $row['Descrizione'];
        $coordinate = $row['Posizione'];
        $scadenza = $row['DataScadenza'];
        $idCategoria = $row['IdCategoria'];
        
        if($scadenza == 0000-00-00)
            $scadenza = "Nessuna scadenza";

            
        echo " 
            <div class='conv-wrapper'>
                <div class='conv-cover' style=\"background-image: url('https://www.remkes.com/wp-content/uploads/2016/04/printable-coupons.jpg')\">
                </div>
                <div class='conv-content'>

                    <h2 class='conv-title'>$titolo</h2>
                    <i class='conv-expiration'>$scadenza</i>
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

/* close connection */
mysqli_close($link);





?>



</div>


</body>
</html>