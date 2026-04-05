<?php
session_start();
require_once 'deco.php';
$est_connecte = isset($_SESSION["prenom"]);

if ($est_connecte) {
    $initiale_prenom = strtoupper(substr($_SESSION["prenom"], 0, 1));
    $initiale_nom = strtoupper(substr($_SESSION["nom"], 0, 1));
    $initiales = $initiale_prenom . $initiale_nom;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Exotique Dream</title>
    <link rel="stylesheet" href="accueil.css">
    <link rel="stylesheet" href="site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>
    <header class="header">
        <div>
            <a href="accueil.php" class="logo">EXOTIQUE<span>DREAM</span></a>
        </div>
        <nav class="milieu">
            <a href="accueil.php" class="active">Accueil</a>
            <a href="com.php">Communication</a>
            <a href="menu.php">Menu</a>
            <a href="loc.php">Localisation</a>
            <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "Admin"): ?>
                <a href="administrateur.php" style="color: var(--fraise); font-weight: bold;">
                    <i class="fas fa-lock"></i> Admin
                </a>
            <?php endif; ?>
        </nav>
        <div class="droite">
            <?php if ($est_connecte): ?>
                <a href="profil.php" class="avatar-lien">
                    <div class="avatar-cercle">
                        <?php echo $initiales; ?>
                    </div>
                </a>
                <a href="deconnexion.php" class="bouton-inscription">Déconnexion</a>
            <?php else: ?>
                <a href="connexion_au_compte.php" class="bouton-connexion">Connexion</a>
                <a href="creation_de_compte.php" class="bouton-inscription">Inscription</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="fond">
        <div class="voile"></div>
        
        <div class="contenu">
            <div class="bloc-texte-gauche">
                <span class="etiquette">OUVERT TOUS LES JOURS</span>
                <h1>Bienvenue dans votre plus beau<span> Rêve</span>.</h1>
                
                <div class="recherche">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Découvrez nos spécialités..." class="entree-recherche">
                    <button class="bouton-recherche">EXPLORER</button>
                </div>
                
                <p class="texte-bas">Un moment de détente et de saveurs partagées.</p>
            </div>

            <div class="bloc-deroulant">
                <div class="vitrine">
                    <div class="texte-vitrine">EN CE MOMENT</div>
                    <div class="image">
                        <div class="slide">
                            <img src="image_plat/Le_colesterdestroyeur.png" alt="Plat1">
                            <img src="image_plat/cake_sale_au_sel.png" alt="Plat2">
                            <img src="image_plat/rien_ne_va.png" alt="Plat3">
                            <img src="image_plat/Le_reve_bleu.png" alt="Plat4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-section">
            <h3>Horaires</h3>
            <p>Lun - Ven : 11h00 - 22h30</p>
            <p>Samedi : 12h00 - 23h30</p>
            <p>Dimanche : 12h00 - 21h00</p>
        </div>
        <div class="footer-section">
            <h3>Navigation</h3>
            <a href="accueil.php">Accueil</a>
            <a href="com.php">Communauté</a>
            <a href="menu.php">Menu</a>
            <a href="loc.php">Localisation</a>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p><i class="fas fa-map-marker-alt"></i> - 12 Avenue des Saveurs, Cergy</p>
            <p><i class="fas fa-phone"></i> - 01 23 45 67 89</p>
            <p><i class="fas fa-envelope"></i> - contact@exotiquedream.fr</p>
        </div>
        <div class="footer-bas">
            <p>© 2026 EXOTIQUE DREAM - Tous droits réservés</p>
            <div class="reseaux">
                <a href="https://www.instagram.com/exoticdream__/"><i class="fab fa-instagram"></i></a>
                <a href="https://x.com/ExotiqueDream"><i class="fab fa-twitter"></i></a>
                <a href="https://www.tiktok.com/fr/"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
