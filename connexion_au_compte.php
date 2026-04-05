<?php
session_start();
$erreur = "";

if(isset($_POST["email"])){
    $fichier = "utilisateurs.json";

    $contenu = file_get_contents($fichier);
    $utilisateurs = json_decode($contenu, true);

    foreach($utilisateurs as $key){
        if ($key["email"] == $_POST["email"] && $key["mdp"] == $_POST["mdp"]) {
            $_SESSION["prenom"] = $key["prenom"];
            $_SESSION["nom"] = $key["nom"];
            $_SESSION["role"] = $key["role"];
            $_SESSION["email"] = $key["email"];
            $_SESSION["tel"] = $key["tel"];
            $_SESSION["adresse"] = $key["adresse"];
            
            $longueur_mdp = strlen($key["mdp"]);
            $_SESSION["mdp_masque"] = str_repeat("•", $longueur_mdp);

            if ($_SESSION["role"] === "cuisinier") {
                header("Location: commandes.php");
            } else {
                header("Location: accueil.php");
            }
            exit();
         }
    }
    $erreur = "Email ou mot de passe incorrect";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="connexion_au_compte.css">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>

<div class="connexion">

    <form action="connexion_au_compte.php" method="post">
        
        <div class="entete">
            <a href="accueil.php" class="lien-logo">
                <div class="logo"><span>Exotique</span> Dream</div>
            </a>
            <h1>Bon retour !</h1>
            <p>Heureux de vous revoir parmi nous.</p>
        </div>

        <div class="section">
            <div class="saisie">
                <label for="email">Votre Email</label>
                <input type="email" id="email" name="email" placeholder="ex: email@gmail.com" required autofocus>
            </div>

            <div class="saisie">
                <div class="label-flex">
                    <label for="mdp">Mot de passe</label>
                    <a href="mdp_oublie.html" class="mdp-oublie">Oublié ?</a>
                </div>
                <input type="password" id="mdp" name="mdp" placeholder="Votre mot de passe" required>
            </div>
        </div>

        <?php if($erreur !== ""){
            echo "<p style='color: #ff4d4d; font-size: 14px; margin-bottom: 10px; margin-left: 5px;'> $erreur </p>  ";     
        }
        ?>

        <div>
            <div class="case-cocher">
                <input type="checkbox" id="rester_connecte">
                <label for="rester_connecte">Rester connecté</label>
            </div>
        </div>

        <div class="actions-form">
            <button type="submit" class="btn-principal">Se connecter</button>
            <p class="texte-bas">
                Pas encore de compte ? 
                <a href="creation_de_compte.php">Créer un profil</a>
            </p>
        </div>

    </form>
</div>

</body>
</html>
