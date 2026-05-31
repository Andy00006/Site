<?php
session_start();
require_once 'deco.php';
$est_connecte = isset($_SESSION["prenom"]);

if ($est_connecte) {
    $initiale_prenom = strtoupper(substr($_SESSION["prenom"], 0, 1));
    $initiale_nom    = strtoupper(substr($_SESSION["nom"], 0, 1));
    $initiales       = $initiale_prenom . $initiale_nom;
}

$json_content = file_get_contents('../json/menu.json');
$menu = json_decode($json_content, true);

if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = array();
}

if (isset($_POST["ajouter_item"])) {
    $item_id   = $_POST["item_id"];
    $item_nom  = $_POST["item_nom"];
    $item_prix = (float)$_POST["item_prix"];
    $found     = false;
    foreach ($_SESSION['panier'] as &$item) {
        if ($item["id"] == $item_id) {
            $item["quantite"]++;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION["panier"][] = array(
            'id'       => $item_id,
            'nom'      => $item_nom,
            'prix'     => $item_prix,
            'quantite' => 1
        );
    }
    header("Location: menu.php");
    exit();
}

if (isset($_GET["vider_panier"])) {
    $_SESSION["panier"] = array();
    header("Location: menu.php");
    exit();
}

$total_panier = 0;
foreach ($_SESSION['panier'] as $article) {
    $total_panier += $article['prix'] * $article['quantite'];
}

$map_categories = array(
    'entres'  => 'entrees',
    'plats'   => 'plats',
    'boisson' => 'boissons',
    'dessert' => 'desserts',
);
$tous_les_plats = array();
foreach ($map_categories as $cle_json => $cle_js) {
    foreach ($menu[$cle_json] as $plat) {
        $plat['_categorie'] = $cle_js;
        $tous_les_plats[]   = $plat;
    }
}
$tous_les_plats_json = json_encode($tous_les_plats);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Carte - Exotique Dream</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/couleur.css">
    <script src="../js/darkmode.js"></script>
    <style>
        
    </style>
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
            <button class="dark-toggle" id="dark-toggle" aria-label="Mode sombre">
                <span class="toggle-icon icon-moon">🌙</span>
                <span class="toggle-icon icon-sun">☀️</span>
            </button>
            <?php if ($est_connecte): ?>
                <a href="profil.php" class="avatar-lien">
                    <div class="avatar-cercle">
                        <?php echo $initiales; ?>
                    </div>
                </a>
                <a href="deconnexion.php" class="bouton-inscription">Déconnexion</a>
            <?php else: ?>
                <a href="connexion_au_compte.php" class="bouton-connexion">Connexion</a>
                <a href="creation_de_compte.php" class="bouton-inscription">Inscription</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="promo-bar">
        🔥 OFFRE MUTANTE : -10€ SUR TOUS LES PLATS ET MENUES AVEC LE CODE "REDUC10"
    </div>

    <div class="principal">
        <nav class="categorie-gauche">
            <a href="#menus">Menus</a>
            <a href="#catalogue">Carte</a>
        </nav>

        <main class="menu">

            <section id="menus">
                <h2 class="titre">Menus transdimensionnels</h2>
                <div class="grille-menus">
                    <?php foreach ($menu["groupe_plat"] as $groupe): ?>
                        <div class="plat">
                            <?php
                            $total_menu = 0;
                            $ids = array_merge(
                                $groupe["composition"]["entres"],
                                $groupe["composition"]["boisson"],
                                $groupe["composition"]["plats"],
                                $groupe["composition"]["dessert"]
                            );
                            $tous_plats_menu = array_merge(
                                $menu["entres"],
                                $menu["boisson"],
                                $menu["plats"],
                                $menu["dessert"]
                            );
                            foreach ($ids as $id) {
                                foreach ($tous_plats_menu as $p) {
                                    if ($p["id"] == $id) {
                                        $total_menu += $p["prix"];
                                    }
                                }
                            }
                            ?>
                            <span class="prix-total"><?php echo number_format($total_menu, 2); ?>€</span>
                            <div class="contenu">
                                <h3><?php echo $groupe["nom"]; ?></h3>
                                <div class="images-menu">
                                    <?php foreach ($ids as $id):
                                        foreach ($tous_plats_menu as $plat):
                                            if ($plat["id"] == $id): ?>
                                            <a href="affichage.php?id=<?php echo $plat['id']; ?>" class="lien-mini-plat">
                                                <div class="mini-plat">
                                                    <h4>
                                                        <?php
                                                        if (in_array($id, $groupe["composition"]["entres"])) {
                                                            echo "Entrée";
                                                        } elseif (in_array($id, $groupe["composition"]["boisson"])) {
                                                            echo "Boisson";
                                                        } elseif (in_array($id, $groupe["composition"]["plats"])) {
                                                            echo "Plat";
                                                        } else {
                                                            echo "Dessert";
                                                        }
                                                        ?>
                                                    </h4>
                                                    <img src="<?php echo $plat["img"]; ?>" alt="<?php echo $plat["nom"]; ?>" width="100">
                                                    <p><?php echo $plat["nom"]; ?></p>
                                                </div>
                                            </a>
                                            <?php endif;
                                        endforeach;
                                    endforeach; ?>
                                </div>
                                <form method="POST" action="menu.php">
                                    <input type="hidden" name="item_id"    value="menu_<?php echo str_replace(' ', '_', $groupe['nom']); ?>">
                                    <input type="hidden" name="item_nom"   value="<?php echo htmlspecialchars($groupe['nom']); ?>">
                                    <input type="hidden" name="item_prix"  value="<?php echo $total_menu; ?>">
                                    <button type="submit" name="ajouter_item" class="ajouter">AJOUTER LE MENU</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="catalogue">
                <h2 class="titre">La Carte Complète</h2>

                <div class="barre-filtres">

                    <div class="filtres-ligne">
                        <span class="filtres-label">Catégorie</span>
                        <button class="btn-filtre actif" data-type="categorie" data-valeur="toutes">Toutes</button>
                        <button class="btn-filtre" data-type="categorie" data-valeur="entrees">Entrées</button>
                        <button class="btn-filtre" data-type="categorie" data-valeur="plats">Plats</button>
                        <button class="btn-filtre" data-type="categorie" data-valeur="boissons">Boissons</button>
                        <button class="btn-filtre" data-type="categorie" data-valeur="desserts">Desserts</button>
                    </div>

                    <div class="filtres-ligne">
                        <span class="filtres-label">Sans…</span>
                        <button class="btn-filtre" data-type="allergo" data-valeur="lactose">Sans lactose</button>
                        <button class="btn-filtre" data-type="allergo" data-valeur="gluten">Sans gluten</button>
                        <button class="btn-filtre" data-type="allergo" data-valeur="viande">Sans viande</button>
                        <button class="btn-filtre" data-type="allergo" data-valeur="fruit de mer">Sans fruits de mer</button>
                        <button class="btn-filtre" data-type="allergo" data-valeur="fruit à coque">Sans fruits à coque</button>
                    </div>

                    <div class="filtres-ligne">
                        <span class="filtres-label">Trier par</span>
                        <select class="select-tri" id="select-tri">
                            <option value="defaut">Par défaut</option>
                            <option value="prix_asc">Prix croissant</option>
                            <option value="prix_desc">Prix décroissant</option>
                            <option value="nom_az">Nom A → Z</option>
                            <option value="nom_za">Nom Z → A</option>
                        </select>
                    </div>

                </div>

                <div id="zone-resultats"></div>
            </section>

        </main>
        <aside class="panier-droit">
            <div class="panier-fixe">
                <h3>VOTRE PANIER</h3>
                <div class="liste-panier">
                    <?php if (empty($_SESSION['panier'])): ?>
                        <p class="vide">Le rêve est vide...</p>
                    <?php else: ?>
                        <ul style="list-style: none; padding: 0; width:100%;">
                            <?php foreach ($_SESSION['panier'] as $article):
                                $sous_total = $article['prix'] * $article['quantite'];
                            ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span><?php echo $article['quantite']; ?>x <?php echo htmlspecialchars($article['nom']); ?></span>
                                <span><?php echo number_format($sous_total, 2); ?>€</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <?php 
                $reduction = 0;
                if (isset($_SESSION["valeur_reduction"])) {
                    $reduction = (float)$_SESSION["valeur_reduction"];
                }
                
                if ($reduction > 0 && !empty($_SESSION['panier'])) {
                    echo '<div style="display: flex; justify-content: space-between; color: #2ed573; font-weight: 600; font-size: 14px; margin-bottom: 5px;">';
                    echo '<span>Code fidéliter (' . htmlspecialchars($_SESSION["code_promo_actif"]) . ')</span>';
                    echo '<span>-' . number_format($reduction, 2) . '€</span>';
                    echo '</div>';
                    
                    $total_panier = $total_panier - $reduction;
                    if ($total_panier < 0) {
                        $total_panier = 0;
                    }
                }
                ?>
                <div class="total">
                    <span>Total</span>
                    <span><?php echo number_format($total_panier, 2); ?>€</span>
                </div>
                <?php if (!empty($_SESSION['panier'])): ?>
                    <a href="menu.php?vider_panier=1" style="display:block; text-align:center; color: var(--noir); font-size:12px; margin-bottom: 15px;">Vider le panier</a>
                    <a href="<?php if ($est_connecte) { echo 'validation.php'; } else { echo 'connexion_au_compte.php'; } ?>">
                        <button class="btn-valider">VALIDER LE RÊVE</button>
                    </a>
                <?php endif; ?>
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
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </footer>

<script>
var tousLesPlats = <?php echo $tous_les_plats_json; ?>;

var etatFiltres = {
    categorie : 'toutes',
    allergenes: [],
    tri       : 'defaut'
};

var titres = {
    entrees : "Entrées de l'Espace",
    plats   : 'Plats Mutants',
    boissons: 'Élixirs de Morphée',
    desserts: 'Desserts du Néant'
};

function carteHTML(plat) {
    return `<div class="plat">
        <a href="affichage.php?id=${plat.id}" class="image-box" style="display:block;">
            <img src="${plat.img}" alt="${plat.nom}">
        </a>
        <div class="contenu">
            <div class="titre-plat">
                <h3>${plat.nom}</h3>
                <span class="prix">${parseFloat(plat.prix).toFixed(2)}€</span>
            </div>
            <p class="description">${plat.description}</p>
            <form method="POST" action="menu.php">
                <input type="hidden" name="item_id"   value="${plat.id}">
                <input type="hidden" name="item_nom"  value="${plat.nom.replace(/"/g, '&quot;')}">
                <input type="hidden" name="item_prix" value="${plat.prix}">
                <button type="submit" name="ajouter_item" class="ajouter">AJOUTER</button>
            </form>
        </div>
    </div>`;
}

function filtrerEtTrier() {
    var resultat = tousLesPlats.slice();

    if (etatFiltres.categorie !== 'toutes') {
        resultat = resultat.filter(p => p._categorie === etatFiltres.categorie);
    }

    if (etatFiltres.allergenes.length > 0) {
        resultat = resultat.filter(p =>
            !etatFiltres.allergenes.some(a => p.allergene && p.allergene.includes(a))
        );
    }

    if (etatFiltres.tri === 'prix_asc')  resultat.sort((a, b) => a.prix - b.prix);
    if (etatFiltres.tri === 'prix_desc') resultat.sort((a, b) => b.prix - a.prix);
    if (etatFiltres.tri === 'nom_az')    resultat.sort((a, b) => a.nom.localeCompare(b.nom));
    if (etatFiltres.tri === 'nom_za')    resultat.sort((a, b) => b.nom.localeCompare(a.nom));

    afficher(resultat);
}

function afficher(plats) {
    var zone = document.getElementById('zone-resultats');

    if (plats.length === 0) {
        zone.innerHTML = '<p class="aucun-resultat">Aucun plat ne correspond à ces filtres.</p>';
        return;
    }

    var groupes = { entrees: [], plats: [], boissons: [], desserts: [] };
    plats.forEach(p => groupes[p._categorie].push(p));

    var html = '';
    for (var cat in groupes) {
        if (groupes[cat].length > 0) {
            html += `<h2 class="titre-section-filtre">${titres[cat]}</h2>`;
            html += `<div class="grille-plats">${groupes[cat].map(carteHTML).join('')}</div>`;
        }
    }

    zone.innerHTML = html;
}

document.querySelectorAll('[data-type="categorie"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('[data-type="categorie"]').forEach(b => b.classList.remove('actif'));
        btn.classList.add('actif');
        etatFiltres.categorie = btn.getAttribute('data-valeur');
        filtrerEtTrier();
    });
});

document.querySelectorAll('[data-type="allergo"]').forEach(btn => {
    btn.addEventListener('click', () => {
        var valeur = btn.getAttribute('data-valeur');
        var idx    = etatFiltres.allergenes.indexOf(valeur);
        if (idx === -1) {
            etatFiltres.allergenes.push(valeur);
            btn.classList.add('actif-allergo');
        } else {
            etatFiltres.allergenes.splice(idx, 1);
            btn.classList.remove('actif-allergo');
        }
        filtrerEtTrier();
    });
});

document.getElementById('select-tri').addEventListener('change', function() {
    etatFiltres.tri = this.value;
    filtrerEtTrier();
});

filtrerEtTrier();
</script>
</body>
</html>
