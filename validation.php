<?php
session_start();
require('getapikey.php');

$vendeur = "MI-4_H"; 
$api_key = getAPIKey($vendeur);

$nom_client = $_SESSION['nom'] ?? 'Nom';
$prenom_client = $_SESSION['prenom'] ?? 'Prénom';
$panier = $_SESSION['panier'] ?? [];
$adresse = $_SESSION['adresse'] ?? 'Non renseignée';
$tel = $_SESSION['tel'] ?? 'Non renseigné';

$total_panier = 0;
foreach ($panier as $item) {
    $total_panier += $item['prix'] * $item['quantite'];
}
$montant = number_format($total_panier, 2, '.', '');

$mode_choisi = $_POST['moment_retrait'] ?? 'immediat';
$date_p = $_POST['date_p'] ?? date('Y-m-d');
$heure_p = $_POST['heure_p'] ?? date('H:i');

if ($mode_choisi === 'plus_tard') {
    $_SESSION['heure_choisie'] = date('d/m', strtotime($date_p)) . " à " . $heure_p;
} else {
    $_SESSION['heure_choisie'] = "Immédiat";
}

$transaction = substr(md5(uniqid(rand(), true)), 0, 15);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$directory = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$retour = $protocol . $host . $directory . "/retour_paiement.php";

$chaine = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $retour . "#";
$control = md5($chaine);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Validation - Exotique Dream</title>
    <link rel="stylesheet" href="validation.css">
    <link rel="stylesheet" href="site.css">
</head>
<body>

<div class="card">
    <h2>Résumé de ma commande</h2>

    <div class="recap-client" style="background: #fdfdfd; border: 1px solid #eee; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
        <p style="margin: 5px 0;"><strong>Client :</strong> <?= htmlspecialchars($prenom_client) ?> <?= htmlspecialchars($nom_client) ?></p>
        <p style="margin: 5px 0;"><strong>Livraison :</strong> <?= htmlspecialchars($adresse) ?></p>
        <p style="margin: 5px 0;"><strong>Contact :</strong> <?= htmlspecialchars($tel) ?></p>
    </div>

    <div class="liste-articles">
        <?php foreach ($panier as $item): ?>
            <div class="recap-item">
                <span><strong><?= $item['quantite'] ?>x</strong> <?= htmlspecialchars($item['nom']) ?></span>
                <span><?= number_format($item['prix'] * $item['quantite'], 2) ?>€</span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="total">
        Total : <?= $montant ?> €
    </div>

    <div class="options">
        <form method="POST" action="validation.php" id="form-moment">
            <strong>Préparation :</strong><br><br>
            <label>
                <input type="radio" name="moment_retrait" value="immediat" <?= ($mode_choisi == 'immediat') ? 'checked' : '' ?> onchange="this.form.submit()"> Immédiat
            </label>
            <label style="margin-left: 15px;">
                <input type="radio" name="moment_retrait" value="plus_tard" <?= ($mode_choisi == 'plus_tard') ? 'checked' : '' ?> onchange="this.form.submit()"> Plus tard
            </label>

            <?php if ($mode_choisi == 'plus_tard'): ?>
                <div style="margin-top:15px; border-top: 1px dashed #ccc; padding-top: 10px;">
                    <p style="font-size: 13px; color: #666; margin-bottom: 5px;">Choisir la date et l'heure :</p>
                    <input type="date" name="date_p" value="<?= $date_p ?>" min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                    <input type="time" name="heure_p" value="<?= $heure_p ?>" onchange="this.form.submit()">
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

        <button type="submit" class="btn-pay" <?= ($total_panier <= 0) ? 'disabled' : '' ?>>
            PROCÉDER AU PAIEMENT (<?= $_SESSION['heure_choisie'] ?>)
        </button>
    </form>
</div>

</body>
</html>
