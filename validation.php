<?php
session_start();
require('getapikey.php');

$vendeur = "MI-1_A"; 
$api_key = getAPIKey($vendeur);


$total_panier = 0;
if (!empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $article) {
        $total_panier += $article['prix'] * $article['quantite'];
    }
}


$montant = number_format($total_panier, 2, '.', ''); 

$transaction = substr(md5(uniqid(rand(), true)), 0, 15);


$retour = "http://localhost/retour_paiement.php"; 

$concatenation = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $retour . "#";
$control = md5($concatenation);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation du Rêve</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; }
        .card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; width: 300px; text-align: center; }
        .btn-valider { background: #000; color: #fff; border: none; padding: 10px 20px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <div class="card">
        <h3>VOTRE RÉCAPITULATIF</h3>
        <p>Total à payer : <strong><?php echo $montant; ?>€</strong></p>

        <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
            
            <input type="hidden" name="transaction" value="<?php echo $transaction; ?>"> <input type="hidden" name="montant" value="<?php echo $montant; ?>"> <input type="hidden" name="vendeur" value="<?php echo $vendeur; ?>"> <input type="hidden" name="retour" value="<?php echo $retour; ?>"> <input type="hidden" name="control" value="<?php echo $control; ?>"> <button type="submit" class="btn-valider">PROCÉDER AU PAIEMENT</button> </form>
    </div>
</body>
</html>
