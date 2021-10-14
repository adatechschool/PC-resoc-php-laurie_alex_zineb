<?php
session_start()
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
    <meta name="author" content="Laurie, Alex et Zineb">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social" /></a>
        <nav id="menu">
            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
            <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mur</a>
            <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Flux</a>
            <a href="tags.php?<?php echo $_SESSION['connected_id'] ?>">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">▾ Profil</a>
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
                <p>Sur cette page vous trouverez les derniers messages de
                    tous les utilisatrices du site.</p>
            </section>
        </aside>
        <main>
            <?php
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            $userId = $_SESSION['connected_id'];
            $enCoursDeTraitement = isset($_POST['post_id']);
            if ($enCoursDeTraitement) {
                $idAVerifier = $_POST['id'];
                $lInstructionSql = "INSERT INTO `likes` "
                    . "(`id`, `user_id`, `post_id`) "
                    . "VALUES (NULL, "
                    . $userId . ", "
                    . $_POST['post_id'] . ")";

                $ok = $mysqli->query($lInstructionSql);
                if (!$ok) {
                    echo "Impossible d'ajouter le like: " . $mysqli->error;
                }
            }
            ?>
            <?php
            if ($mysqli->connect_errno) {
                echo ("Échec de la connexion : " . $mysqli->connect_error);
                exit();
            }
            $laQuestionEnSql = "SELECT `posts`.`content`,"
                . "`posts`.`created`,"
                . "`posts`.`id`,  "
                . "`posts`.`user_id`, "
                . "`users`.`alias` as author_name,  "
                . "count(DISTINCT `likes`.`id`) as like_number,  "
                . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                . "FROM `posts`"
                . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
                . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                . "GROUP BY `posts`.`id`"
                . "ORDER BY `posts`.`created` DESC  "
                . "LIMIT 5";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
                exit();
            }
            while ($post = $lesInformations->fetch_assoc()) {
            ?>
                <article>
                    <h3>
                        <time><?php echo $post['created'] ?></time>
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a>
                    </address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <form method="POST">
                            <input type='hidden' name='post_id' value=<?= $post['id'] ?>>
                            <input type='hidden' name='user_id' value=<?= $userId ?>>
                            <input type="submit" value="like">
                        </form>
                        <small>♥ <?php echo $post['like_number'] ?> </small>
                        <a href="">#<?php echo $post['taglist'] ?></a>,
                    </footer>
                </article>
            <?php } ?>
        </main>
    </div>
</body>

</html>