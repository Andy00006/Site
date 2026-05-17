<?php
$erreur = "";
$classe_erreur = "";

if(isset($_POST["prenom"])){
    $mdp = $_POST["mdp1"];
    $confirmation = $_POST["mdp2"];
    $email = $_POST["email"];

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

    if($erreur == ""){
        $fichier = "utilisateurs.json";
        $contenu = file_get_contents($fichier);
        $utilisateurs = json_decode($contenu, true);

        if (!empty($utilisateurs)) {
            foreach($utilisateurs as $user){
                if($user['email'] === $email){
                    $erreur = "Cette adresse email est déjà utilisée pour un autre compte.";
                    $classe_erreur = "input-erreur";
                    break;
                }
            }
        }

        if($erreur == "" && isset($_POST["mdp1"])){
            if (empty($utilisateurs)) {
                $nouvel_id = 1;
            } 
            else {
                $dernier_utilisateur = end($utilisateurs);
                $nouvel_id = $dernier_utilisateur['id'] + 1;
            }

            $nouveau = array(
                "id" => $nouvel_id,
                "prenom" => $_POST["prenom"],
                "nom" => $_POST["nom"],
                "email" => $email,
                "date" => $_POST["anniversaire"],
                "mdp" => password_hash($mdp, PASSWORD_DEFAULT),
                "tel" => str_replace(" ", "", $_POST["tel"]),
                "adresse" => $_POST["numero"]." ".$_POST["rue"],
                "role" => "client"
            );

            $utilisateurs[] = $nouveau;
                        
            $json_final = json_encode($utilisateurs);
            file_put_contents($fichier, $json_final);

            session_start();
            $_SESSION['success_inscription'] = "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.";

            header("Location: connexion_au_compte.php");
            exit();
        }
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
    <style>
        .liste-criteres {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 5px;
            font-size: 13px;
        }
        .liste-criteres li {
            margin-bottom: 4px;
            transition: color 0.2s ease;
        }
        .critere-invalide {
            color: #ff4d4d;
        }
        .critere-valide {
            color: #2ecc71;
        }
    </style>
    <script src="darkmode.js"></script>
</head>
<body>

<div class="inscription">
    <form action="creation_de_compte.php" method="post">
        
        <div class="entete">
            <a href="accueil.php">
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
                <input type="tel" id="tel" name="tel" placeholder="0600000000" maxlength="14" required>
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
            
            <ul class="liste-criteres">
                <li id="critere-longueur" class="critere-invalide">❌ 12 caractères minimum</li>
                <li id="critere-majuscule" class="critere-invalide">❌ Au moins 1 majuscule</li>
                <li id="critere-chiffre" class="critere-invalide">❌ Au moins 1 chiffre</li>
                <li id="critere-special" class="critere-invalide">❌ Au moins 1 caractère spécial</li>
                <li id="critere-identique" class="critere-invalide">❌ Mots de passe identiques</li>
            </ul>
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
                <a href="accueil.php" class="btn-lien">Retour à l'accueil</a>
            </div>
        </div>
    </form>
</div>

<script>
let mdp1Input = document.getElementById("mdp1");
let mdp2Input = document.getElementById("mdp2");

let cLongueur = document.getElementById("critere-longueur");
let cMajuscule = document.getElementById("critere-majuscule");
let cChiffre = document.getElementById("critere-chiffre");
let cSpecial = document.getElementById("critere-special");
let cIdentique = document.getElementById("critere-identique");

function validerCritere(element, estValide, texte) {
    if (estValide) {
        element.className = "critere-valide";
        element.textContent = "✅ " + texte;
    } else {
        element.className = "critere-invalide";
        element.textContent = "❌ " + texte;
    }
}

function verifierFormulaire() {
    let mdp1 = mdp1Input.value;
    let mdp2 = mdp2Input.value;

    let vLongueur = mdp1.length >= 12;
    let vMajuscule = /[A-Z]/.test(mdp1);
    let vChiffre = /[0-9]/.test(mdp1);
    let vSpecial = /[^A-Za-z0-9]/.test(mdp1);
    let vIdentique = mdp1 === mdp2 && mdp1 !== "";

    validerCritere(cLongueur, vLongueur, "12 caractères minimum");
    validerCritere(cMajuscule, vMajuscule, "Au moins 1 majuscule");
    validerCritere(cChiffre, vChiffre, "Au moins 1 chiffre");
    validerCritere(cSpecial, vSpecial, "Au moins 1 caractère spécial");
    validerCritere(cIdentique, vIdentique, "Mots de passe identiques");

    return vLongueur && vMajuscule && vChiffre && vSpecial && vIdentique;
}

mdp1Input.addEventListener("input", verifierFormulaire);
mdp1Input.addEventListener("keyup", verifierFormulaire);
mdp2Input.addEventListener("input", verifierFormulaire);
mdp2Input.addEventListener("keyup", verifierFormulaire);

document.querySelector("form").addEventListener("submit", function(e) {
    let formulaireValide = verifierFormulaire();
    if (!formulaireValide) {
        e.preventDefault();
    }
});
</script>

</body>
</html>
