<?php
session_start();
require('getapikey.php');

if (isset($_GET['status'])) {
    $status = $_GET['status'];
} else {
    $status = '';
}

if (isset($_GET['transaction'])) {
    $transaction = $_GET['transaction'];
} else {
    $transaction = '';
}

if (isset($_GET['montant'])) {
    $montant = $_GET['montant'];
} else {
    $montant = '';
}

if (isset($_GET['vendeur'])) {
    $vendeur = $_GET['vendeur'];
} else {
    $vendeur = '';
}

if (isset($_GET['control'])) {
    $control_banque = $_GET['control'];
} else {
    $control_banque = '';
}

$api_key = getAPIKey($vendeur);
$chaine_verif = $api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $status . "#";
$mon_control = md5($chaine_verif);

if ($status === 'accepted' && $mon_control === $control_banque) {
    $fichier = 'commandes.json';
    
    if (file_exists($fichier)) {
        $commandes = json_decode(file_get_contents($fichier), true);
    } else {
        $commandes = array();
    }

    if (isset($_SESSION['id_commande_en_cours'])) {
        $id_cmd_cible = $_SESSION['id_commande_en_cours'];
        
        foreach ($commandes as $index => $cmd) {
            if (isset($cmd['id']) && $cmd['id'] == $id_cmd_cible) {
                if (isset($_SESSION['heure_choisie'])) {
                    $commandes[$index]['heure'] = $_SESSION['heure_choisie'];
                }
                break;
            }
        }
        
        unset($_SESSION['montant_complement_a_payer']);
        unset($_SESSION['id_commande_en_cours']);
        unset($_SESSION['panier_modif']);
        
        $message = "Merci ! Votre modification a été payée et prise en compte.";
    } else {
        if (isset($_SESSION['nom'])) {
            $nom_c = $_SESSION['nom'];
        } else {
            $nom_c = 'Client';
        }

        if (isset($_SESSION['prenom'])) {
            $prenom_c = $_SESSION['prenom'];
        } else {
            $prenom_c = 'Anonyme';
        }

        if (isset($_SESSION['adresse'])) {
            $adresse_c = $_SESSION['adresse'];
        } else {
            $adresse_c = 'Non précisée';
        }

        if (isset($_SESSION['tel'])) {
            $tel_c = $_SESSION['tel'];
        } else {
            $tel_c = '';
        }

        if (isset($_SESSION['heure_choisie'])) {
            $heure_c = $_SESSION['heure_choisie'];
        } else {
            $heure_c = date('H:i');
        }

        $nouvelle_commande = array(
            "id" => $transaction,
            "nom" => $nom_c,
            "prenom" => $prenom_c,
            "adresse" => $adresse_c,
            "tel" => $tel_c,
            "heure" => $heure_c,
            "statut" => "a_preparer",
            "panier" => array()
        );

        if (isset($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $item) {
                $nouvelle_commande['panier'][] = array(
                    "type" => "plat",
                    "id_produit" => $item['id'],
                    "nom_plat" => $item['nom'],
                    "quantite" => $item['quantite']
                );
            }
        }

        $commandes[] = $nouvelle_commande;
        unset($_SESSION['panier']);
        $message = "Merci ! Votre paiement a été accepté. Votre commande est en préparation.";
    }

    file_put_contents($fichier, json_encode($commandes, JSON_PRETTY_PRINT));
} else {
    $message = "Le paiement a été refusé ou les données ont été altérées.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat Paiement</title>
    <link rel="stylesheet" href="site.css">
</head>
<body>
    <div style="text-align:center; margin-top:100px;">
        <h1><?php echo htmlspecialchars($message); ?></h1>
        <br>
        <a href="accueil.php" style="padding:10px 20px; background:#333; color:white; text-decoration:none; border-radius:5px;">Retour à la boutique</a>
    </div>
</body>
</html>
