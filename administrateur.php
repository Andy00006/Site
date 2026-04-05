<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    header("Location: accueil.php");
    exit();
}

$fichier = "utilisateurs.json";
$utilisateurs = array();
if (file_exists($fichier)) {
    $contenu = file_get_contents($fichier);
    $utilisateurs = json_decode($contenu, true) ?? [];
}

$total_inscrits = count($utilisateurs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrateur</title>
    <link rel="stylesheet" href="administrateur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="couleur.css">
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
            </div>

            <div class="section-info"> <div class="entete-liste">
                    <h2>Base de données</h2>
                    <div class="elements">
                        <input type="text" placeholder="Rechercher..." class="recherche-admin">
                        <select class="selection-filtre">
                            <option>Tous les profils</option>
                            <option>Clients</option>
                            <option>Restaurateurs</option>
                            <option>Livreurs</option>
                        </select>
                    </div>
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
                        <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><strong><?php echo $user["prenom"] . " " . $user["nom"]; ?></strong></td>
                            <td><?php echo $user["email"]; ?></td>
                            <td>
                                <?php if ($user["id"] != $_SESSION["id_user"]): ?>
                                    <form action="update_role.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_user" value="<?= $user['id'] ?>">
                                        <select name="nouveau_role" onchange="this.form.submit()" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
                                            <option value="client" <?= ($user["role"] === "client") ? "selected" : "" ?>>Client</option>
                                            <option value="cuisinier" <?= ($user["role"] === "cuisinier") ? "selected" : "" ?>>Cuisinier</option>
                                            <option value="livreur" <?= ($user["role"] === "livreur") ? "selected" : "" ?>>Livreur</option>
                                            <option value="Admin" <?= ($user["role"] === "Admin") ? "selected" : "" ?>>Admin</option>
                                            <option value="bloqué" <?= ($user["role"] === "bloqué") ? "selected" : "" ?>>🚫 BLOQUÉ</option>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <strong>Admin</strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-remise"><?php echo $user["remise"] ?? "0"; ?>%</span>
                            </td>
                            <td class="cellule-actions">
                                <a href="affiche_profil.php?id=<?= $user['id'] ?>" class="btn-action-admin bleu">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
