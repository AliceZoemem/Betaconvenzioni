<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form action="registrazione.php" method="post">
            <h2>Form di Registrazione</h2>
            <h4>Inserisci il tuo nome:</h4>
            <input type="text" name="nome" size="30" required>
            <h4>Inserisci il tuo cognome:</h4>
            <input type="text" name="cognome" size="30" required>
            <h4>Inserisci e-mail:</h4>
            <input type="text" name="email" size="30" required>
            <h4>Inserisci Password:</h4>
            <input type="text" name="password" size="30" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="La password deve contenere almeno 8 caratteri, di cui uno numerico, uno maiuscolo e uno minuscolo" required>
            <h4>Inserisci il tuo indirizzo:</h4>
            <input type="text" name="indirizzo" placeholder="Via, numero civico e città" size="30" required><br>
            <br><input type="submit" value="Registrati">
        </form>
        <?php
        @$mysqli = new mysqli('localhost','root','','db_betaconvenzioni');
        if($mysqli->connect_errno)
        {
            echo "Errore di Connessione";
            exit;
        }
        ?>
    </body>
</html>
