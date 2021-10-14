<?php
session_start();
session_unset();
session_destroy();

?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Déconnexion</title>
    <meta name="author" content="Laurie, Alex et Zineb">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">
            <a href="">Actualités</a>
            <a href="">Mur</a>
            <a href="">Flux</a>
            <a href="">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="">Paramètres</a></li>
                <li><a href="">Mes suiveurs</a></li>
                <li><a href="">Mes abonnements</a></li>
            </ul>
        </nav>
    </header>
    <div id="wrapper">
        <aside>
            <h2>Au revoir ! à bientôt</h2>
            <h3>you are dead</h3>
        </aside>
        <main>
            <article>
                <a href='login.php?>'>
                    <h1>Se connecter</h1>
                </a>
            </article>
        </main>
    </div>
</body>

</html>