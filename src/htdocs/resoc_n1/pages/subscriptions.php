<?php
session_start()
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mes abonnements</title>
    <meta name="author" content="Laurie, Alex et Zineb">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">
            <a href="news.php">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Flux</a>
            <a href="tags.php?tag_id=1">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mes abonnements</a></li>
                <li><a href="logOut.php?">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <div id="wrapper">
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez la liste des personnes dont
                    l'utilisatrice
                    n° <?php echo $_GET['user_id'] ?>
                    suit les messages
                </p>
            </section>
        </aside>
        <main class='contacts'>
            <?php
            $userId = $_SESSION['connected_id'];
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            $laQuestionEnSql = "SELECT `users`.* "
                . "FROM `followers` "
                . "LEFT JOIN `users` ON `users`.`id`=`followers`.`followed_user_id` "
                . "WHERE `followers`.`following_user_id`='" . intval($userId) . "'"
                . "GROUP BY `users`.`id`";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            while ($user = $lesInformations->fetch_assoc()) {
            ?>
                <article>
                    <img src="user.jpg" alt="blason" />
                    <h3><a href="wall.php?user_id=<?php echo $user['id'] ?>"><?php echo $user['alias'] ?></a></h3>
                    <p>id:<?php echo $user['id'] ?></p>
                </article>
            <?php } ?>
        </main>
    </div>
</body>

</html>