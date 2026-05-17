<?php
session_start();
$livreur = "Yves Oikeudal";
$file = "notation.json";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btn-valider"])) {

    if (isset($_POST["note"])) {
        $note = $_POST["note"];
    } else {
        $note = "0";
    }

    if (isset($_POST["comment"])) {
        $comment = $_POST["comment"];
    } else {
        $comment = "";
    }

    if (isset($_POST["pourboire"])) {
        $pourboire = $_POST["pourboire"];
    } else {
        $pourboire = "0";
    }

    $avis = array(
        "note" => $note,
        "commentaire" => $comment,
        "livreur" => $livreur,
        "pourboire" => $pourboire
    );

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    } else {
        $data = array();
    }

    $data[] = $avis;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    $fichier_commandes = "commandes.json";
    $fichier_histo = "histo_commande.json";

    if (file_exists($fichier_commandes)) {
        $commandes = json_decode(file_get_contents($fichier_commandes), true);
    } else {
        $commandes = array();
    }

    if (file_exists($fichier_histo)) {
        $historique = json_decode(file_get_contents($fichier_histo), true);
    } else {
        $historique = array();
    }

    if (isset($_SESSION["nom"]) && isset($_SESSION["prenom"])) {
        $nom_client = $_SESSION["nom"];
        $prenom_client = $_SESSION["prenom"];
        $index_commande = -1;

        foreach ($commandes as $index => $cmd) {
            if ($cmd["nom"] === $nom_client && $cmd["prenom"] === $prenom_client) {
                $index_commande = $index;
            }
        }

        if ($index_commande !== -1) {
            $commande_a_deplacer = $commandes[$index_commande];
            $commande_a_deplacer["date"] = date("d/m/Y");
            
            $historique[] = $commande_a_deplacer;
            file_put_contents($fichier_histo, json_encode($historique, JSON_PRETTY_PRINT));

            array_splice($commandes, $index_commande, 1);
            file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
        }
    }

    $_SESSION['pourboire'] = $pourboire;

    if ($pourboire === "0" || $pourboire === "") {
        header("Location: accueil.php");
        exit();
    } else {
        header("Location: paiement_pourboire.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Noter le livreur</title>
<link rel="stylesheet" href="notation.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="couleur.css">
</head>
<body>
<div class="carte-notation">
<header class="entete-avis">
<div class="logo">EXOTIQUE<span> DREAM</span></div>
<h1>Commande livrée !</h1>
<p>Comment s'est passée votre livraison avec <strong>Yves Oikeudal</strong> ?</p>
</header>
<main>
<section class="bloc-note">
<div class="cercle-icone">
<i class="fas fa-bicycle"></i>
</div>
<div class="note" id="affichage-note">Note : 0 / 5</div>
<h3>Notez votre livreur</h3>
<div class="groupe-etoiles">
<input type="radio" name="note-livreur" id="l5"><label for="l5" class="fas fa-star"></label>
<input type="radio" name="note-livreur" id="l4"><label for="l4" class="fas fa-star"></label>
<input type="radio" name="note-livreur" id="l3"><label for="l3" class="fas fa-star"></label>
<input type="radio" name="note-livreur" id="l2"><label for="l2" class="fas fa-star"></label>
<input type="radio" name="note-livreur" id="l1"><label for="l1" class="fas fa-star"></label>
</div>
</section>
<section class="bloc-pourboire">
<h3>Ajouter un pourboire</h3>
<div class="choix-pourboire">
<input type="radio" name="pourboire" id="p0" value="0">
<label for="p0">0€</label>
<input type="radio" name="pourboire" id="p-perso" value="autre">
<label for="p-perso">Autre</label></div>
<div id="montant-perso-zone">
<input type="number" id="montant-perso" name="montant_perso" min="1" step="1" placeholder="Montant pourboir (€)"></div>
</section>
<div class="zone-commentaire">
<textarea id="commentaire" name="commentaire" maxlength="1000"></textarea>
<div id="compteur">0 / 1000</div>
<form action="" method="POST">
<input type="hidden" name="note" id="note-cache">
<input type="hidden" name="pourboire" id="pourboire-cache" value="0">
<input type="hidden" name="commentaire" id="commentaire-cache">
<button type="submit" name="btn-valider" class="valider" id="btn-valider">
VALIDER MON AVIS
</button></form>
<a href="accueil.php" class="bouton-passer">Passer cette étape</a>
</main>
</div>
<script>
let pourboireCache = document.getElementById("pourboire-cache");
let listeEtoiles = document.querySelectorAll('input[name="note-livreur"]');
let champNote = document.getElementById("note-cache");
let listePourboires = document.querySelectorAll('input[name="pourboire"]');
let boutonValidation = document.getElementById("btn-valider");
let zoneMontant = document.getElementById("montant-perso-zone");
let champMontant = document.getElementById("montant-perso");
let affichageNote = document.getElementById("affichage-note");
let commentaire = document.getElementById("commentaire");
let compteur = document.getElementById("compteur");
let champCommentaire = document.getElementById("commentaire-cache");

zoneMontant.style.display = "none";

for(let i = 0; i < listeEtoiles.length; i++) {
    listeEtoiles[i].addEventListener("change", function() {
        let note = 5 - i;
        champNote.value = note;
        affichageNote.textContent = "Note : " + note + " / 5";
    });
}

for(let i = 0; i < listePourboires.length; i++) {
    listePourboires[i].addEventListener("change", function() {
        if(this.value == "autre") {
            zoneMontant.style.display = "block";
            pourboireCache.value = champMontant.value;
            boutonValidation.textContent = "VALIDER ET PAYER";
        } else {
            zoneMontant.style.display = "none";
            pourboireCache.value = this.value;
            boutonValidation.textContent = "VALIDER MON AVIS";
        }
    });
}

champMontant.addEventListener("input", function() {
    if(this.value < 0) {
        this.value = 1;
    }
    if(document.getElementById("p-perso").checked) {
        pourboireCache.value = this.value;
    }
});

commentaire.addEventListener("input", function() {
    let longueur = commentaire.value.length;
    compteur.textContent = longueur + " / 1000";
    champCommentaire.value = commentaire.value;
});
</script>
</body>
</html>
