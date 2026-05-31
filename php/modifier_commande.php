<?php
session_start();
require_once 'deco.php';
require('getapikey.php');

$est_connecte = isset($_SESSION["prenom"]);
if (!$est_connecte) {
    header("Location: connexion_au_compte.php");
    exit();
}

$initiales = strtoupper(substr($_SESSION["prenom"], 0, 1) . substr($_SESSION["nom"], 0, 1));

$json_menu = file_get_contents('menu.json');
$menu = json_decode($json_menu, true);

function obtenirPrixDepuisMenu($item_id, $menu)
{
    if (!is_array($menu)) return 0.0;

    if (strpos($item_id, 'menu_') === 0) {
        $nom_groupe_recherche = str_replace('menu_', '', $item_id);
        $nom_groupe_recherche = str_replace("_", " ", $nom_groupe_recherche);

        if (isset($menu['groupe_plat'])) {
            foreach ($menu['groupe_plat'] as $groupe) {
                if (strcasecmp(trim($groupe['nom']), trim($nom_groupe_recherche)) === 0) {
                    $total_groupe = 0.0;
                    if (isset($groupe['composition'])) {
                        foreach ($groupe['composition'] as $categorie => $ids_plats) {
                            foreach ($ids_plats as $id_p) {
                                $total_groupe += obtenirPrixDepuisMenu($id_p, $menu);
                            }
                        }
                    }
                    return $total_groupe;
                }
            }
        }
    }

    foreach ($menu as $categorie => $liste_plats) {
        if ($categorie !== 'groupe_plat' && is_array($liste_plats)) {
            foreach ($liste_plats as $plat) {
                if (isset($plat['id']) && (string)$plat['id'] === (string)$item_id) {
                    return (float)str_replace(',', '.', $plat['prix']);
                }
            }
        }
    }
    return 0.0;
}

$fichier_commandes = 'commandes.json';
$commandes_data = array();
if (file_exists($fichier_commandes)) {
    $commandes_data = json_decode(file_get_contents($fichier_commandes), true);
}
$id_commande = null;
$index_commande = null;
if (isset($_POST['id_commande'])) {
    if (!isset($_SESSION['id_commande_en_cours']) || $_SESSION['id_commande_en_cours'] != $_POST['id_commande']) {
        unset($_SESSION["panier_modif"]);
    }
    $_SESSION['id_commande_en_cours'] = $_POST['id_commande'];
}
if (isset($_SESSION['id_commande_en_cours'])) {
    $id_commande_recherche = $_SESSION['id_commande_en_cours'];
    foreach ($commandes_data as $index => $cmd) {
        if (isset($cmd['id']) && $cmd['id'] == $id_commande_recherche) {
            $id_commande = $cmd['id'];
            $index_commande = $index;
            break;
        }
    }
}
if ($id_commande === null) {
    foreach ($commandes_data as $index => $cmd) {
        if (isset($cmd['prenom']) && $cmd['prenom'] === $_SESSION['prenom']) {
            if (isset($cmd['statut']) && $cmd['statut'] === 'a_preparer') {
                $id_commande = $cmd['id'];
                $index_commande = $index;
                if (!isset($_SESSION['id_commande_en_cours']) || $_SESSION['id_commande_en_cours'] != $id_commande) {
                    unset($_SESSION["panier_modif"]);
                }
                $_SESSION['id_commande_en_cours'] = $id_commande;
                break;
            }
        }
    }
}
$id_commande_cible = $id_commande;
if ($id_commande === null || $index_commande === null) {
    die("Erreur : Aucune commande modifiable n'a été trouvée pour votre compte.");
}
$commande_actuelle = $commandes_data[$index_commande];
if (isset($commande_actuelle['statut'])) {
    $commande_statut = $commande_actuelle['statut'];
} else {
    $commande_statut = 'a_preparer';
}
if ($commande_statut !== 'a_preparer') {
    header("Location: suivie.php?erreur=deja_en_preparation");
    exit();
}
$ancien_total = 0;
if (isset($commande_actuelle['panier'])) {
    foreach ($commande_actuelle['panier'] as $art) {
        $ancien_total += obtenirPrixDepuisMenu($art['id_produit'], $menu) * $art['quantite'];
    }
}
if (!isset($_SESSION["panier_modif"])) {
    $_SESSION["panier_modif"] = array();
    if (isset($commande_actuelle['panier'])) {
        foreach ($commande_actuelle['panier'] as $art) {
            $nom_article = isset($art['nom_plat']) ? $art['nom_plat'] : 'Article';
            $_SESSION["panier_modif"][] = array(
                'id'       => $art['id_produit'],
                'nom'      => $nom_article,
                'prix'     => obtenirPrixDepuisMenu($art['id_produit'], $menu),
                'quantite' => $art['quantite'],
                'type'     => isset($art['type']) ? $art['type'] : 'plat'
            );
        }
    }
}
if (isset($_POST['action']) && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $action = $_POST['action'];

    if ($action === 'ajouter_catalogue') {
        $trouve_dans_panier = false;
        foreach ($_SESSION["panier_modif"] as &$item) {
            if ($item['id'] == $item_id) {
                $item['quantite']++;
                $trouve_dans_panier = true;
                break;
            }
        }
        if (!$trouve_dans_panier) {
            $produit_ajoute = null;
            if (strpos($item_id, 'menu_') === 0) {
                $nom_groupe = str_replace('menu_', '', $item_id);
                $nom_groupe = str_replace("_", " ", $nom_groupe);
                if (isset($menu['groupe_plat'])) {
                    foreach ($menu['groupe_plat'] as $groupe) {
                        if (strcasecmp(trim($groupe['nom']), trim($nom_groupe)) === 0) {
                            $produit_ajoute = array(
                                'id'   => $item_id,
                                'nom'  => $groupe['nom'],
                                'prix' => obtenirPrixDepuisMenu($item_id, $menu),
                                'type' => 'menu'
                            );
                            break;
                        }
                    }
                }
            } else {
                foreach ($menu as $categorie => $liste_plats) {
                    if ($categorie !== 'groupe_plat' && is_array($liste_plats)) {
                        foreach ($liste_plats as $plat) {
                            if ((string)$plat['id'] === (string)$item_id) {
                                $produit_ajoute = array(
                                    'id'   => $plat['id'],
                                    'nom'  => $plat['nom'],
                                    'prix' => (float)str_replace(',', '.', $plat['prix']),
                                    'type' => 'plat'
                                );
                                break 2;
                            }
                        }
                    }
                }
            }

            if ($produit_ajoute !== null) {
                $_SESSION["panier_modif"][] = array(
                    'id'       => $produit_ajoute['id'],
                    'nom'      => $produit_ajoute['nom'],
                    'prix'     => $produit_ajoute['prix'],
                    'quantite' => 1,
                    'type'     => $produit_ajoute['type']
                );
            }
        }
    } else {
        foreach ($_SESSION["panier_modif"] as $index => &$item) {
            if ($item['id'] == $item_id) {
                if ($action === 'plus') {
                    $item['quantite']++;
                }
                if ($action === 'moins') {
                    $item['quantite']--;
                }
                if ($action === 'supprimer' || $item['quantite'] <= 0) {
                    unset($_SESSION["panier_modif"][$index]);
                }
                break;
            }
        }
        $_SESSION["panier_modif"] = array_values($_SESSION["panier_modif"]);
    }
    header("Location: modifier_commande.php");
    exit();
}
$nouveau_total = 0;
foreach ($_SESSION["panier_modif"] as $item) {
    $nouveau_total += $item['prix'] * $item['quantite'];
}
if (isset($_POST['valider_modification'])) {
    $nouvelle_liste_articles = array();
    foreach ($_SESSION["panier_modif"] as $item) {
        $nouvelle_liste_articles[] = array(
            'type'       => isset($item['type']) ? $item['type'] : 'plat',
            'id_produit' => $item['id'],
            'nom_plat'   => $item['nom'],
            'quantite'   => $item['quantite']
        );
    }
    $commandes_data[$index_commande]['panier'] = $nouvelle_liste_articles;
    file_put_contents($fichier_commandes, json_encode($commandes_data, JSON_PRETTY_PRINT));
    unset($_SESSION["panier_modif"]);
    unset($_SESSION['id_commande_en_cours']);
    $total_ancien_formate = number_format($ancien_total, 2, '.', '');
    $total_nouveau_formate = number_format($nouveau_total, 2, '.', '');
    if ($total_nouveau_formate === $total_ancien_formate) {
        header("Location: suivie.php?statut=modifie");
        exit();
    } elseif ($total_nouveau_formate < $total_ancien_formate) {
        $remboursement_fidelite = $ancien_total - $nouveau_total;
        header("Location: fideliter.php?credit=" . $remboursement_fidelite);
        exit();
    } else {
        $difference = $nouveau_total - $ancien_total;
        $_SESSION['montant_complement_a_payer'] = $difference;
        header("Location: validation.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="couleur.css">
    <link rel="stylesheet" href="site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="modifier_commande.css">
    <title>Modifier ma commande - Exotique Dream</title>
</head>
<body>
    <header class="header">
        <div>
            <a href="accueil.php" class="logo">EXOTIQUE<span>DREAM</span></a>
        </div>
        <nav class="milieu">
            <a href="accueil.php">Accueil</a>
            <a href="menu.php">Menu</a>
            <a href="suivie.php">Suivi Commande</a>
        </nav>
        <div class="droite">
            <div class="avatar-cercle"><?php echo $initiales; ?></div>
        </div>
    </header>
    <main class="page-modification">
        <div class="conteneur-modification">
            <h2>Modification de la commande N° <?php echo htmlspecialchars($id_commande_cible); ?></h2>
            <p>Ajustez vos quantités avant la prise en charge par les cuisiniers.</p>
            <h3>Votre panier actuel</h3>
            <div class="panier-container">
                <?php if (empty($_SESSION['panier_modif'])): ?>
                    <p>Votre panier est vide.</p>
                <?php else: ?>
                    <?php foreach ($_SESSION['panier_modif'] as $item): ?>
                        <div class="ligne-panier">
                            <span>
                                <strong><?php echo htmlspecialchars($item['nom']); ?></strong>
                                (<?php echo number_format($item['prix'], 2); ?>€)
                            </span>
                            <form method="POST" action="modifier_commande.php">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button class="btn-quantite" type="submit" name="action" value="moins">-</button>
                                <span>
                                    <strong><?php echo $item['quantite']; ?></strong>
                                </span>
                                <button class="btn-quantite" type="submit" name="action" value="plus">+</button>
                                <button class="btn-supprimer" type="submit" name="action" value="supprimer">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="resume-total">
                <p>
                    Ancien Total payé :
                    <strong><?php echo number_format($ancien_total, 2); ?> €</strong>
                </p>
                <p>
                    Nouveau Total :
                    <strong><?php echo number_format($nouveau_total, 2); ?> €</strong>
                </p>
                <hr>
                <?php $diff = $nouveau_total - $ancien_total; ?>
                <?php if ($diff > 0): ?>
                    <p>
                        Reste à payer :
                        +<?php echo number_format($diff, 2); ?> €
                        (Redirection vers la page de payement)
                    </p>
                <?php elseif ($diff < 0): ?>
                    <p>
                        Crédit transféré vers fidélité :
                        <?php echo number_format(abs($diff), 2); ?> €
                        (Redirection vers la page de fidéliter)
                    </p>
                <?php else: ?>
                    <p>
                        Le montant reste identique.
                        (Redirection vers la page de suivie des commandes)
                    </p>
                <?php endif; ?>
            </div>
            <form method="POST" action="modifier_commande.php">
                <button class="btn-validation" type="submit" name="valider_modification">
                    CONFIRMER LES MODIFICATIONS
                </button>
            </form>
            <hr>
            <h3>Ajouter d'autres produits au panier</h3>
            <?php
            if (is_array($menu)):
                foreach ($menu as $nom_cat => $liste_plats):
                    if (is_array($liste_plats) && !empty($liste_plats)):
            ?>
                        <h4><?php echo htmlspecialchars(ucfirst($nom_cat)); ?></h4>
                        <div class="grille-produits">
                            <?php foreach ($liste_plats as $plat): ?>
                                <div class="carte-produit">
                                    <?php if ($nom_cat !== 'groupe_plat'): ?>
                                        <img
                                            class="image-produit"
                                            src="<?php echo htmlspecialchars($plat['img']); ?>"
                                            alt="<?php echo htmlspecialchars($plat['nom']); ?>">
                                        <h5><?php echo htmlspecialchars($plat['nom']); ?></h5>
                                        <p><?php echo htmlspecialchars($plat['description']); ?></p>
                                        <p>
                                            Prix :
                                            <?php echo number_format((float)str_replace(',', '.', $plat['prix']), 2); ?> €
                                        </p>
                                        <form method="POST" action="modifier_commande.php">
                                            <input
                                                type="hidden"
                                                name="item_id"
                                                value="<?php echo $plat['id']; ?>">

                                            <button
                                                class="btn-ajout"
                                                type="submit"
                                                name="action"
                                                value="ajouter_catalogue">
                                                Ajouter à la commande
                                            </button>
                                        </form>
                                    <?php else:
                                        $id_genere_menu = "menu_" . str_replace(" ", "_", $plat['nom']);
                                    ?>
                                        <h5>
                                            Formule :
                                            <?php echo htmlspecialchars($plat['nom']); ?>
                                        </h5>
                                        <p>
                                            Prix combiné :
                                            <?php echo number_format(obtenirPrixDepuisMenu($id_genere_menu, $menu), 2); ?> €
                                        </p>
                                        <form method="POST" action="modifier_commande.php">
                                            <input
                                                type="hidden"
                                                name="item_id"
                                                value="<?php echo $id_genere_menu; ?>">
                                            <button class="btn-ajout" type="submit" name="action" value="ajouter_catalogue">
                                                Ajouter cette formule
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
            <?php
                    endif;
                endforeach;
            endif;
            ?>
        </div>
    </main>
</body>
</html>