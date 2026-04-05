<?php
date_default_timezone_set('Europe/Paris');

$commandes_file = 'commandes.json';
$menu_file = 'menu.json';

if (file_exists($commandes_file)) {
    $commandes_brutes = json_decode(file_get_contents($commandes_file), true);
    if ($commandes_brutes === null) {
        $commandes_brutes = [];
    }
} else {
    $commandes_brutes = [];
}

if (file_exists($menu_file)) {
    $menu_data = json_decode(file_get_contents($menu_file), true);
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
            if ($c['statut'] == 'a_preparer') {
                $c['statut'] = 'en_cours_de_prep';
            } 
            else if ($c['statut'] == 'en_cours_de_prep') {
                $c['statut'] = 'en_livraison';
                if (empty($c['info_livraison'])) {
                    $c['info_livraison'] = 'Parti à ' . date('H:i');
                }
            }
            break; 
        }
    }
    file_put_contents($commandes_file, json_encode($commandes_brutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: commandes.php");
    exit();
}
$a_preparer = [];
$en_livraison = [];
foreach ($commandes_brutes as $cmd) {
    if (isset($cmd['statut'])) {
        if ($cmd['statut'] == 'a_preparer' || $cmd['statut'] == 'en_cours_de_prep') {
            $a_preparer[] = $cmd;
        } else if ($cmd['statut'] == 'en_livraison') {
            $en_livraison[] = $cmd;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface Restaurateur</title>
    <link rel="stylesheet" href="commandes.css">
    <link rel="stylesheet" href="couleur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <?php foreach ($a_preparer as $cmd): ?>
                        <a href="contenu.php?id=<?= $cmd['id'] ?>" class="pas-souligner">
                        <div class="ticket-commande">
                            <div class="haut-du-ticket">
                                <span class="nom-client"><?php echo htmlspecialchars($cmd['prenom'] . " " . $cmd['nom']); ?></span>
                                <span class="chronometre"><i class="far fa-clock"></i> <?php echo htmlspecialchars($cmd['heure']); ?></span>
                            </div>
                            
                            <div class="liste-articles">
                                <?php foreach ($cmd['panier'] as $item): ?>
                                    <div class="item-commande">
                                        <?php if ($item['type'] == 'plat'): ?>
                                            <?php $p = trouverProduit($item['id_produit'], $menu_data); ?>
                                            <p><strong><?php echo $item['quantite']; ?>x <?php if($p) { echo htmlspecialchars($p['nom']); } else { echo "Produit"; } ?></strong></p>
                                            <?php if ($p && isset($p['ingredient'])): ?>
                                                <small>Ingrédients : <?php echo htmlspecialchars(implode(', ', $p['ingredient'])); ?></small>
                                            <?php endif; ?>
                                        
                                        <?php elseif ($item['type'] == 'menu'): ?>
                                            <p><strong><?php echo $item['quantite']; ?>x Menu : <?php echo htmlspecialchars($item['nom_menu']); ?></strong></p>
                                            <?php 
                                            if (isset($menu_data['groupe_plat'])) {
                                                foreach ($menu_data['groupe_plat'] as $gp) {
                                                    if ($gp['nom'] == $item['nom_menu']) {
                                                        foreach ($gp['composition'] as $cat => $ids) {
                                                            foreach ($ids as $id_p) {
                                                                $p_menu = trouverProduit($id_p, $menu_data);
                                                                if ($p_menu) {
                                                                    echo "<span>- " . htmlspecialchars($p_menu['nom']) . "</span><br>";
                                                                    if (isset($p_menu['ingredient'])) {
                                                                        echo "<small>Ingrédients : " . htmlspecialchars(implode(', ', $p_menu['ingredient'])) . "</small><br>";
                                                                    }
                                                                }
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
                                <?php if ($cmd['statut'] == 'a_preparer'): ?>
                                    <button type="submit" name="valider_commande" class="bouton_vert">
                                        Prêt pour préparation
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="valider_commande" class="bouton_rouge">Prêt pour validation
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="colonne-commandes">
                <div class="entete-colonne">
                    <h2><i class="fas fa-truck"></i> En livraison</h2>
                    <span class="compteur-commandes"><?php echo count($en_livraison); ?></span>
                </div>
                <div class="liste-tickets">
                    <?php foreach ($en_livraison as $cmd): ?>
                        <a href="contenu.php?id=<?= $cmd['id'] ?>" class="pas-souligner">
                        <div class="ticket-commande statut-en-route">
                            <div class="haut-du-ticket">
                                <span class="nom-client"><?php echo htmlspecialchars($cmd['prenom'] . " " . $cmd['nom']); ?></span>
                                <span class="nom-livreur">Livreur : Yves Oikeudal</span>
                            </div>
                            <div class="message-status">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?php 
                                if (!empty($cmd['info_livraison'])) {
                                    echo htmlspecialchars($cmd['info_livraison']);
                                } else {
                                    echo "En cours...";
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
