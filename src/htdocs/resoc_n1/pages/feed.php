<?php
session_start()
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Flux</title>
    <meta name="author" content="Laurie, Alex et Zineb">
    <link rel="stylesheet" href="style.css" />
    </head≠>

<body>
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">
            <a href="news.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Actualités</a>
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
        <?php
        $userId = $_SESSION['connected_id'];
        ?>
        <?php
        $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
        ?>
        <aside>
            <?php
            $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();

            ?>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message des utilisatrices
                    auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
                    (n° <?php echo $userId ?>)
                </p>
            </section>
        </aside>
        <main>
            <?php
            $enCoursDeTraitement = isset($_POST['post_id']);
            if ($enCoursDeTraitement) {
                $idAVerifier = $_POST['id'];
                $lInstructionSql = "INSERT INTO `likes` "
                    . "(`id`, `user_id`, `post_id`)"
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
            $laQuestionEnSql = "SELECT `posts`.`content`,"
                . "`posts`.`created`,"
                . "`users`.`alias` as author_name,  "
                . "`users`. `id` as usersId, "
                . "`posts`.`id`, "
                . "count(DISTINCT `likes`.`id`) as like_number,  "
                . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                . "FROM `followers` "
                . "JOIN `users` ON `users`.`id`=`followers`.`followed_user_id`"
                . "JOIN `posts` ON `posts`.`user_id`=`users`.`id`"
                . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                . "WHERE `followers`.`following_user_id`='" . intval($userId) . "' "
                . "GROUP BY `posts`.`id`"
                . "ORDER BY `posts`.`created` DESC  ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            while ($post = $lesInformations->fetch_assoc()) {
                $laQuestionEnSqlTags = "SELECT `tags` . `id`, "
                    . " `tags`. `label` "
                    . "FROM `posts_tags`"
                    . "JOIN `tags` ON `tags`.`id` = `posts_tags`.`tag_id` "
                    . "Where `post_id` =" . $post['id'];
                $lesInformationsTags = $mysqli->query($laQuestionEnSqlTags);
                if (!$lesInformationsTags) {
                    echo ("Échec de la requete : " . $mysqli->error);
                }
            ?>
                <article>
                    <h3>
                        <time><?php echo $post['created'] ?></time>
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['usersId'] ?>"><?php echo $post['author_name'] ?></a></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <form method="POST">
                            <input type='hidden' name='post_id' value=<?= $post['id'] ?>>
                            <input type='hidden' name='user_id' value=<?= $userId ?>>
                            <input type="submit" value="like">
                        </form>
                        <small>♥ <?php echo $post['like_number'] ?></small>
                        <?php while ($tag = $lesInformationsTags->fetch_assoc()) { ?>
                            <a href="tags.php?tag_id=<?= $tag['id'] ?>">#<?php echo $post['taglist'] ?></a>
                        <?php } ?>
                    </footer>
                </article>
            <?php } ?>
        </main>
    </div>
</body>

</html>