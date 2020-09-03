<?php
session_start();


// Si la SESSION est vide on affichera cette fonction contenant le formulaire de connexion.
function loginForm()
{
    echo '<div id="loginform">
    <form action="index.php" method="post">
        <p>Entrez un pseudo : </p>
        <input type="text" name="name" id="name" />
        <input type="submit" name="enter" id="enter" value="Enter" />
    </form>
    </div>';
}

// Vérification du pseudo entré par l'utilisateur.
if (isset($_POST['enter'])) {
    if ($_POST['name'] != "") {
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    } else {
        echo '<span class="error">Veuillez enter un pseudo</span>';
    }
}

// Déconnexion de l'utilisateur.
if (isset($_GET['logout'])) {
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div class='msgln'><i>" . $_SESSION['name'] . " a quitté le tchat.</i><br></div>");
    fclose($fp);

    session_destroy();
    // Retour sur la page de connexion initial
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Test Tchat</title>
    <link type="text/css" rel="stylesheet" href="css/styles.css"/>
</head>
<body>
<?php
// Si l'utilisateur n'est pas connecté.
if (!isset($_SESSION['name'])) {
    loginForm();
} else {
    ?>
    <div id="wrapper">
        <div id="menu">
            <p class="welcome">Bienvenue, <b><?php echo $_SESSION['name']; ?></b> !</p>
            <p class="logout"><a id="exit" href="#">Quitter</a></p>
            <div style="clear:both"></div>
        </div>
        <div id="chatbox"><?php
            if(file_exists("log.html") && filesize("log.html") > 0){
                $handle = fopen("log.html", "r");
                $contents = fread($handle, filesize("log.html"));
                fclose($handle);

                echo $contents;
            }
            ?></div>

        <form name="message" action="">
            <input name="usermsg" type="text" id="usermsg" size="63"/>
            <input name="submitmsg" type="submit" id="submitmsg" value="Send"/>
        </form>
    </div>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Pop up avant déconnexion
            $("#exit").click(function () {
                let exit = confirm("Êtes-vous sûr de vouloir quitter ?");
                if (exit === true) {
                    window.location = 'index.php?logout=true';
                }
            });
            // Envoie d'un message
            $("#submitmsg").click(function(){
                let clientmsg = $("#usermsg").val();
                $.post("post.php", {text: clientmsg});
                $("#usermsg").attr("value", "");
                return false;
            });
            // Sauvegarde des logs
            function loadLog(){
                let oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
                $.ajax({
                    url: "log.html",
                    cache: false,
                    success: function(html){
                        $("#chatbox").html(html);
                        var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
                        if(newscrollHeight > oldscrollHeight){
                            $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
                        }
                    },
                });
            }
            // Refresh de la page.
            setInterval (loadLog, 1000);
        });
    </script>
    <?php
}
?>




</body>
</html>