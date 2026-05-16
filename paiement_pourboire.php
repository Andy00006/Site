<?php
session_start();
require('getapikey.php');

if (!isset($_SESSION['pourboire']) || $_SESSION['pourboire'] <= 0) {
    header("Location: accueil.php");
    exit();
}

$vendeur = "MI-4_H"; 
$api_key = getAPIKey($vendeur);
$montant_pourboire = $_SESSION['pourboire'];
$montant = number_format($montant_pourboire, 2, '.', '');
$transaction = substr(md5(uniqid(rand(), true)), 0, 15);

$protocol = "http://";
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $protocol = "https://";
}

$host = $_SERVER['HTTP_HOST'];
$retour = $protocol . $host . "/accueil.php"; 

$chaine = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $retour . "#";
$control = md5($chaine);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement du Pourboire - Exotique Dream</title>
    <link rel="stylesheet" href="paiement_pourboire.css">
    <link rel="stylesheet" href="site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="card">
    <div class="logo">
        EXOTIQUE<span> DREAM</span>
    </div>
    <div>
        <i class="fas fa-hand-holding-usd"></i>
    </div>
    <h2>Règlement du pourboire</h2>
    <p>
        Vous vous apprêtez à verser un pourboire pour votre livreur <strong>Yves Oikeudal</strong>.
    </p>
    <div class="total">
        Montant : <?= $montant ?> €
    </div>
    <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
        <input type="hidden" name="transaction" value="<?= $transaction ?>">
        <input type="hidden" name="montant" value="<?= $montant ?>">
        <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
        <input type="hidden" name="retour" value="<?= $retour ?>">
        <input type="hidden" name="control" value="<?= $control ?>">
        <button type="submit" class="btn-pay">
            <i class="fas fa-credit-card"></i> PAYER LE POURBOIRE
        </button>
    </form>
    <a href="accueil.php" class="link-cancel">
        Annuler et retourner à l'accueil
    </a>
</div>
</body>
</html>
