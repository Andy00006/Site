<?php
session_start();
require('getapikey.php');

$status = $_GET['status'] ?? ''; 
$transaction = $_GET['transaction'] ?? '';
$montant = $_GET['montant'] ?? '';
$vendeur = $_GET['vendeur'] ?? '';
$control_banque = $_GET['control'] ?? '';

$api_key = getAPIKey($vendeur);

$chaine_verif = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $status . "#";
$mon_control = md5($chaine_verif);

if ($status === 'accepted' && $mon_control === $control_banque) {
    $fichier = 'commandes.json';
    $commandes = file_exists($fichier) ? json_decode(file_get_contents($fichier), true) : [];

    $nouvelle_commande = [
        "id" => $transaction,
        "nom" => $_SESSION['nom'] ?? 'Client',
        "prenom" => $_SESSION['prenom'] ?? 'Anonyme',
        "adresse" => $_SESSION['adresse'] ?? 'Non précisée',
        "tel" => $_SESSION['tel'] ?? '',
        "heure" => $_SESSION['heure_choisie'] ?? date('H:i'),
        "statut" => "a_preparer",
        "panier" => []
    ];

    foreach ($_SESSION['panier'] as $item) {
        $nouvelle_commande['panier'][] = [
            "type" => "plat",
            "id_produit" => $item['id'],
            "nom_plat" => $item['nom'],
            "quantite" => $item['quantite']
        ];
    }

    $commandes[] = $nouvelle_commande;
    file_put_contents($fichier, json_encode($commandes, JSON_PRETTY_PRINT));
    unset($_SESSION['panier']);
    $message = "Merci ! Votre paiement a été accepté. Votre commande est en préparation.";
} else {
    $message = "Le paiement a été refusé ou les données ont été altérées.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Résultat Paiement</title>
    <link rel="stylesheet" href="site.css">
</head>
<body>
    <div style="text-align:center; margin-top:100px;">
        <h1><?= htmlspecialchars($message) ?></h1>
        <br>
        <a href="accueil.php" style="padding:10px 20px; background:#333; color:white; text-decoration:none; border-radius:5px;">Retour à la boutique</a>
    </div>
</body>
</html>
