<?php
session_start();

if (!isset($_SESSION["prenom"])) {
    header("Location: connexion_au_compte.php");
    exit();
}

$initiale_p = mb_substr($_SESSION["prenom"], 0, 1);
$initiale_n = mb_substr($_SESSION["nom"], 0, 1);
$initiales = mb_strtoupper($initiale_p . $initiale_n);
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
</head>
<body>

    <div class="profil">
        
        <div class="profil-header">
            <div class="avatar-logo"><?php echo $initiales; ?></div>
            <h2>Mon Profil</h2>
            <p>Heureux de vous revoir, <?php echo $_SESSION["prenom"]; ?> !</p>
        </div>

        <div class="formulaire-utopik">
            
            <div class="section">
                <div class="categorie">
                    <label>Informations & Sécurité</label>
                </div>
                
                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Nom & Prénom</span>
                        <p><?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le nom">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Email</span>
                        <p><?php echo $_SESSION["email"]; ?></p> 
                    </div>
                    <button type="button" class="btn-edit" title="Modifier l'email">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Téléphone</span>
                        <p><?php echo $_SESSION["tel"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier le téléphone">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Adresse de livraison</span>
                        <p><?php echo $_SESSION["adresse"]; ?></p>
                    </div>
                    <button type="button" class="btn-edit" title="Modifier l'adresse">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Mot de passe</span>
                        <p><?php echo $_SESSION["mdp_masque"]; ?></p>
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
                    <p class="fidelite">Plus que 50 points avant votre cadeau ! 🎁</p>
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

            <div class="actions-profil">
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

</body>
</html>
