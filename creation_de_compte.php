<?php
$erreur = "";
$classe_erreur = "";

if(isset($_POST["prenom"])){
    $mdp = $_POST["mdp1"];
    $confirmation = $_POST["mdp2"];

    if($mdp !== $confirmation){
        $erreur = "Les mots de passe ne sont pas identiques.";
        $classe_erreur = "input-erreur";
    } 
    else{
        $majuscule = false;
        $chiffre = false;
        $special = false;

        $lettres = str_split($mdp);

        foreach($lettres as $key){
            if(ctype_upper($key)) $majuscule = true;
            if(ctype_digit($key)) $chiffre = true;
            if(!ctype_alnum($key)) $special = true; 
        }

        if(strlen($mdp) < 12 || !$majuscule || !$chiffre || !$special){
            $erreur = "Il faut une majuscule, un chiffre, un caractère spécial et 12 caractères minimum.";
            $classe_erreur = "input-erreur";
        }
    }
    if($erreur == "" && isset($_POST["mdp1"])){
        $fichier = "utilisateurs.json";
                    
         $contenu = file_get_contents($fichier);
         $utilisateurs = json_decode($contenu, true);

         $nouveau = array(
            "prenom" => $_POST["prenom"],
            "nom" => $_POST["nom"],
            "email" => $_POST["email"],
            "date" => $_POST["anniversaire"],
            "mdp" => $_POST["mdp1"],
            "role" => "client"
        );

        $utilisateurs[] = $nouveau;
                    
        $json_final = json_encode($utilisateurs);
        file_put_contents($fichier, $json_final);

        header("Location: connexion_au_compte.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte</title>
    <link rel="stylesheet" href="creation_de_compte.css">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>

<div class="inscription">
    <form action="creation_de_compte.php" method="post">
        
        <div class="entete">
            <a href="accueil.html">
                <div class="logo"><span>Exotique</span> Dream</div>
            </a>
            <h1>Rejoignez l'aventure</h1>
            <p>Créez votre profil vitaminé en quelques secondes.</p>
        </div>

        <div class="section">
            <div class="groupe-ligne">
                <div class="saisie">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="ex: Prénom" required autofocus>
                </div>
                <div class="saisie">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="ex: Nom" required>
                </div>
            </div>

            <div class="saisie">
                <label for="anniversaire">Date d'anniversaire</label>
                <input type="date" id="anniversaire" name="anniversaire" required>
            </div>
        </div>

        <div class="section">
            <div class="saisie">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="email@gmail.com" required>
            </div>
            <div class="saisie">
                <label for="tel">Numéro de téléphone</label>
                <input type="tel" id="tel" name="tel" placeholder="06 00 00 00 00" pattern="[0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}" required>
            </div>
        </div>

        <div class="section">
            <div class="groupe-ligne">
                <div class="saisie" style="flex: 2;">
                    <label for="rue">Rue, Boulevard, Avenue...</label>
                    <input type="text" id="rue" name="rue" placeholder="ex: Avenue du Général de Gaulle" required>
                </div>
                <div class="saisie" style="flex: 1;">
                    <label for="numero">N°</label>
                    <input type="number" id="numero" name="numero" placeholder="23" min="1" required>
                </div>
            </div>

            <div class="radio">
                <label>Complément de numéro :</label>
                <div class="choix-radio">
                    <input type="radio" id="aucun" value="aucun" name="cdn" checked> <label for="aucun">Aucun</label>
                    <input type="radio" id="bis" value="bis" name="cdn"> <label for="bis">Bis</label>
                    <input type="radio" id="ter" value="ter" name="cdn"> <label for="ter">Ter</label>
                </div>
            </div>

            <div class="groupe-ligne">
                <div class="saisie">
                    <label for="code_postal">Code postal</label>
                    <input type="number" id="code_postal" name="code_postal" placeholder="94280" required>
                </div>
                <div class="saisie">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" placeholder="ex: Paris" required>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="groupe-ligne">
                <div class="saisie">
                    <label for="mdp1">Mot de passe</label>
                    <input type="password" id="mdp1" name="mdp1" class="<?php echo $classe_erreur; ?>" placeholder="Mot de passe" required>
                </div>
                <div class="saisie">
                    <label for="mdp2">Confirmation</label>
                    <input type="password" id="mdp2" name="mdp2" class="<?php echo $classe_erreur; ?>" placeholder="Confirmer" required >
                </div>
            </div>
            <?php if($erreur !== ""){
                echo "<p style='color: #ff4d4d; font-size: 14px; margin-bottom: 10px; margin-left: 5px;'> $erreur </p>  ";     
            }
            ?>
        </div>

        <div class="consentement">
            <div class="case-cocher">
                <input type="checkbox" id="mention_legal" name="validation" required>
                <label for="mention_legal">J'accepte les <a href="#">conditions d'utilisation</a></label>
            </div>
            <div class="case-cocher">
                <input type="checkbox" id="accord" name="accord">
                <label for="accord">Je souhaite recevoir des newsletters vitaminées</label>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn-principal">Créer mon compte</button>
            <div class="btn-secondaires">
                <button type="reset" class="btn-lien">Réinitialiser</button>
                <a href="accueil.html" class="btn-lien">Retour à l'accueil</a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
