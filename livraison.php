<?php
session_start();

$commandes_file = 'commandes.json';
$commande_active = null;

if (file_exists($commandes_file)) {
    $contenu = file_get_contents($commandes_file);
    $commandes = json_decode($contenu, true);
    
    if ($commandes === null) {
        $commandes = [];
    }
} else {
    $commandes = [];
}

foreach ($commandes as $cmd) {
    if (isset($cmd['statut'])) {
        if ($cmd['statut'] == 'en_livraison') {
            $commande_active = $cmd;
            break;
        }
    }
}

    if (isset($_POST['finaliser_commande'])) {
        $id_fin = $_POST['commande_id'];
        foreach ($commandes as &$c) {
            if ($c['id'] == $id_fin) {
                $c['statut'] = 'livre';
                break;
            }
        }
        file_put_contents($commandes_file, json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: livraison.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livraison - Exotique Dream</title>
    <link rel="stylesheet" href="livraison.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>

    <div class="conteneur-livreur">
        
        <header class="entete-livreur">
            <?php if ($commande_active): ?>
                <span>Commande <strong>#<?php echo $commande_active['id']; ?></strong></span>
            <?php else: ?>
                <span>Aucune livraison</span>
            <?php endif; ?>
            <div class="logo"><span>Exotique</span> Dream</div>
        </header>

        <main class="zone-livraison">
            
            <?php if ($commande_active): ?>
                <section class="bloc-client">
                    <h2><?php echo $commande_active['prenom'] . " " . $commande_active['nom']; ?></h2>
                    <div class="liste-plats-simple">
                        <?php foreach ($commande_active['panier'] as $item): ?>
                            <p>- <?php echo $item['quantite']; ?>x 
                                <?php 
                                if (isset($item['nom_menu'])) {
                                    echo $item['nom_menu'];
                                } else {
                                    echo "Plat: " . $item['nom_plat'];
                                }
                                ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="bloc-adresse">
                    <div class="icone-destination">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <p class="rue"><?php echo $commande_active['adresse']; ?></p>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($commande_active['adresse']); ?>" class="bouton-gps" target="_blank">
                        LANCER LE GPS
                    </a>
                </section>

                <div class="actions-livreur">
                    <a href="tel:<?php echo $commande_active['tel'] ?? '0600000000'; ?>" class="bouton-appel">
                        <i class="fas fa-phone-alt"></i> APPELER (<?php echo $commande_active['tel'] ?? 'N/A'; ?>)
                    </a>
                    
                    <form method="POST" style="width: 100%;">
                        <input type="hidden" name="commande_id" value="<?php echo $commande_active['id']; ?>">
                        <button type="submit" name="finaliser_commande" class="bouton-fin-livraison">
                            CONFIRMER LA LIVRAISON
                        </button>
                    </form>
                    <button class="bouton-erreur" style="background: #e74c3c; color: white; width: 100%; padding: 15px; border: none; border-radius: 12px; font-weight: bold; margin-top: 10px;">
                        SIGNALER UNE ERREUR
                    </button>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-box-open" style="font-size: 50px; color: #ccc;"></i>
                    <p>Aucune commande à livrer pour le moment.</p>
                </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>
