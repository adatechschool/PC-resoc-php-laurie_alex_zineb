<?php
session_start()
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Paramètres</title>
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
            <a href="tags.php?<?php echo $_SESSION['connected_id'] ?>">Mots-clés</a>
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
    <div id="wrapper" class='profile'>
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les informations de l'utilisatrice
                    n° <?php echo $_SESSION['connected_id'] ?></p>
            </section>
        </aside>
        <main>
            <?php
            $userId = $_SESSION['connected_id'];
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            $laQuestionEnSql = "SELECT `users`.*, "
                . "count(DISTINCT `posts`.`id`) as totalpost, "
                . "count(DISTINCT `given`.`post_id`) as totalgiven, "
                . "count(DISTINCT `recieved`.`user_id`) as totalrecieved "
                . "FROM `users` "
                . "LEFT JOIN `posts` ON `posts`.`user_id`=`users`.`id` "
                . "LEFT JOIN `likes` as `given` ON `given`.`user_id`=`users`.`id` "
                . "LEFT JOIN `likes` as `recieved` ON `recieved`.`post_id`=`posts`.`id` "
                . "WHERE `users`.`id`='" . intval($userId) . "'"
                . "GROUP BY `users`.`id`";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            $user = $lesInformations->fetch_assoc();
            ?>
            <article class='parameters'>
                <h3>Mes paramètres</h3>
                <dl>
                    <dt>Pseudo</dt>
                    <dd><a href="wall.php?<?php echo $_SESSION['connected_id'] ?>"><?php echo $user['alias'] ?></a></dd>
                    <dt>Email</dt>
                    <dd><?php echo $user['email'] ?></dd>
                    <dt>Nombre de message</dt>
                    <dd><?php echo $user['totalpost'] ?></dd>
                    <dt>Nombre de "J'aime" donnés </dt>
                    <dd><?php echo $user['totalgiven'] ?></dd>
                    <dt>Nombre de "J'aime" reçus</dt>
                    <dd><?php echo $user['totalrecieved'] ?></dd>
                </dl>
            </article>
        </main>
    </div>
</body>

</html>