<?php
session_start();
require('getapikey.php');

$vendeur = "MI-4_H"; 
$api_key = getAPIKey($vendeur);

$panier = $_SESSION['panier'] ?? [];
$total_panier = 0;
foreach ($panier as $item) {
    $total_panier += $item['prix'] * $item['quantite'];
}
$montant = number_format($total_panier, 2, '.', '');

$mode_choisi = $_POST['moment_retrait'] ?? 'immediat';

$transaction = substr(md5(uniqid(rand(), true)), 0, 15);
$retour = "http://localhost/vrai/menu.php"; 
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
    <h2>Résumé de ma commande</h2>

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
                   <?= ($mode_choisi == 'immediat') ? 'checked' : '' ?> 
                   onchange="this.form.submit()"> Immédiat
            
            <input type="radio" name="moment_retrait" value="plus_tard" 
                   <?= ($mode_choisi == 'plus_tard') ? 'checked' : '' ?> 
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
        
        <button type="submit" class="btn-pay">CONFIRMER ET PAYER</button>
    </form>
</div>

</body>
</html>
