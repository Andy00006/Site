<?php
session_start();
$erreur = "";
$message_succes = "";

if (isset($_SESSION['success_inscription'])) {
    $message_succes = $_SESSION['success_inscription'];
    unset($_SESSION['success_inscription']); 
}

if(isset($_POST["email"])){
    $fichier = "../json/utilisateurs.json";

    $contenu = file_get_contents($fichier);
    $utilisateurs = json_decode($contenu, true);

    foreach($utilisateurs as $key){
        if ($key["email"] == $_POST["email"] && password_verify($_POST["mdp"], $key["mdp"])) {
            
            if ($key["role"] === "bloqué") {
                $erreur = "Votre compte a été suspendu par l'administrateur.";
                break;
            } 
            else {
                $_SESSION["id_user"] = $key["id"];
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
                }
                elseif ($_SESSION["role"] === "livreur") {
                    header("Location: livraison.php");
                } 
                else {
                    header("Location: accueil.php");
                }
                exit();
            }
            break;
         }
    }
    if ($erreur === "") {
        $erreur = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/connexion_au_compte.css">
    <link rel="stylesheet" href="../css/couleur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/site.css">
    <script src="../js/darkmode.js"></script>
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

        <?php if ($message_succes !== ""): ?>
            <p style="color: #2ecc71; background-color: rgba(46, 204, 113, 0.1); padding: 10px; border-radius: 5px; font-size: 14px; margin-bottom: 15px; border: 1px solid #2ecc71;">
                <?php echo $message_succes; ?>
            </p>
        <?php endif; ?>

        <div class="section">
            <div class="saisie">
                <label for="email">Votre Email</label>
                <input type="email" id="email" name="email" placeholder="ex: email@gmail.com" required autofocus>
            </div>

            <div class="saisie">
                <div class="label-flex">
                    <label for="mdp">Mot de passe</label>
                </div>
                <div class="conteneur-mdp">
                    <input type="password" id="mdp" name="mdp" placeholder="Votre mot de passe" required>
                    <i class="fas fa-eye icone-oeil" id="bascule-mdp"></i>
                </div>
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

<script>
    const basculeMdp = document.getElementById('bascule-mdp');
    const champMdp = document.getElementById('mdp');

    basculeMdp.addEventListener('click', function() {
        if (champMdp.type === 'password') {
            champMdp.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            champMdp.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>
