<?php
session_start();
require_once 'deco.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    header("Location: accueil.php");
    exit();
}

$message_admin_promo = "";
$classe_admin_promo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btn_ajouter_promo"])) {
    $nouveau_code = strtoupper(trim($_POST["nouveau_code"]));
    $nouvelle_valeur = (float)$_POST["nouvelle_valeur"];
    $fichier_reduc = "reduc.json";

    if ($nouveau_code !== "" && $nouvelle_valeur > 0) {
        if (file_exists($fichier_reduc)) {
            $les_reductions = json_decode(file_get_contents($fichier_reduc), true);
        } else {
            $les_reductions = array();
        }

        $code_existe = false;
        foreach ($les_reductions as $reduc) {
            if (isset($reduc["code"]) && strtoupper($reduc["code"]) === $nouveau_code) {
                $code_existe = true;
                break;
            }
        }

        if (!$code_existe) {
            $nouvelle_reduc = array(
                "code" => $nouveau_code,
                "valeur" => $nouvelle_valeur
            );
            $les_reductions[] = $nouvelle_reduc;
            file_put_contents($fichier_reduc, json_encode($les_reductions, JSON_PRETTY_PRINT));
            $message_admin_promo = "Le code promo a été ajouté avec succès !";
            $classe_admin_promo = "promo-succes";
        } else {
            $message_admin_promo = "Ce code promo existe déjà.";
            $classe_admin_promo = "promo-erreur";
        }
    } else {
        $message_admin_promo = "Veuillez remplir correctement tous les champs.";
        $classe_admin_promo = "promo-erreur";
    }
}

$fichier = "utilisateurs.json";
$utilisateurs = array();
if (file_exists($fichier)) {
    $contenu = file_get_contents($fichier);
    $decoded = json_decode($contenu, true);
    if ($decoded !== null) {
        $utilisateurs = $decoded;
    }
}

$total_inscrits = count($utilisateurs);

if (isset($_GET['filtre'])) {
    $filtre = $_GET['filtre'];
} else {
    $filtre = 'tous';
}

$roles_map = array(
    'tous'      => null,
    'client'    => 'client',
    'cuisinier' => 'cuisinier',
    'livreur'   => 'livreur',
    'admin'     => 'Admin',
    'bloque'    => 'bloqué',
);

if (isset($roles_map[$filtre])) {
    $role_filtre = $roles_map[$filtre];
} else {
    $role_filtre = null;
}

if ($role_filtre !== null) {
    $utilisateurs_affiches = array_filter($utilisateurs, function($u) use ($role_filtre) {
        if (isset($u['role'])) {
            return $u['role'] === $role_filtre;
        }
        return false;
    });
} else {
    $utilisateurs_affiches = $utilisateurs;
}

if (isset($_GET['recherche'])) {
    $recherche = trim($_GET['recherche']);
} else {
    $recherche = '';
}

if ($recherche !== '') {
    $recherche_lower = mb_strtolower($recherche);
    $utilisateurs_affiches = array_filter($utilisateurs_affiches, function($u) use ($recherche_lower) {
        $nom_complet = mb_strtolower($u['prenom'] . ' ' . $u['nom']);
        $email       = mb_strtolower($u['email']);
        return str_contains($nom_complet, $recherche_lower) || str_contains($email, $recherche_lower);
    });
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrateur</title>
    <script src="darkmode.js"></script>
    <link rel="stylesheet" href="administrateur.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="couleur.css">
    <script src="darkmode.js"></script>
</head>
<body>

    <div class="structure-admin">
        <aside class="barre-laterale">
            <div>
                <a href="accueil.php" class="lien-logo">
                    <div class="logo"><span>Exotique</span> Dream</div>
                </a>
            </div>
            <nav class="navigation-admin">
                <a href="administrateur.php" class="actif"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="commandes.php"><i class="fas fa-shopping-cart"></i> Commandes</a>
                <a href="menu.php"><i class="fas fa-utensils"></i> Carte</a>
                <a href="accueil.php" class="quitter"><i class="fas fa-arrow-left"></i> Quitter</a>
            </nav>
        </aside>

        <main class="zone-principale">
            <header class="barre-haute">
                <h1>Gestion des Utilisateurs</h1>
                <div class="infos-admin">
                    <span>Admin Principal</span>
                    <div class="avatar-admin">A</div>
                </div>
            </header>

            <div class="grille-statistiques">
                <div class="boite-stat">
                    <span class="etiquette">Total Inscrits</span>
                    <p class="valeur"><?php echo $total_inscrits; ?></p>
                </div>
                <div class="boite-stat">
                    <span class="etiquette">Affichés</span>
                    <p class="valeur"><?php echo count($utilisateurs_affiches); ?></p>
                </div>
            </div>

            <div class="bloc-creation-promo">
                <h2>Créer un nouveau code promo</h2>
                
                <?php if ($message_admin_promo !== ""): ?>
                    <div class="alerte-promo <?php echo $classe_admin_promo; ?>">
                        <?php echo $message_admin_promo; ?>
                    </div>
                <?php endif; ?>

                <form action="administrateur.php" method="POST" class="form-admin-promo">
                    <input type="text" name="nouveau_code" placeholder="CODE PROMO (Ex: RECON15)" required autocomplete="off">
                    <input type="number" name="nouvelle_valeur" placeholder="Montant de la réduction (€)" min="1" step="0.01" required>
                    <button type="submit" name="btn_ajouter_promo">Générer le code</button>
                </form>
            </div>

            <div class="section-info">
                <div class="entete-liste">
                    <h2>Base de données</h2>
                    <form method="GET" action="administrateur.php" class="elements" id="form-filtre">
                        <input
                            type="text"
                            name="recherche"
                            placeholder="Rechercher..."
                            class="recherche-admin"
                            value="<?php echo htmlspecialchars($recherche); ?>"
                            oninput="clearTimeout(this._t); this._t=setTimeout(function(){ document.getElementById('form-filtre').submit(); }, 300)"
                        >
                        <select name="filtre" class="selection-filtre" onchange="this.form.submit()">
                            <?php
                            $options = array(
                                'tous'      => 'Tous les profils',
                                'client'    => 'Clients',
                                'cuisinier' => 'Cuisiniers',
                                'livreur'   => 'Livreurs',
                                'admin'     => 'Administrateurs',
                                'bloque'    => 'Bloqués',
                            );
                            foreach ($options as $valeur => $label) {
                                echo '<option value="' . $valeur . '"';
                                if ($filtre === $valeur) {
                                    echo ' selected';
                                }
                                echo '>' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </form>
                </div>

                <table class="tableau-utilisateurs">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Remise</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($utilisateurs_affiches)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:40px; color:#a4b0be;">Aucun utilisateur trouvé.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($utilisateurs_affiches as $user): ?>
                        <tr>
                            <td><strong><?php echo $user["prenom"] . " " . $user["nom"]; ?></strong></td>
                            <td><?php echo $user["email"]; ?></td>
                            <td>
                                <?php if ($user["id"] != $_SESSION["id_user"]): ?>
                                    <form action="update_role.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_user" value="<?php echo $user['id']; ?>">
                                        <select name="nouveau_role" onchange="this.form.submit()" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
                                            <?php
                                            $roles_dispo = array(
                                                'client'    => 'Client',
                                                'cuisinier' => 'Cuisinier',
                                                'livreur'   => 'Livreurs',
                                                'Admin'     => 'Admin',
                                                'bloqué'    => 'BLOQUÉ',
                                            );
                                            foreach ($roles_dispo as $val => $nom_role) {
                                                echo '<option value="' . $val . '"';
                                                if ($user["role"] === $val) {
                                                    echo ' selected';
                                                }
                                                echo '>' . $nom_role . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <strong>Admin</strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                if (isset($user["remise"])) {
                                    $remise = $user["remise"];
                                } else {
                                    $remise = "0";
                                }
                                ?>
                                <span class="badge-remise"><?php echo $remise; ?>%</span>
                            </td>
                            <td class="cellule-actions">
                                <a href="affiche_profil.php?id=<?php echo $user['id']; ?>" class="btn-action-admin bleu">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
