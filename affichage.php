<?php
session_start();

$json_content = file_get_contents('menu.json');
$menu = json_decode($json_content, true);

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$plat_actuel = null;


foreach ($menu as $categorie => $liste_plats) {
    foreach ($liste_plats as $plat) {
        if ($plat['id'] === $id_demande) {
            $plat_actuel = $plat;
            break 2; 
        }
    }
}

if (!$plat_actuel) {
    header("Location: menu.php");
    exit();
}
$ingredients = isset($plat_actuel['ingredient']) ? $plat_actuel['ingredient'] : ['Ingrédients secrets non révélés'];
$allergenes = isset($plat_actuel['allergene']) ? $plat_actuel['allergene'] : ['Aucun allergène mutant déclaré'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($plat_actuel['nom']) ?> - Exotique Dream</title>
    <link rel="stylesheet" href="site.css">
    <link rel="stylesheet" href="affichage.css"> <link rel="stylesheet" href="couleur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
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
            <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "Admin"): ?>
                <a href="administrateur.php" style="color: var(--fraise); font-weight: bold;">
                    <i class="fas fa-lock"></i> Admin
                </a>
            <?php endif; ?>
        </nav>
        <div class="droite">
            <a href="connexion_au_compte.php" class="bouton-connexion">Connexion</a>
        </div>
    </header>

    <main class="page-details">
        <a href="menu.php" class="btn-retour"><i class="fas fa-arrow-left"></i> Retour au menu</a>
        
        <div class="conteneur-details">
            <div class="details-gauche">
                <h1 class="titre-geant"><?= htmlspecialchars($plat_actuel['nom']) ?></h1>
                <p class="desc-detail"><?= htmlspecialchars($plat_actuel['description']) ?></p>
                
                <div class="bloc-infos">
                    <h3><i class="fas fa-leaf"></i> Ingrédients</h3>
                    <ul class="liste-tags">
                        <?php foreach($ingredients as $ing): ?>
                            <li><?= htmlspecialchars($ing) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bloc-infos">
                    <h3><i class="fas fa-exclamation-triangle"></i> Allergènes</h3>
                    <ul class="liste-tags allergenes">
                        <?php foreach($allergenes as $all): ?>
                            <li><?= htmlspecialchars($all) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="zone-achat">
                    <span class="prix-geant"><?= number_format($plat_actuel['prix'], 2) ?>€</span>
                    
                    <form method="POST" action="menu.php">
                        <input type="hidden" name="item_id" value="<?= $plat_actuel['id'] ?>">
                        <input type="hidden" name="item_nom" value="<?= htmlspecialchars($plat_actuel['nom']) ?>">
                        <input type="hidden" name="item_prix" value="<?= $plat_actuel['prix'] ?>">
                        <button type="submit" name="ajouter_item" class="btn-valider-detail">AJOUTER AU PANIER</button>
                    </form>
                </div>
            </div>

            <div class="details-droite">
                <div class="cadre-image-geant">
                    <img src="<?= htmlspecialchars($plat_actuel['img']) ?>" alt="<?= htmlspecialchars($plat_actuel['nom']) ?>">
                </div>
            </div>
        </div>
    </main>
</body>
</html>