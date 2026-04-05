<?php
session_start();
require('getapikey.php');

$vendeur = "MI-4_H"; 
$api_key = getAPIKey($vendeur);

if (isset($_SESSION['nom'])) {
    $nom_client = $_SESSION['nom'];
} else {
    $nom_client = 'Nom';
}

if (isset($_SESSION['prenom'])) {
    $prenom_client = $_SESSION['prenom'];
} else {
    $prenom_client = 'Prénom';
}

if (isset($_SESSION['panier'])) {
    $panier = $_SESSION['panier'];
} else {
    $panier = [];
}
if (isset($_SESSION['adresse'])) {
    $adresse = $_SESSION['adresse'];
} else {
    $atel = 'adresse';
}
if (isset($_SESSION['tel'])) {
    $tel = $_SESSION['tel'];
} else {
    $tel = 'telephone';
}
$total_panier = 0;
foreach ($panier as $item) {
    $total_panier += $item['prix'] * $item['quantite'];
}
$montant = number_format($total_panier, 2, '.', '');
if (isset($_POST['moment_retrait'])) {
    $mode_choisi = $_POST['moment_retrait'];
} else {
    $mode_choisi = 'immediat';
}
$transaction = substr(md5(uniqid(rand(), true)), 0, 15);
$retour = "http://localhost/retour_paiement.php"; 
$concatenation = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $retour . "#";
$control = md5($concatenation);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation - Exotique Dream</title>
    <link rel="stylesheet" href="validation.css">
</head>
<body>
<div class="card">
    <h2>Résumé de la commande</h2>
    <p>Client : <?= htmlspecialchars($prenom_client) ?> <?= htmlspecialchars($nom_client) ?></p>

    <?php foreach ($panier as $item): ?>
        <div class="recap-item">
            <span><strong><?= $item['quantite'] ?>x</strong> <?= htmlspecialchars($item['nom']) ?></span>
            <span><?= number_format($item['prix'] * $item['quantite'], 2) ?>€</span>
        </div>
    <?php endforeach; ?>

    <div class="total">TOTAL : <?= $montant ?> €</div>
    <div class="options">
        <form method="POST" action="validation.php" id="form_mode">
            <strong>Quand préparer la commande ?</strong><br><br>
            <input type="radio" name="moment_retrait" value="immediat" 
                   <?php if($mode_choisi == 'immediat'){
    echo 'checked';
} else {
    echo '';
} ?> 
                   onchange="this.form.submit()"> Immédiat
            
            <input type="radio" name="moment_retrait" value="plus_tard" 
                   <?php if ($mode_choisi == 'plus_tard'){ echo 'checked';}else{ echo '';}?> 
                   onchange="this.form.submit()"> Plus tard

            <?php if ($mode_choisi == 'plus_tard'): ?>
                <div style="margin-top:15px; padding-top:10px; border-top: 1px solid #ccc;">
                    <p style="font-size: 13px; color: #666;">Choisissez votre créneau :</p>
                    <input type="date" name="date_p" required min="<?= date('Y-m-d') ?>">
                    <input type="time" name="heure_p" required>
                </div>
            <?php endif; ?>
        </form>
    </div>
     <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
    <input type="hidden" name="transaction" value="<?= $transaction ?>">
    <input type="hidden" name="montant" value="<?= $montant ?>">
    <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
    <input type="hidden" name="retour" value="<?= $retour ?>">
    <input type="hidden" name="control" value="<?= $control ?>">
    <button type="submit" class="btn-pay">PROCÉDER AU RÈGLEMENT (<?= $montant ?>€)</button>
</form>
</div>

</body>
</html>
