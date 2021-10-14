<?php
session_start()

?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Connexion</title>
    <meta name="author" content="Laurie, Alex et Zineb">
    <link rel="stylesheet" href="style.css" />
</head>

<body> 
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">
            <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bienvenu sur votre réseau social préféré</h1>
        </nav>
    </header>
    <div id="wrapper">
        
        <main>
            <article>
                <h2>Connexion</h2>
                <?php
                $enCoursDeTraitement = isset($_POST['email']);
                if ($enCoursDeTraitement) {
                    $emailAVerifier = $_POST['email'];
                    $passwdAVerifier = $_POST['motpasse'];
                    $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
                    $emailAVerifier = $mysqli->real_escape_string($emailAVerifier);
                    $passwdAVerifier = $mysqli->real_escape_string($passwdAVerifier);
                    $passwdAVerifier = md5($passwdAVerifier);
                    $lInstructionSql = "SELECT * "
                        . "FROM `users` "
                        . "WHERE "
                        . "`email` LIKE '" . $emailAVerifier . "' "
                        . "";
                    $res = $mysqli->query($lInstructionSql);
                    $user = $res->fetch_assoc();
                    if (!$user or $user["password"] != $passwdAVerifier) {
                        echo "La connexion a échouée. ";
                    } else {
                        echo "Votre connexion est un succès  ";
                        $_SESSION['connected_id'] = $user['id'];
                    }
                }
                ?>
                <a href='wall.php?user_id=<?php echo $_SESSION['connected_id'] ?>'><?php echo $user['alias'] ?></a>
                <form action="login.php" method="post">
                    <input type='hidden' name='???' value='achanger'>
                    <dl>
                        <dt><label for='email'>E-Mail</label></dt>
                        <dd><input type='email' name='email'></dd>
                        <dt><label for='motpasse'>Mot de passe</label></dt>
                        <dd><input type='password' name='motpasse'></dd>
                    </dl>
                    <input type='submit'>
                </form>
                <p>
                    Pas de compte?
                    <a href='registration.php'>Inscrivez-vous.</a>
                </p>
            </article>
        </main>
    </div>
</body>

</html>