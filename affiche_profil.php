<?php
session_start();

if (!isset($_SESSION["prenom"])) {
    header("Location: connexion_au_compte.php");
    exit();
}
$json_content = file_get_contents('utilisateurs.json');
$profils = json_decode($json_content, true);
$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$profil_actuel = null;
foreach ($profils as $profil) {
    if ($profil['id'] == $id_demande) {
        $profil_actuel = $profil;
        break; 
    }
}

$longueur_mdp = strlen($profil["mdp"]);
$profil_actuel["mdp_masque"] = str_repeat("•", $longueur_mdp);
$initiale_p = substr($profil_actuel["prenom"], 0, 1);
$initiale_n = substr($profil_actuel["nom"], 0, 1);
$initiales = strtoupper($initiale_p . $initiale_n);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>

    <div class="profil">
        
        <div class="profil-header">
            <div class="avatar-logo"><?php echo $initiales; ?></div>
        </div>

        <div class="formulaire-utopik">
            
            <div class="section">
                <div class="categorie">
                    <label>Informations & Sécurité</label>
                </div>
                
                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Nom & Prénom</span>
                        <p><?php echo $profil_actuel["prenom"] . " " . $profil_actuel["nom"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le nom">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Email</span>
                        <p><?php echo $profil_actuel["email"]; ?></p> 
                    </div>
                    <button type="button" class="btn-edit" title="Modifier l'email">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Téléphone</span>
                        <p><?php echo $profil_actuel["tel"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le téléphone">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Adresse de livraison</span>
                        <p><?php echo $profil_actuel["adresse"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier l'adresse">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Mot de passe</span>
                        <p><?php echo $profil_actuel["mdp_masque"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Changer de mot de passe">
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
                    <p class="fidelite">Plus que 50 points avant son cadeau ! 🎁</p>
                </div>
            </div>

            <div class="section" style="border-bottom: none;">
                <div class="categorie">
                    <label>Historique des commandes</label>
                </div>
                <ul class="liste-commandes">
                    <li><span>1000 et une Pâte</span><span class="date">Hier</span></li>
                    <li><span>Viande de Dodo</span><span class="date">10 Fév.</span></li>
                </ul>
            </div>

        </div>
    </div>

</body>
</html>
