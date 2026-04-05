<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    header("Location: accueil.php");
    exit();
}

$id_user = $_POST['id_user'] ?? null;
$nouveau_role = $_POST['nouveau_role'] ?? null;
$fichier = "utilisateurs.json";

if ($id_user && $nouveau_role && file_exists($fichier)) {
    $utilisateurs = json_decode(file_get_contents($fichier), true);

    foreach ($utilisateurs as &$user) {
        if ($user['id'] == $id_user) {
            $user['role'] = $nouveau_role;
            break;
        }
    }

    file_put_contents($fichier, json_encode($utilisateurs, JSON_PRETTY_PRINT));
}

header("Location: administrateur.php");
exit();
