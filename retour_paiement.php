<?php
session_start();
date_default_timezone_set('Europe/Paris');
if (isset($_GET['etat']) && $_GET['etat'] == 'ok') {
    
    $commandes_file = 'commandes.json';

    if (isset($_SESSION['nom'])) { $nom = $_SESSION['nom']; } else { $nom = 'Nom'; }
    if (isset($_SESSION['prenom'])) { $prenom = $_SESSION['prenom']; } else { $prenom = 'Prénom'; }
    if (isset($_SESSION['panier'])) { $panier = $_SESSION['panier']; } else { $panier = []; }
    if (isset($_SESSION['adresse_livraison'])) { $adresse = $_SESSION['adresse_livraison']; } else { $adresse = 'Non précisée'; }
    if (isset($_SESSION['tel'])) { $tel = $_SESSION['tel']; } else { $tel = '0600000000'; }
    if (isset($_SESSION['dernier_montant'])) { $montant = $_SESSION['dernier_montant']; } else { $montant = '0.00'; }

    if (!empty($panier)) {
        if (file_exists($commandes_file)) {
            $contenu = file_get_contents($commandes_file);
            $commandes = json_decode($contenu, true);
            if ($commandes === null) {
                $commandes = [];
            }
        } else {
            $commandes = [];
        }

        $nouvel_id = 1;
        if (!empty($commandes)) {
            $derniere_cmd = end($commandes);
            $nouvel_id = $derniere_cmd['id'] + 1;
        }

        $panier_cuisine = [];
        foreach ($panier as $item) {
            $id_item = $item['id'];
            if (strpos((string)$id_item, 'menu_') === 0) {
                $panier_cuisine[] = [
                    'type' => 'menu',
                    'nom_menu' => $item['nom'],
                    'quantite' => $item['quantite']
                ];
            } else {
                $panier_cuisine[] = [
                    'type' => 'plat',
                    'id_produit' => (int)$id_item,
                    'quantite' => $item['quantite'],
                    'nom_plat' =>$nom['nom']
                ];
            }
        }

        $nouvelle_commande = [
            "id" => $nouvel_id,
            "nom" => $nom,
            "prenom" => $prenom,
            "adresse" => $adresse,
            "tel" => $tel,
            "heure" => date('H:i'),
            "statut" => "a_preparer",
            "panier" => $panier_cuisine,
            "total" => $montant,
            "info_livraison" => ""
        ];

        $commandes[] = $nouvelle_commande;
        $texte_json = json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($commandes_file, $texte_json);

        unset($_SESSION['panier']);
        
        $message = "Paiement validé ! Votre commande est en préparation.";
        $classe = "success";
    } else {
        $message = "Erreur : Commande déjà traitée ou panier vide.";
        $classe = "error";
    }
} else {
    $message = "Le paiement n'a pas pu être finalisé.";
    $classe = "error";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation - Exotique Dream</title>
    <link rel="stylesheet" href="couleur.css">
    <style>
        .reponse { text-align: center; margin-top: 50px; font-family: Arial; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .btn-home { display: inline-block; padding: 10px 20px; background: #333; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="reponse">
        <h1 class="<?php echo $classe; ?>"><?php echo $message; ?></h1>
        <p>Merci de votre confiance.</p>
        <br>
        <a href="index.php" class="btn-home">Retour à l'accueil</a>
    </div>
</body>
</html>
