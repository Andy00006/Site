<?php
date_default_timezone_set('Europe/Paris');

$commandes_file = 'commandes.json';
$menu_file = 'menu.json';

$commandes_brutes = file_exists($commandes_file) ? json_decode(file_get_contents($commandes_file), true) : [];
$menu_data = file_exists($menu_file) ? json_decode(file_get_contents($menu_file), true) : [];

$id_commande = isset($_GET['id']) ? $_GET['id'] : null;
$ma_commande = null;

foreach ($commandes_brutes as $cmd) {
    if ($cmd['id'] === $id_commande) {
        $ma_commande = $cmd;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface Restaurateur</title>
    <link rel="stylesheet" href="contenu.css">
    <link rel="stylesheet" href="couleur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<a href="commandes.php" class="oe">← RETOUR AUX COMMANDES</a>

<div class="ticket">
    <?php if ($ma_commande): ?>
        <div class="header">
            <h2 style="margin:0;">TICKET DE PRÉPARATION</h2>
            <small>ID: <?= $ma_commande['id'] ?></small>
        </div>

        <div class="info">
            <strong>CLIENT :</strong> <?= $ma_commande['prenom'] . " " . $ma_commande['nom'] ?><br>
            <strong>HEURE  :</strong> <?= $ma_commande['heure'] ?>
        </div>

        <div class="liste">
            <?php foreach ($ma_commande['panier'] as $item): 
                $q = $item['quantite'];
                $est_un_menu = ($item['type'] === 'menu' || strpos($item['id_produit'], 'menu_') === 0);
            ?>

                <div class="article">
                    <?php if ($est_un_menu): ?>
                        <div class="titre-article"><span class="quantite"><?= $q ?>x</span> MENU : <?= $item['nom_plat'] ?? $item['nom_menu'] ?></div>
                        
                        <div class="ingredients">
                            <?php 
                            $nom_menu_nettoye = str_replace(['menu_', '_'], ['', ' '], $item['id_produit']);
                            $compo_trouvee = null;
                            foreach ($menu_data['groupe_plat'] as $gp) {
                                if (strcasecmp($gp['nom'], $nom_menu_nettoye) === 0) {
                                    $compo_trouvee = $gp['composition'];
                                    break;
                                }
                            }

                            if ($compo_trouvee):
                                foreach ($compo_trouvee as $categorie => $ids):
                                    foreach ($ids as $id_plat):
                                        $plat_details = null;
                                        foreach (['entres', 'plats', 'boisson', 'dessert'] as $cat_search) {
                                            if (isset($menu_data[$cat_search])) {
                                                foreach ($menu_data[$cat_search] as $p) {
                                                    if ($p['id'] == $id_plat) { 
                                                        $plat_details = $p; break 2; 
                                                    }
                                                }
                                            }
                                        }
                                        if ($plat_details): ?>
                                            <span class="sous-produit">• <?= htmlspecialchars($plat_details['nom']) ?></span>
                                            <?php if(!empty($plat_details['ingredient'])): ?>
                                                <div class=ingredient>
                                                    (<?= implode(', ', $plat_details['ingredient']) ?>)
                                                </div>
                                            <?php endif; ?>
                                        <?php endif;
                                    endforeach;
                                endforeach;
                            endif; ?>
                        </div>

                    <?php else: ?>
                        <?php 
                        $plat_solo = null;
                        foreach (['entres', 'plats', 'boisson', 'dessert'] as $cat_search) {
                            if (isset($menu_data[$cat_search])) {
                                foreach ($menu_data[$cat_search] as $p) {
                                    if ($p['id'] == $item['id_produit']){ 
                                        $plat_solo = $p; break 2; 
                                    }
                                }
                            }
                        }
                        ?>
                        <div class="titre-article">
                            <span class="quantite"><?= $q ?>x</span> 
                            <?= $plat_solo['nom'] ?? $item['nom_plat'] ?>
                        </div>
                        <?php if ($plat_solo && !empty($plat_solo['ingredient'])): ?>
                            <div class="ingredients">
                                <?= implode(', ', $plat_solo['ingredient']) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            <?php endforeach; ?>
        </div>

        <div class="fin">
            *** TICKET DE PRÉPARATION ***
        </div>

    <?php else: ?>
        <p>Commande introuvable.</p>
    <?php endif; ?>
</div>

</body>
</html>