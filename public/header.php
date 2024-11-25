<?php
session_start();
require_once 'config/database.php'; // Votre fichier de configuration de base de données

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Connexion à la base de données
    $db = Database::getConnection();
    
    // Vérifier si une préférence de thème existe pour cet utilisateur
    $stmt = $db->prepare("SELECT theme FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_setting = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si aucune préférence n'est définie, on initialise à "light"
    $theme = ($user_setting && isset($user_setting['theme'])) ? $user_setting['theme'] : 'light';

    // Appliquer le thème dans la session
    $_SESSION['theme'] = $theme;
} else {
    // Si l'utilisateur n'est pas connecté, appliquer le thème par défaut "light"
    $_SESSION['theme'] = 'light';
}

// Charger la classe CSS appropriée en fonction du thème
$theme_class = ($_SESSION['theme'] === 'dark') ? 'dark-theme' : 'light-theme';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tâches</title>
    <!-- Liens vers les fichiers Bootstrap CSS et les thèmes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/light-theme.css">  <!-- Style pour le thème clair -->
    <link rel="stylesheet" href="css/dark-theme.css">   <!-- Style pour le thème sombre -->
</head>
<body class="<?= $theme_class ?>"> <!-- Appliquer la classe CSS pour le thème -->

<!-- NavBar et autres éléments du header -->
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-center">Gestion des Tâches</h1>
        <div>
            <!-- Bouton pour basculer entre les thèmes -->
            <button class="btn btn-primary theme-toggle">
                <?= ($_SESSION['theme'] === 'dark') ? 'Basculer vers Clair' : 'Basculer vers Sombre' ?>
            </button>
        </div>
    </div>
</div>

<!-- Ajouter le script JavaScript pour le changement de thème -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const themeToggleButton = document.querySelector(".theme-toggle");

        themeToggleButton.addEventListener("click", function() {
            // Détecter l'état actuel du thème
            const currentTheme = document.body.classList.contains("dark-theme") ? "dark" : "light";
            const newTheme = (currentTheme === "dark") ? "light" : "dark";

            // Basculer entre les classes de thème
            document.body.classList.toggle("dark-theme", newTheme === "dark");
            document.body.classList.toggle("light-theme", newTheme === "light");

            // Changer le texte du bouton
            themeToggleButton.textContent = (newTheme === "dark") ? "Basculer vers Clair" : "Basculer vers Sombre";

            // Envoyer la nouvelle préférence de thème au serveur via AJAX
            fetch("update_theme.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ theme: newTheme })
            }).then(response => {
                if (!response.ok) {
                    console.error("Erreur lors de la mise à jour du thème.");
                }
            });
        });
    });
</script>

</body>
</html>
