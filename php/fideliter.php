<?php
session_start();

if (isset($_SESSION["prenom"])) {
    $est_connecte = true;
} else {
    $est_connecte = false;
}

if (!$est_connecte) {
    header("Location: connexion_au_compte.php");
    exit();
}

if (isset($_SESSION["prenom"]) && isset($_SESSION["nom"])) {
    $initiales = strtoupper(substr($_SESSION["prenom"], 0, 1) . substr($_SESSION["nom"], 0, 1));
} else {
    $initiales = "ED";
}

if (isset($_GET['credit'])) {
    $montant_credit = (float)$_GET['credit'];
} else {
    $montant_credit = 0.0;
}

$montant_formate = number_format($montant_credit, 2, '.', '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/couleur.css">
    <link rel="stylesheet" href="../css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Fidélité - Exotique Dream</title>
    <link rel="stylesheet" href="../css/fideliter.css">
</head>
<body>
    <header class="header">
        <div>
            <a href="accueil.php" class="logo">EXOTIQUE<span>DREAM</span></a>
        </div>
        <nav class="milieu">
            <a href="accueil.php">Accueil</a>
            <a href="menu.php">Menu</a>
            <a href="suivie.php">Suivi Commande</a>
        </nav>
        <div class="droite">
            <a href="compte.php" class="avatar-lien">
                <div class="avatar-cercle"><?php echo $initiales; ?></div>
            </a>
        </div>
    </header>

    <main class="contenant-fidelite">
        <div class="carte-fidelite">
            <div class="badge-fidelite">AVANTAGE CLIENT</div>
            <h2>Modification validée !</h2>
            <p class="description-fidelite">Votre nouveau menu coûte moins cher que votre sélection précédente.</p>
            
            <div class="zone-difference">
                <span class="libelle">Différence à votre avantage</span>
                <span class="valeur-difference"><?php echo $montant_formate; ?> €</span>
            </div>

            <div class="boite-message">
                <p>Vous allez obtenir un coupon de réduction du montant de la différence directement déposé dans votre sac lors du retrait de votre commande.</p>
            </div>

            <a href="suivie.php" class="bouton-retour">Retour au suivi de commande</a>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-section">
            <h3>Exotique Dream</h3>
            <p>Une expérience culinaire unique et mémorable.</p>
        </div>
        <div class="footer-section">
            <h3>Liens Utiles</h3>
            <a href="accueil.php">Accueil</a>
            <a href="menu.php">Notre Carte</a>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p>Service client disponible 7j/7.</p>
        </div>
        <div class="footer-bas">
            <p>&copy; 2026 Exotique Dream. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>