<?php
session_start();
include("getapikey.php"); 

if (empty($_SESSION['panier'])) {
    header("Location: menu.php");
    exit();
}

$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}

$taux_remise = $_SESSION['remise'] ?? 0;
$total_final = $total - ($total * ($taux_remise / 100));

$vendeur = "MI-4_A"; 
$url_cybank = "https://www.plateforme-smc.fr/cybank/";

$cle_secrete = getAPIKey($vendeur); 


$clicontrol = sha1($vendeur . $total_final . $cle_secrete);
$montant_pur = number_format($total_final, 2, '.', '');

$chaine = $vendeur . $montant_pur . $cle_secrete;

$clicontrol = sha1($chaine);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation de commande</title>
    <link rel="stylesheet" href="validation.css">
    <link rel="stylesheet" href="couleur.css">

</head>
<body>

<div class="cadre-validation">
    <h2>Résumé final</h2>
    
    <?php foreach ($_SESSION['panier'] as $article): ?>
        <div class="recap-item">
            <span><?= $article['quantite'] ?>x <?= htmlspecialchars($article['nom']) ?></span>
            <span><?= number_format($article['prix'] * $article['quantite'], 2) ?>€</span>
        </div>
    <?php endforeach; ?>

    <hr>
    <div class="recap-item" style="font-weight: bold; font-size: 1.2em;">
        <span>Total à régler :</span>
        <span><?= number_format($total_final, 2) ?>€</span>
    </div>

    <form action="<?= $url_cybank ?>" method="POST">
        <p style="margin-top: 20px;"><strong>Adresse de livraison :</strong></p>
        <input type="text" name="adresse" style="width:100%; padding: 10px;" required>

        <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
        <input type="hidden" name="montant" value="<?= $total_final ?>">
        <input type="hidden" name="control" value="<?= $clicontrol ?>">
        <input type="hidden" name="session" value="<?= session_id() ?>">

        <button type="submit" class="btn-payer">Payer sur CYBank</button>
    </form>
</div>

</body>
</html>
