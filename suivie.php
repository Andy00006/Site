<?php
session_start();
date_default_timezone_set('Europe/Paris');

$commandes_file = 'commandes.json';
$ma_commande = null;

if (file_exists($commandes_file)) {
    $contenu = file_get_contents($commandes_file);
    $commandes = json_decode($contenu, true);
    
    if (!empty($commandes)) {
        $ma_commande = end($commandes);
    }
}

$libelle_statut = [
    "a_preparer" => "En attente de préparation",
    "en_cours_de_prep" => "En cours de préparation 👨‍🍳",
    "en_livraison" => "En cours de livraison 🛵",
    "en_cours_de_livr" => "Le livreur arrive ! 🏁",
    "livre" => "Commande livrée ✅"
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi - Exotique Dream</title>
    <link rel="stylesheet" href="suivie.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="container">
        <header class="header">
            <div class="logo"><span>Exotique</span> Dream</div>
            <h1>Suivi de commande</h1>
        </header>

        <?php if ($ma_commande): ?>
            <div class="card-suivi">
                <div class="statut-badge <?php echo $ma_commande['statut']; ?>">
                    <?php 
                    $code_statut = $ma_commande['statut'];
                    if (isset($libelle_statut[$code_statut])) {
                        echo $libelle_statut[$code_statut];
                    } else {
                        echo "Statut inconnu";
                    }
                    ?>
                </div>

                <div class="info-principale">
                    <p>Commande <strong>#<?php echo $ma_commande['id']; ?></strong></p>
                    <p>Heure : <strong><?php echo $ma_commande['heure']; ?></strong></p>
                </div>

                <hr>

                <div class="liste-articles">
                    <?php foreach ($ma_commande['panier'] as $item): ?>
                        <div class="article">
                            <span class="qte"><?php echo $item['quantite']; ?>x</span>
                            <span class="nom">
                                <?php 
                                if (isset($item['nom_menu'])) {
                                    echo htmlspecialchars($item['nom_menu']);
                                } else {
                                    echo htmlspecialchars($item['nom_plat']);
                                }
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($ma_commande['info_livraison'])): ?>
                    <div class="info-livraison">
                        <i class="fas fa-truck"></i> <?php echo htmlspecialchars($ma_commande['info_livraison']); ?>
                    </div>
                <?php endif; ?>
            </div>
<div class="actions">
                <?php if ($ma_commande['statut'] == 'livre'): ?>
                    <a href="notation.php?<?php echo $ma_commande['id']; ?>" class="btn-avis">
                        DONNER MON AVIS
                    </a>
                <?php endif; ?>
                
                <a href="notation.php" class="btn-retour">Retour à l'accueil</a>
            </div>

        <?php else: ?>
            <div class="card-suivi" style="text-align: center;">
                <p>Aucune commande en cours.</p>
                <a href="index.php" class="btn-retour">Commander</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
