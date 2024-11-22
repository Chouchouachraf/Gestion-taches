<?php
// Démarrer la session
session_start();

// Détruire la session
session_unset();  // Libère toutes les variables de session
session_destroy(); // Détruit la session

// Rediriger l'utilisateur vers la page d'accueil ou la page de connexion
header("Location: login.php");
exit();
?>
