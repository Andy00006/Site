<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$temps_limite = 1000;
if (isset($_SESSION["role"])) {
    if (isset($_SESSION["derniere_activite"])) {
        $temps_inactif = time() - $_SESSION["derniere_activite"];
        if ($temps_inactif > $temps_limite) {
            session_unset();
            session_destroy();
            header("Location: accueil.php");
            exit();
        }
    }
    $_SESSION["derniere_activite"] = time();
}
?>