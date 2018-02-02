<?php

require 'index.php';
require 'user.php';
$utente = new user();
$nome=$_POST["nome"];
$cognome=$_POST["cognome"];
$email=$_POST["email"];
$password=$_POST["password"];
$indirizzo=$_POST["indirizzo"];
$coordinate=$utente->getCoordinate($indirizzo);
$utente->registra($nome,$cognome,$email,$password);
$php='SELECT * FROM tbl_utenti';
$result=$mysqli->query($php);
if($result==false)
{
    echo "errore nella query: ".$php;
}
else
{
    $nrows= mysqli_num_rows($result);
    $i=0;
    $isEx = false;
    while(($i<$nrows)&&($isEx==false))
    {
        $row=$result->fetch_array(MYSQLI_ASSOC);
        $emaildb=$row['Email'];
        if($emaildb==$email)
        {
            $isEx = true;
        }
        $i = $i + 1;
    }
    if($isEx==true)
    {
        echo "non puoi iscriverti. Un utente sta già utilizzando quest'email";
    }
    else
    {
        $i = 1;
        $nrows=$nrows + 1;
        $php="INSERT INTO tbl_utenti (`IdUtente`, `Cognome`, `Nome`, `Email`, `Password`, `Posizione`, `IsAmminstratore`, `Attivo`) VALUES ('".$nrows."', '".$cognome."', '".$nome."', '".$email."', '". md5($password)."', '".$coordinate."', '0', '1')";
        if(mysqli_query($mysqli, $php))
        {
            echo "Registrazione effettuata";
        }
        else
        {
            echo 'ERRORE!: '.mysqli_error($mysqli);
        }
    }
}

