<html>
    <head>
        <title>Betacom</title>
    </head>
    <body>
        <input type="button" class="left" value="Homepage" onclick="window.location.href='homepage.php'">
        <input type="button" class="right" value="Logout" onclick="window.location.href='logout.php'">
        <h1>Visualizza Profilo</h1>
    </body>
</html>
<?php
require_once 'functions/functions.php';
$conn= InstauraConnessione();
if(isset($_COOKIE['auth_betaconvenzioni']))
{
    $userID = Encryption($_COOKIE['auth_betaconvenzioni'],'d');
    $php="SELECT Cognome, Nome, Email FROM tbl_utenti WHERE IdUtente = ".$userID;
    $result=$conn->query($php);
    if($result==false)
    {
        echo 'Internal Error';
    }
    else 
    {
        $row=$result->fetch_array(MYSQLI_ASSOC);
        echo '<h4><b>Cognome: </b>'.$row['Cognome'].'</h4>';
        echo '<h4><b>Nome: </b>'.$row['Nome'].'</h4>';
        echo '<h4><b>Email: </b>'.$row['Email'].'</h4>';
    }
}
else
{
    header('Location: login.php');
}

