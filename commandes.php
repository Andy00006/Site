<?php
date_default_timezone_set('Europe/Paris');

$commandes_file = 'commandes.json';
$menu_file = 'menu.json';

if (file_exists($commandes_file)) {
    $contenu_commandes = file_get_contents($commandes_file);
    $commandes_brutes = json_decode($contenu_commandes, true);
} else {
    $commandes_brutes = [];
}

if (file_exists($menu_file)) {
    $contenu_menu = file_get_contents($menu_file);
    $menu_data = json_decode($contenu_menu, true);
} else {
    $menu_data = [];
}

function trouverProduit($id, $menu) {
    $categories = ['entres', 'plats', 'boisson', 'dessert'];
    foreach ($categories as $cat) {
        if (isset($menu[$cat])) {
            foreach ($menu[$cat] as $p) {
                if ($p['id'] == $id) {
                    return $p;
                }
            }
        }
    }
    return null;
}
    if (isset($_POST['valider_commande'])) {
        $id_mod = $_POST['commande_id'];
        
        foreach ($commandes_brutes as &$c) {
            if ($c['id'] == $id_mod) {
                $c['statut'] = 'en_livraison';
                
                if (empty($c['info_livraison'])) {
                    $c['info_livraison'] = 'Parti à ' . date('H:i');
                }
                break; 
            }
        }
        
        $json_final = json_encode($commandes_brutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($commandes_file, $json_final);
        
        header("Location: commandes.php");
        exit();
    }
$a_preparer = [];
$en_livraison = [];

foreach ($commandes_brutes as $cmd) {
    if (isset($cmd['statut'])) {
        if ($cmd['statut'] === 'a_preparer') {
            $a_preparer[] = $cmd;
        } else if ($cmd['statut'] === 'en_livraison') {
            $en_livraison[] = $cmd;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Yumland - Gestion Cuisine</title>
    <link rel="stylesheet" href="commandes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>
    <div class="restaurateur">
        <header class="entete-restaurateur">
            <div class="logo-restaurateur"><span>Exotique</span> Dream</div>
            <h1>Interface Restaurateur</h1>
        </header>

        <main class="zone-principale-restaurateur">
            <section class="colonne-commandes">
                <div class="entete-colonne">
                    <h2><i class="fas fa-utensils"></i> À préparer</h2>
                    <span class="compteur-commandes"><?php echo count($a_preparer); ?></span>
                </div>

                <div class="liste-tickets">
                    <?php if (empty($a_preparer)): ?>
                        <p style="text-align:center; color:#888;">Aucune commande.</p>
                    <?php else: ?>
                        <?php foreach ($a_preparer as $cmd): ?>
                            <div class="ticket-commande">
                                <div class="haut-du-ticket">
                                    <span class="nom-client"><?php echo $cmd['prenom'] . " " . $cmd['nom']; ?></span>
                                    <span class="chronometre"><i class="far fa-clock"></i> <?php echo $cmd['heure']; ?></span>
                                </div>
                                <div class="liste-articles">
                                    <?php foreach ($cmd['panier'] as $item): ?>
                                        <div class="item-commande">
                                            <?php if ($item['type'] === 'plat'): ?>
                                                <?php $p = trouverProduit($item['id_produit'], $menu_data); ?>
                                                <p><strong><?php echo $item['quantite']; ?>x <?php echo $p['nom'] ?? 'Produit'; ?></strong></p>
                                                <small>Ingrédients : <?php echo implode(', ', $p['ingredient'] ?? []); ?></small>
                                            
                                            <?php elseif ($item['type'] === 'menu'): ?>
                                                <p><strong><?php echo $item['quantite']; ?>x Menu : <?php echo $item['nom_menu']; ?></strong></p>
                                                <?php 
                                                foreach ($menu_data['groupe_plat'] as $gp) {
                                                    if ($gp['nom'] === $item['nom_menu']) {
                                                        foreach ($gp['composition'] as $cat => $ids) {
                                                            foreach ($ids as $id_p) {
                                                                $p_menu = trouverProduit($id_p, $menu_data);
                                                                if ($p_menu) {
                                                                    echo "<span>- " . $p_menu['nom'] . "</span><br>";
                                                                    echo "<small>Ingrédients : " . implode(', ', $p_menu['ingredient'] ?? []) . "</small><br>";
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="commande_id" value="<?php echo $cmd['id']; ?>">
                                    <button type="submit" name="valider_commande" class="bouton-pret">Prêt pour livraison</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="colonne-commandes">
                <div class="entete-colonne">
                    <h2><i class="fas fa-truck"></i> En livraison</h2>
                    <span class="compteur-commandes"><?php echo count($en_livraison); ?></span>
                </div>
                <div class="liste-tickets">
                    <?php foreach ($en_livraison as $cmd): ?>
                        <div class="ticket-commande statut-en-route">
                            <div class="haut-du-ticket">
                                <span class="nom-client"><?php echo $cmd['prenom'] . " " . $cmd['nom']; ?></span>
                                <span class="nom-livreur">Livreur : Yves Oikeudal</span>
                            </div>
                            <div class="message-status">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?php 
                                if (!empty($cmd['info_livraison'])) {
                                    echo $cmd['info_livraison'];
                                } else {
                                    echo "En cours...";
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
