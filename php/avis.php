<?php
$file = "../json/notation.json";
$avis_liste = [];
$moyenne = 0;
$total_notes = 0;
$compteur_notes = 0;
$liste_noms = ["Michael Jackson", "Napoléon Bonaparte", "Lionel Messi", "Marilyn Monroe", "Albert Einstein","Kratos", "Taylor Swift", "Marie Curie", "Kylian Mbappé", "Elon Musk", "Winston Churchill", "Beyoncé Knowles", "Stephen Hawking", "Johnny Depp", "Vladimir Poutine", "Harry Potter", "Sherlock Holmes", "Bruce Wayne", "Clark Kent", "Tony Stark", "Darth Vader", "Walter White", "Homer Simpson", "Lara Croft", "Gandalf Le Gris", "Squeezie Dupont", "Mister Beast", "Mario Bros", "Emmanuel Macron", "Widowmaker","Terracid","Amine matue","jhonny sins","homelander","Dexter Morgane"];
if (file_exists($file)) {
    $json_content = file_get_contents($file);
    $avis_liste = json_decode($json_content, true) ?? [];
}
$avis_liste = array_reverse($avis_liste);
foreach ($avis_liste as $avis) {
    if (isset($avis['note']) && $avis['note'] !== "") {
        $total_notes += floatval($avis['note']);
        $compteur_notes++;
    }
}
if ($compteur_notes > 0) {
    $moyenne = round($total_notes / $compteur_notes, 1);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis Livreurs - Exotique Dream</title>
    <link rel="stylesheet" href="../css/couleur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/avis.css">
    <script src="../js/darkmode.js"></script>
</head>
<body>
    <header class="header">
        <a href="accueil.php" class="logo">EXOTIQUE<span> DREAM</span></a>
        <nav class="milieu">
            <a href="accueil.php">Accueil</a>
            <a href="avis.php" class="active">Avis Livrraisons</a>
        </nav>
        <div class="droite">
            <button class="dark-toggle" id="dark-toggle" aria-label="Mode sombre">
                <span class="toggle-icon icon-moon">🌙</span>
                <span class="toggle-icon icon-sun">☀️</span>
            </button>
            <div class="avatar-cercle">ED</div>
        </div>
    </header>
    <main class="page-container">
        <section class="section-stats">
            <div class="carte-stat-livreur">
                <div class="avatar-livreur">
                    <i class="fas fa-bicycle"></i>
                </div>
                <h2>Yves Oikeudal</h2>
                <p class="role-livreur">Livreur Officiel</p>
            </div>
            <div class="carte-stat-note">
                <h3>Note Moyenne</h3>
                <div class="note-chiffre"><?= $moyenne ?> <span>/ 5</span></div>
                <div class="etoiles-globales">
                    <?php 
                    $note_entiere = floor($moyenne);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $note_entiere) {
                            echo '<i class="fas fa-star or"></i>';
                        } elseif ($i - 0.5 <= $moyenne) {
                            echo '<i class="fas fa-star-half-alt or"></i>';
                        } else {
                            echo '<i class="far fa-star or"></i>';
                        }
                    }
                    ?>
                </div>
                <p class="total-avis"><?= count($avis_liste) ?> avis au total</p>
            </div>
        </section>
        <section class="section-avis-liste">
            <h2>Commentaires des clients</h2>
            
            <?php if (empty($avis_liste)): ?>
                <div class="aucun-avis">
                    <i class="far fa-comment-dots"></i>
                    <p>Aucun avis n'a encore été laissé pour ce livreur.</p>
                </div>
            <?php else: ?>
                <div class="grille-avis">
                    <?php foreach ($avis_liste as $avis): ?>
                        <div class="carte-avis">
                            <div class="entete-carte-avis">
                                <div class="client-info">
                                    <div class="avatar-client">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?php 
                                        $index_aleatoire = array_rand($liste_noms);
                                        echo htmlspecialchars($liste_noms[$index_aleatoire]); 
                                        ?>
                                </div>
                                <div class="note-avis">
                                    <?php 
                                    $note_client = isset($avis['note']) && $avis['note'] !== "" ? intval($avis['note']) : 0;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $note_client) {
                                            echo '<i class="fas fa-star or"></i>';
                                        } else {
                                            echo '<i class="far fa-star gris"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <p class="commentaire-texte">
                                <?= !empty($avis['commentaire']) ? htmlspecialchars($avis['commentaire']) : '<em>L\'utilisateur n\'a pas laissé de commentaire écrit.</em>' ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <footer class="footer">
        <div class="footer-section">
            <h3>À PROPOS</h3>
            <p>Exotique Dream vous livre vos produits exotiques préférés en un temps record.</p>
        </div>
        <div class="footer-section">
            <h3>LIENS UTILES</h3>
            <a href="com.php">Retour à la page de communication</a>
            <a href="accueil.php">Retour à l'accueil</a>
            <a href="mentions.php">Mentions Légales</a>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p><i class="fas fa-map-marker-alt"></i> - 12 Avenue des Saveurs, Cergy</p>
            <p><i class="fas fa-phone"></i> - 01 23 45 67 89</p>
            <p><i class="fas fa-envelope"></i> - contact@exotiquedream.fr</p>
        </div>
        <div class="footer-bas">
            <span>© 2026 EXOTIQUE DREAM. Tous droits réservés.</span>
            <div class="reseaux">
                <i class="fab fa-facebook"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-twitter"></i>
            </div>
        </div>
    </footer>

</body>
</html>
