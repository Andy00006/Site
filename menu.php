<?php
session_start();

$json_content = file_get_contents('menu.json');
$menu = json_decode($json_content, true);

if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = [];
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_item"])) {
    $item_id = $_POST["item_id"];
    $item_nom = $_POST["item_nom"];
    $item_prix = (float)$_POST["item_prix"];
    $found = false;
    foreach ($_SESSION['panier'] as &$item) {
        if ($item["id"] == $item_id) {
            $item["quantite"]++;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION["panier"][] = [
            'id' => $item_id,
            'nom' => $item_nom,
            'prix' => $item_prix,
            'quantite' => 1
        ];
    }
    header("Location: menu.php");
    exit();
}
//la derniere fonction est temporaire le temps d'avoir des comptes fonctionnels
if (isset($_GET["vider_panier"])) {
    $_SESSION["panier"] = [];
    header("Location: menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Carte - Exotique Dream</title>
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="couleur.css">
</head>
<body>
    <header class="header">
        <div>
            <a href="accueil.php" class="logo">EXOTIQUE<span>DREAM</span></a>
        </div>
        <nav class="milieu">
            <a href="accueil.php">Accueil</a>
            <a href="com.php">Communication</a>
            <a href="menu.php" class="active">Menu</a>
            <a href="loc.php">Localisation</a>
        </nav>
        <div class="droite">
            <a href="connexion_au_compte.php" class="bouton-connexion">Connexion</a>
            <a href="creation_de_compte.php" class="bouton-inscription">Inscription</a>
        </div>
    </header>

    <div class="promo-bar">
        🔥 OFFRE MUTANTE : -20% SUR TOUS LES DESSERTS AVEC LE CODE "NEAN20"
    </div>

    <div class="principal">
        <nav class="categorie-gauche">
            <a href="#entrees">Entrées</a>
            <a href="#plats">Plats</a>
            <a href="#boissons">Boissons</a>
            <a href="#desserts">Desserts</a>
        </nav>

        <main class="menu">
            <section id='entrees'>
                <h2 class="titre">Entrées de l'Espace</h2>
                <div class="grille-plats">
                <?php foreach ($menu["entres"] as $plat): ?>
                    <div class="plat">
                        <input type="checkbox" id="plat_<?= $plat['id'] ?>" class="cadre">
                        <label for="plat_<?= $plat['id'] ?>" class="image-box">
                            <img src=<?= $plat["img"] ?>>
                        </label>
                        <div class="contenu">
                            <div class="titre-plat"><h3><?= $plat["nom"]?></h3><span class="prix"><?= $plat["prix"]?>€</span></div>
                                <p class="description"><?= $plat["description"]?></p>
                                <form method="POST" action="menu.php">
                                    <input type="hidden" name="item_id" value="<?= $plat['id'] ?>">
                                    <input type="hidden" name="item_nom" value="<?= htmlspecialchars($plat['nom']) ?>">
                                    <input type="hidden" name="item_prix" value="<?= $plat['prix'] ?>">
                                    <button type="submit" name="ajouter_item" class="ajouter">AJOUTER</button>
                                </form>
                            </div>
                        </div>
                <?php endforeach; ?>
        </section>
            <section id='boissons'>
                <h2 class="titre">Élixirs de Morphée</h2>
                <div class="grille-plats">
                    <?php foreach ($menu["boisson"] as $plat): ?>
                    <div class="plat">
                        <input type="checkbox" id="plat_<?= $plat['id'] ?>" class="cadre">
                        <label for="plat_<?= $plat['id'] ?>" class="image-box">
                            <img src=<?= $plat["img"] ?>>
                        </label>
                        <div class="contenu">
                            <div class="titre-plat"><h3><?= $plat["nom"]?></h3><span class="prix"><?= $plat["prix"]?>€</span></div>
                                <p class="description"><?= $plat["description"]?></p>
                                <form method="POST" action="menu.php">
                                    <input type="hidden" name="item_id" value="<?= $plat['id'] ?>">
                                    <input type="hidden" name="item_nom" value="<?= htmlspecialchars($plat['nom']) ?>">
                                    <input type="hidden" name="item_prix" value="<?= $plat['prix'] ?>">
                                    <button type="submit" name="ajouter_item" class="ajouter">AJOUTER</button>
                                </form>
                            </div>
                        </div>
                <?php endforeach; ?>
            </section>
            <section id="plats">
                <h2 class="titre">Plats Mutants</h2>
                <div class="grille-plats">
                    <?php foreach ($menu["plats"] as $plat): ?>
                    <div class="plat">
                        <input type="checkbox" id="plat_<?= $plat['id'] ?>" class="cadre">
                        <label for="plat_<?= $plat['id'] ?>" class="image-box">
                            <img src=<?= $plat["img"] ?>>
                        </label>
                        <div class="contenu">
                            <div class="titre-plat"><h3><?= $plat["nom"]?></h3><span class="prix"><?= $plat["prix"]?>€</span></div>
                                <p class="description"><?= $plat["description"]?></p>
                                <form method="POST" action="menu.php">
                                    <input type="hidden" name="item_id" value="<?= $plat['id'] ?>">
                                    <input type="hidden" name="item_nom" value="<?= htmlspecialchars($plat['nom']) ?>">
                                    <input type="hidden" name="item_prix" value="<?= $plat['prix'] ?>">
                                    <button type="submit" name="ajouter_item" class="ajouter">AJOUTER</button>
                                </form>
                            </div>
                        </div>
                <?php endforeach; ?>
            </section>
            <section id="desserts">
                <h2 class="titre">Desserts du Néant</h2>
                <div class="grille-plats">
                    <?php foreach ($menu["dessert"] as $plat): ?>
                    <div class="plat">
                        <input type="checkbox" id="plat_<?= $plat['id'] ?>" class="cadre">
                        <label for="plat_<?= $plat['id'] ?>" class="image-box">
                            <img src=<?= $plat["img"] ?>>
                        </label>
                        <div class="contenu">
                            <div class="titre-plat"><h3><?= $plat["nom"]?></h3><span class="prix"><?= $plat["prix"]?>€</span></div>
                                <p class="description"><?= $plat["description"]?></p>
                                <form method="POST" action="menu.php">
                                    <input type="hidden" name="item_id" value="<?= $plat['id'] ?>">
                                    <input type="hidden" name="item_nom" value="<?= htmlspecialchars($plat['nom']) ?>">
                                    <input type="hidden" name="item_prix" value="<?= $plat['prix'] ?>">
                                    <button type="submit" name="ajouter_item" class="ajouter">AJOUTER</button>
                                </form>
                            </div>
                        </div>
                <?php endforeach; ?>
            </section>
        </main>

        <aside class="panier-droit">
            <div class="panier-fixe">
                <h3>VOTRE PANIER</h3>
                <div class="liste-panier" style="flex-direction: column; align-items: stretch; justify-content: flex-start;">
                    <?php if (empty($_SESSION['panier'])): ?>
                        <p class="vide" style="text-align: center; margin-top: 30px;">Le rêve est vide...</p>
                        <?php $total = 0; ?>
                    <?php else: ?>
                        <ul style="list-style: none; padding: 0; width: 100%;">
                            <?php 
                                $total = 0;
                                foreach ($_SESSION['panier'] as $article): 
                                $sous_total = $article['prix'] * $article['quantite'];
                                $total += $sous_total;
                            ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: var(--noir);">
                                <span><strong><?= $article['quantite'] ?>x</strong> <?= htmlspecialchars($article['nom']) ?></span>
                                 <span style="font-weight: bold; color: var(--fraise); margin-left: 10px;"><?= number_format($sous_total, 2) ?>€</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="total">
                    <span>Total</span>
                    <span><?= number_format($total, 2) ?>€</span> 
                </div>
                <?php if (!empty($_SESSION['panier'])): ?>
                    <a href="menu.php?vider_panier=1" style="display:block; text-align:center; color: var(--noir); font-size:12px; margin-bottom: 15px; text-decoration: underline;">Vider le panier</a>
                <?php endif; ?>
                <button class="btn-valider">VALIDER LE RÊVE</button>
            </div>
        </aside>
    </div>

    <footer class="footer">
        <div class="footer-section">
            <h3>Horaires</h3>
            <p>Lun - Ven : 11h00 - 22h30</p>
            <p>Samedi : 12h00 - 23h30</p>
            <p>Dimanche : 12h00 - 21h00</p>
        </div>
        <div class="footer-section">
            <h3>Navigation</h3>
            <a href="accueil.php">Accueil</a>
            <a href="com.php">Communauté</a>
            <a href="menu.php">Menu</a>
            <a href="loc.php">Localisation</a>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p><i class="fas fa-map-marker-alt"></i> - 12 Avenue des Saveurs, Cergy</p>
            <p><i class="fas fa-phone"></i> - 01 23 45 67 89</p>
            <p><i class="fas fa-envelope"></i> - contact@exotiquedream.fr</p>
        </div>
        <div class="footer-bas">
            <p>© 2026 EXOTIQUE DREAM - Tous droits réservés</p>
            <div class="reseaux">
                <a href="https://www.instagram.com/exoticdream__/"><i class="fab fa-instagram"></i></a>
                <a href="https://x.com/ExotiqueDream"><i class="fab fa-twitter"></i></a>
                <a href="https://www.tiktok.com/fr/"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>

