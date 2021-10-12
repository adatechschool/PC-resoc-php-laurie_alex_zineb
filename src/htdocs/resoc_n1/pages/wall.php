<?php 
session_start()
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>
            <nav id="menu">
                <a href="news.php?user_id=<?php echo $_SESSION['connected_id']?>">Actualités</a>
                <a href="wall.php?user_id=<?php echo $_SESSION['connected_id']?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $_SESSION['connected_id']?>">Flux</a>
                <a href="tags.php?">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id']?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $_SESSION['connected_id']?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $_SESSION['connected_id']?>">Mes abonnements</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">

            <?php
             $userId = $_GET['user_id'];
             $userConnected = $_SESSION['connected_id'];
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo print_r($user['alias'],50)?>
                        (n° <?php echo $userId ?>)
                    </p>
                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $enCoursDeTraitement = isset($_POST['post_id']);
            if ($enCoursDeTraitement) {
                $idAVerifier = $_POST['id'];
                $lInstructionSql = "INSERT INTO `likes` "
                    . "(`id`, `user_id`, `post_id`)"
                    . "VALUES (NULL, "
                    . $userConnected . ", "
                    . $_POST['post_id'] . ")";

                $ok = $mysqli->query($lInstructionSql);
                if (!$ok) {
                    echo "Impossible d'ajouter le like: " . $mysqli->error;
                }
            }
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`," 
                        . "`posts`.`id`, "
                        . "`users`.`alias` as author_name,  "
                        . "count(`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `posts`"
                        . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE `posts`.`user_id`='" . intval($userId) . "' "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  "
                ;
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                while ($post = $lesInformations->fetch_assoc())
                {
                     

                    $laQuestionEnSqlTags = "SELECT `tags` . `id`, "
                    . " `tags`. `label` "
                    . "FROM `posts_tags`"
                    . "JOIN `tags` ON `tags`.`id` = `posts_tags`.`tag_id` "
                    . "Where `post_id` =" . $post['id'];
            //         SELECT tags.id, tags.label FROM posts_tags JOIN tags ON tags.id = posts_tags.tag_id WHERE post_id = 9;
            
            $lesInformationsTags = $mysqli->query($laQuestionEnSqlTags);
            if ( ! $lesInformationsTags)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                    ?>                
                    <article>
                        <h3>
                            <time ><?php echo $post['created']?></time>
                        </h3>
                        <address><?php echo $post['author_name']?></address>
                        <div>
                            <p><?php echo $post['content']?></p>
                        </div>   
                        <footer>
                        <form method="POST">
                            <input type='hidden' name='post_id' value=<?= $post['id'] ?>>
                            <input type='hidden' name='user_id' value=<?= $userConnected ?>>
                            <input type="submit" value="like">
                        </form>
                        
                            <small>♥ <?php echo $post['like_number']?></small>
                           
                        <?php while ($tag = $lesInformationsTags->fetch_assoc()){?>
                            <a href="tags.php?tag_id=<?= $tag['id']?>">#<?php echo $tag['label']?>
                            #</a>
                            <?php }?>
                        </footer>
                    </article>
                <?php }?>

                <?php 
                 
                if($_SESSION['connected_id'] == $userId){
                    ?>
                <article>
                    <h2>Poster un message</h2>
                    <?php
                    // $_SESSION['connected_id']=$post['id'];
                    $enCoursDeTraitement = isset($_POST['content']);
                    if ($enCoursDeTraitement)
                    {
                        $postContent = $_POST['content'];
                        $postContent = $mysqli->real_escape_string($postContent);
                        $lInstructionSql = "INSERT INTO `posts` "
                                . "(`id`, `user_id`, `content`, `created`, `parent_id`) "
                                . "VALUES (NULL, "
                                . "" . $userId . ", "
                                . "'" . $postContent . "', "
                                . "NOW(), "
                                . "NULL);"
                                . "";
                    
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message posté avec succés";
                        }
                    }
                    ?>
                    <form action = "wall.php?user_id=<?php echo $_SESSION['connected_id']?>" method="post">
                    <input type='hidden' name= '???' value='achanger'>
                    <div><label for='content'>Message</label></div>
                    <br>
                    <div><textarea name='content' placeholder="postez votre message ici"></textarea></div>
                    <br>
                    <input type='submit'>
                    </form>
                </article>
               <?php }?>

            </main>
        </div>
    </body>
</html>
