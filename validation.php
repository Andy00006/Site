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

if (isset($_SESSION['adresse'])) {
    $adresse = $_SESSION['adresse'];
} else {
    $adresse = 'Non renseignée';
}

if (isset($_SESSION['tel'])) {
    $tel = $_SESSION['tel'];
} else {
    $tel = 'Non renseigné';
}

$est_une_modification = isset($_SESSION['montant_complement_a_payer']);

if ($est_une_modification) {
    $total_panier = (float)$_SESSION['montant_complement_a_payer'];
    $panier_affichage = array(
        array(
            'quantite' => 1,
            'nom' => "Complément de modification de commande",
            'prix' => $total_panier
        )
    );
} else {
    if (isset($_SESSION['panier'])) {
        $panier_affichage = $_SESSION['panier'];
    } else {
        $panier_affichage = array();
    }
    
    $total_panier = 0;
    foreach ($panier_affichage as $item) {
        $total_panier += $item['prix'] * $item['quantite'];
    }
}

$montant = number_format($total_panier, 2, '.', '');

if (isset($_POST['moment_retrait'])) {
    $mode_choisi = $_POST['moment_retrait'];
} else {
    $mode_choisi = 'immediat';
}

if (isset($_POST['date_p'])) {
    $date_p = $_POST['date_p'];
} else {
    $date_p = date('Y-m-d');
}

if (isset($_POST['heure_p'])) {
    $heure_p = $_POST['heure_p'];
} else {
    $heure_p = date('H:i');
}

if ($mode_choisi === 'plus_tard') {
    $_SESSION['heure_choisie'] = date('d/m', strtotime($date_p)) . " à " . $heure_p;
} else {
    $_SESSION['heure_choisie'] = "Immédiat";
}

$transaction = substr(md5(uniqid(rand(), true)), 0, 15);

$protocol = "http://";
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $protocol = "https://";
}

$host = $_SERVER['HTTP_HOST'];
$retour = $protocol . $host . "/retour_paiement.php";

$chaine = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $retour . "#";
$control = md5($chaine);
?>
<!DOCTYPE html>
<html lang="fr">
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
        <p style="margin: 5px 0;"><strong>Client :</strong> <?php echo htmlspecialchars($prenom_client); ?> <?php echo htmlspecialchars($nom_client); ?></p>
        <p style="margin: 5px 0;"><strong>Livraison :</strong> <?php echo htmlspecialchars($adresse); ?></p>
        <p style="margin: 5px 0;"><strong>Contact :</strong> <?php echo htmlspecialchars($tel); ?></p>
    </div>

    <div class="liste-articles">
        <?php foreach ($panier_affichage as $item): ?>
            <div class="recap-item">
                <span><strong><?php echo $item['quantite']; ?>x</strong> <?php echo htmlspecialchars($item['nom']); ?></span>
                <span><?php echo number_format($item['prix'] * $item['quantite'], 2); ?>€</span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="total">
        Total à payer : <?php echo $montant; ?> €
    </div>

    <div class="options">
        <form method="POST" action="validation.php" id="form-moment">
            <strong>Préparation :</strong><br><br>
            <label>
                <input type="radio" name="moment_retrait" value="immediat" <?php if ($mode_choisi == 'immediat') { echo 'checked'; } ?> onchange="this.form.submit()"> Immédiat
            </label>
            <label style="margin-left: 15px;">
                <input type="radio" name="moment_retrait" value="plus_tard" <?php if ($mode_choisi == 'plus_tard') { echo 'checked'; } ?> onchange="this.form.submit()"> Plus tard
            </label>

            <?php if ($mode_choisi == 'plus_tard'): ?>
                <div style="margin-top:15px; border-top: 1px dashed #ccc; padding-top: 10px;">
                    <p style="font-size: 13px; color: #666; margin-bottom: 5px;">Choisir la date et l'heure :</p>
                    <input type="date" name="date_p" value="<?php echo $date_p; ?>" min="<?php echo date('Y-m-d'); ?>" onchange="this.form.submit()">
                    <input type="time" name="heure_p" value="<?php echo $heure_p; ?>" onchange="this.form.submit()">
                </div>
            <?php endif; ?>
        </form>
    </div>

    <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
        <input type="hidden" name="transaction" value="<?php echo $transaction; ?>">
        <input type="hidden" name="montant" value="<?php echo $montant; ?>">
        <input type="hidden" name="vendeur" value="<?php echo $vendeur; ?>">
        <input type="hidden" name="retour" value="<?php echo $retour; ?>">
        <input type="hidden" name="control" value="<?php echo $control; ?>">

        <button type="submit" class="btn-pay" <?php if ($total_panier <= 0) { echo 'disabled'; } ?>>
            PROCÉDER AU PAIEMENT (<?php echo $_SESSION['heure_choisie']; ?>)
        </button>
    </form>
</div>

</body>
</html>
