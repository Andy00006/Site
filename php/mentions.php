<?php
$nom_site           = "Exotique Dream";
$url_site           = "https://www.accueil.fr";
$nom_editeur        = "Nicolas Da silva";
$statut_juridique   = "SAS au capital de 2 500 €";
$adresse_editeur    = "12 Avenue des Saveurs, Cergy";
$email_editeur      = "contact@exotiquedream.fr";
$telephone_editeur  = "01 23 45 67 89";
$rcs_editeur        = "RCS Pontoise B 987 654 321";
$tva_editeur        = "FR 88 987 654 321";
$directeur_pub      = "Directeur de la publication";
$nom_hebergeur      = "Xi jinping";
$adresse_hebergeur  = "456 Avenue du Cloud, 69000 Lyon";
$telephone_hebergeur= "04 99 99 99 99";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="couleur.css">
    <link rel="stylesheet" href="mentions.css">
    <link rel="stylesheet" href="site.css">
    <title>Mentions Légales - <?php echo $nom_site; ?></title>
    <script src="darkmode.js"></script>
</head>
<body>
<main class="page-mentions">   
<div class="mentions-bloc">
<h1>Mentions Légales</h1>
    <p>Conformément aux dispositions des articles 6-III et 19 de la Loi n° 2004-575 du 21 juin 2004 pour la Confiance dans l'économie numérique (L.C.E.N.), nous portons à la connaissance des utilisateurs du site les informations suivantes :</p>
    <h2>1. Éditeur du Site</h2>
    <p>Le site Internet <strong><?php echo $nom_site; ?></strong> est édité par :</p>
    <ul>
        <li><strong>Raison sociale :</strong> <?php echo $nom_editeur; ?> (<?php echo $statut_juridique; ?>)</li>
        <li><strong>Adresse :</strong> <?php echo $adresse_editeur; ?></li>
        <li><strong>Téléphone :</strong> <?php echo $telephone_editeur; ?></li>
        <li><strong>Adresse e-mail :</strong> <?php echo $email_editeur; ?></li>
        <li><strong>Immatriculation :</strong> <?php echo $rcs_editeur; ?></li>
        <li><strong>Numéro de TVA intracommunautaire :</strong> <?php echo $tva_editeur; ?></li>
        <li><strong>Directeur de la publication :</strong> <?php echo $directeur_pub; ?></li>
    </ul>
    <h2>2. Hébergeur du Site</h2>
    <p>Le site est hébergé par :</p>
    <ul>
        <li><strong>Nom de l'hébergeur :</strong> <?php echo $nom_hebergeur; ?></li>
        <li><strong>Adresse postale :</strong> <?php echo $adresse_hebergeur; ?></li>
        <li><strong>Téléphone :</strong> <?php echo $telephone_hebergeur; ?></li>
    </ul>
    <h2>3. Propriété Intellectuelle</h2>
    <p>L'ensemble de ce site (textes, images, graphismes, logo, icônes) est la propriété exclusive de <strong><?php echo $nom_editeur; ?></strong>, sauf mention contraire. Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable.</p>
    <h2>4. Gestion des Données Personnelles et Cookies</h2>
    <p>Dans le cadre de ce site, aucune donnée personnelle réelle n'est collectée, et aucun cookie de suivi n'est installé sur votre appareil. Si ce site devenait réel, les utilisateurs disposeraient d'un droit d'accès, de rectification et d'opposition aux données personnelles les concernant conformément au RGPD.</p>
</div>
</main>
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