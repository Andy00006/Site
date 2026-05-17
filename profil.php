<?php
session_start();
require_once 'deco.php';

if (!isset($_SESSION["prenom"])) {
    header("Location: connexion_au_compte.php");
    exit();
}

$message_profil = "";
$classe_message = "";

if (isset($_POST["modifier_champ"])) {
    $champ_a_modifier = $_POST["champ_nom"];
    $nouvelle_valeur = $_POST["champ_valeur"];

    $fichier = "utilisateurs.json";
    $contenu = file_get_contents($fichier);
    $utilisateurs = json_decode($contenu, true);
    
    $ancien_email = $_SESSION["email"];
    $erreur_modif = false;

    if ($champ_a_modifier === "email" && $nouvelle_valeur !== $ancien_email && !empty($utilisateurs)) {
        foreach ($utilisateurs as $user) {
            if ($user['email'] === $nouvelle_valeur) {
                $erreur_modif = true;
                $message_profil = "Cette adresse email est déjà utilisée par un autre compte.";
                $classe_message = "message-erreur";
                break;
            }
        }
    }

    if ($champ_a_modifier === "mdp") {
        $majuscule = false;
        $chiffre = false;
        $special = false;
        $lettres = str_split($nouvelle_valeur);

        foreach($lettres as $key){
            if(ctype_upper($key)) $majuscule = true;
            if(ctype_digit($key)) $chiffre = true;
            if(!ctype_alnum($key)) $special = true; 
        }

        if(strlen($nouvelle_valeur) < 12 || !$majuscule || !$chiffre || !$special){
            $erreur_modif = true;
            $message_profil = "Le mot de passe doit contenir au moins 12 caractères, une majuscule, un chiffre et un caractère spécial.";
            $classe_message = "message-erreur";
        }
    }

    if (!$erreur_modif && !empty($utilisateurs)) {
        foreach ($utilisateurs as $cle => $user) {
            if ($user['email'] === $ancien_email) {
                if ($champ_a_modifier === "mdp") {
                    $utilisateurs[$cle]['mdp'] = password_hash($nouvelle_valeur, PASSWORD_DEFAULT);
                } 
                else {
                    if ($champ_a_modifier === "tel") {
                        $nouvelle_valeur = str_replace(" ", "", $nouvelle_valeur);
                    }
                    $utilisateurs[$cle][$champ_a_modifier] = $nouvelle_valeur;
                    $_SESSION[$champ_a_modifier] = $nouvelle_valeur;
                }
                break;
            }
        }

        file_put_contents($fichier, json_encode($utilisateurs));
        if ($message_profil === "") {
            $message_profil = "Modification enregistrée avec succès !";
            $classe_message = "message-succes";
        }
    }
}

$initiale_p = substr($_SESSION["prenom"], 0, 1);
$initiale_n = substr($_SESSION["nom"], 0, 1);
$initiales = strtoupper($initiale_p . $initiale_n);
$histo_file = 'histo_commande.json';
$historique_commandes = [];

if (file_exists($histo_file)) {
    $historique_commandes = json_decode(file_get_contents($histo_file), true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="couleur.css">
    <script src="darkmode.js"></script>
</head>
<body>

    <div class="profil">
        
        <div class="profil-header">
            <div class="avatar-logo"><?php echo $initiales; ?></div>
            <h2>Mon Profil</h2>
            <p>Heureux de vous revoir, <?php echo $_SESSION["prenom"]; ?> !</p>
        </div>

        <div class="formulaire-utopik">
            
            <?php if ($message_profil !== ""): ?>
                <div class="message-profil <?php echo $classe_message; ?>">
                    <?php echo $message_profil; ?>
                </div>
            <?php endif; ?>

            <div class="section">
                <div class="categorie">
                    <label>Informations & Sécurité</label>
                </div>
                
                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Prénom</span>
                        <p><?php echo $_SESSION["prenom"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le prénom" onclick="ouvrirModale('prenom', 'Prénom', '<?php echo $_SESSION['prenom']; ?>', 'text')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Nom</span>
                        <p><?php echo $_SESSION["nom"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le nom" onclick="ouvrirModale('nom', 'Nom', '<?php echo $_SESSION['nom']; ?>', 'text')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Email</span>
                        <p><?php echo $_SESSION["email"]; ?></p> 
                    </div>
                    <button type="button" class="btn-edit" title="Modifier l'email" onclick="ouvrirModale('email', 'Email', '<?php echo $_SESSION['email']; ?>', 'email')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Téléphone</span>
                        <p><?php echo $_SESSION["tel"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le téléphone" onclick="ouvrirModale('tel', 'Téléphone', '<?php echo $_SESSION['tel']; ?>', 'tel')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Adresse de livraison</span>
                        <p><?php echo $_SESSION["adresse"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier l'adresse" onclick="ouvrirModale('adresse', 'Adresse de livraison', '<?php echo $_SESSION['adresse']; ?>', 'text')">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Mot de passe</span>
                        <p>********</p>
                    </div>
                    <button type="button" class="btn-edit" title="Changer de mot de passe" onclick="ouvrirModale('mdp', 'Mot de passe', '', 'password')">
                        <i class="fas fa-key"></i>
                    </button>
                </div>
            </div>

            <div class="section">
                <div class="categorie">
                    <label>Fidélité</label>
                    <span class="badge-points">450 pts</span>
                </div>
                <div class="fidelite-container">
                    <div class="barre-progression">
                        <div class="barre-remplissage" style="width: 75%;"></div>
                    </div>
                    <p class="fidelite">Plus que 50 points avant votre cadeau ! 🎁</p>
                </div>
            </div>

            <div class="section" style="border-bottom: none;">
                <div class="categorie">
                    <label>Historique des commandes</label>
                </div>
                <ul class="liste-commandes">
               <?php
foreach ($historique_commandes as $commande) {
    if (
        $commande["nom"] == $_SESSION["nom"] &&
        $commande["prenom"] == $_SESSION["prenom"]
    ) {

        echo "<li>";
        echo "<div>";

        foreach ($commande["panier"] as $item) {

            if (isset($item["nom_menu"])) {
                echo $item["quantite"] . "x " . $item["nom_menu"] . "<br>";
            } else {
                echo $item["quantite"] . "x " . $item["nom_plat"] . "<br>";
            }

        }

        echo "</div>";
        echo "<span class='date'>" . $commande["date"] . "</span>";
        echo "</li>";

    } 
}
?>
                </ul>
            </div>
            <div class="actions-profil">
            <a href="suivie.php">
                <button type="button" class="btn-suivie" >regarder le suivie de commande</button>
            </a>
                <a href="accueil.php">
                    <button type="button" class="btn-principal">Retour à l'accueil</button>
                </a>

                <a href="deconnexion.php">
                    <button type="button" class="btn-deconnexion">
                        <i class="fas fa-sign-out-alt"></i> Se déconnecter
                    </button>
                </a>
            </div>

        </div>
    </div>

    <div id="modale-edition" class="modale-fond">
        <div class="modale-contenu">
            <h3 id="modale-titre">Modifier</h3>
            
            <p id="modale-erreur" style="color: #ff4d4d; font-size: 12px; margin-top: -5px; margin-bottom: 15px; display: none; font-weight: 600; line-height: 1.4;"></p>
            
            <form id="form-modale" action="" method="post">
                <input type="hidden" id="champ_nom" name="champ_nom">
                <div class="champ-saisie-modale">
                    <input type="text" id="champ_valeur" name="champ_valeur" required>
                </div>
                <div class="boutons-modale">
                    <button type="button" class="btn-annuler" onclick="fermerModale()">Annuler</button>
                    <button type="submit" name="modifier_champ" class="btn-valider">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function ouvrirModale(nomChamp, labelChamp, valeurActuelle, typeInput) {
        document.getElementById('champ_nom').value = nomChamp;
        document.getElementById('modale-titre').textContent = "Modifier : " + labelChamp;
        document.getElementById('modale-erreur').style.display = 'none'; 
        
        let inputValeur = document.getElementById('champ_valeur');
        inputValeur.type = typeInput;
        inputValeur.value = valeurActuelle;
        
        if(typeInput === 'tel') {
            inputValeur.maxLength = 14;
        } else {
            inputValeur.removeAttribute('maxLength');
        }

        document.getElementById('modale-edition').style.display = 'flex';
    }

    function fermerModale() {
        document.getElementById('modale-edition').style.display = 'none';
    }

    window.onclick = function(event) {
        let modale = document.getElementById('modale-edition');
        if (event.target == modale) {
            fermerModale();
        }
    }

    document.getElementById("form-modale").addEventListener("submit", function(e) {
        let champNom = document.getElementById("champ_nom").value;
        let champValeur = document.getElementById("champ_valeur").value;
        let pErreur = document.getElementById("modale-erreur");

        if (champNom === "mdp") {
            let vLongueur = champValeur.length >= 12;
            let vMajuscule = /[A-Z]/.test(champValeur);
            let vChiffre = /[0-9]/.test(champValeur);
            let vSpecial = /[^A-Za-z0-9]/.test(champValeur);

            if (!vLongueur || !vMajuscule || !vChiffre || !vSpecial) {
                e.preventDefault(); 
                pErreur.textContent = "❌ Le mot de passe doit contenir 12 caractères minimum, une majuscule, un chiffre et un caractère spécial.";
                pErreur.style.display = "block"; 
            }
        }
    });
    </script>

</body>
</html>
