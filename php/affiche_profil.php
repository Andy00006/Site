<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    header("Location: accueil.php");
    exit();
}

$json_content = file_get_contents('../json/utilisateurs.json');
$profils = json_decode($json_content, true);
$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$profil_actuel = null;

if (!empty($profils)) {
    foreach ($profils as $profil) {
        if ($profil['id'] == $id_demande) {
            $profil_actuel = $profil;
            break; 
        }
    }
}

if (!$profil_actuel) {
    header("Location: administrateur.php");
    exit();
}

$initiale_p = substr($profil_actuel["prenom"], 0, 1);
$initiale_n = substr($profil_actuel["nom"], 0, 1);
$initiales = strtoupper($initiale_p . $initiale_n);

$histo_file = '../json/histo_commande.json';
$historique_commandes = array();

if (file_exists($histo_file)) {
    $historique_commandes = json_decode(file_get_contents($histo_file), true);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur - Admin</title>
    <link rel="stylesheet" href="profil.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/couleur.css">
</head>
<body>

    <div class="profil">
        
        <div class="profil-header">
            <div class="avatar-logo"><?php echo $initiales; ?></div>
            <h2>Profil Utilisateur</h2>
            <p>Consultation du compte de <?php echo htmlspecialchars($profil_actuel["prenom"] . " " . $profil_actuel["nom"]); ?></p>
        </div>

        <div class="formulaire-utopik">

            <div class="section">
                <div class="categorie">
                    <label>Informations & Sécurité (Lecture seule)</label>
                </div>
                
                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Prénom</span>
                        <p><?php echo htmlspecialchars($profil_actuel["prenom"]); ?></p>
                    </div>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Nom</span>
                        <p><?php echo htmlspecialchars($profil_actuel["nom"]); ?></p>
                    </div>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Email</span>
                        <p><?php echo htmlspecialchars($profil_actuel["email"]); ?></p> 
                    </div>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Téléphone</span>
                        <p><?php echo htmlspecialchars($profil_actuel["tel"]); ?></p>
                    </div>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Adresse de livraison</span>
                        <p><?php echo htmlspecialchars($profil_actuel["adresse"]); ?></p>
                    </div>
                </div>

                <div class="champ-profil">
                    <div class="texte-info">
                        <span>Mot de passe</span>
                        <p>********</p>
                    </div>
                </div>
            </div>

            <div class="section" style="border-bottom: none;">
                <div class="categorie">
                    <label>Historique des commandes du client</label>
                </div>
                <ul class="liste-commandes">
                <?php
                $a_des_commandes = false;
                if (!empty($historique_commandes)) {
                    foreach ($historique_commandes as $commande) {
                        if ($commande["nom"] === $profil_actuel["nom"] && $commande["prenom"] === $profil_actuel["prenom"]) {
                            $a_des_commandes = true;
                            echo "<li>";
                            echo "<div>";
                            foreach ($commande["panier"] as $item) {
                                if (isset($item["nom_menu"])) {
                                    echo htmlspecialchars($item["quantite"]) . "x " . htmlspecialchars($item["nom_menu"]) . "<br>";
                                } else {
                                    echo htmlspecialchars($item["quantite"]) . "x " . htmlspecialchars($item["nom_plat"]) . "<br>";
                                }
                            }
                            echo "</div>";
                            echo "<span class='date'>" . htmlspecialchars($commande["date"]) . "</span>";
                            echo "</li>";
                        } 
                    }
                }
                
                if (!$a_des_commandes) {
                    echo '<li style="color: #a4b0be; font-style: italic;">Aucune commande enregistrée pour ce client.</li>';
                }
                ?>
                </ul>
            </div>

            <div class="actions-profil">
                <a href="administrateur.php">
                    <button type="button" class="btn-principal">Retour à l'administration</button>
                </a>
            </div>

        </div>
    </div>

</body>
</html>
