<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
require('getapikey.php');

$transaction = $_GET['transaction'] ?? '';
$montant = $_GET['montant'] ?? '';
$vendeur = $_GET['vendeur'] ?? '';
$status = $_GET['status'] ?? '';
$control = $_GET['control'] ?? '';

$api_key = getAPIKey($vendeur);



$control_local = md5(
    $api_key . "#" .
    $transaction . "#" .
    $montant . "#" .
    $vendeur . "#" .
    $status . "#"
);


if ($control === $control_local) {

    if ($status === "accepted") {
        echo "<h2>Paiement accepté ✅</h2>";
    } else {
        echo "<h2>Paiement refusé ❌</h2>";
    }

} else {
    echo "<h2>Erreur : données falsifiées ⚠️</h2>";
}
?>
</body>
</html>
