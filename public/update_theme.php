<?php
session_start();
require_once 'config/database.php'; // Inclure votre fichier de configuration

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id']) && isset($_POST['theme'])) {
    $user_id = $_SESSION['user_id'];
    $theme = $_POST['theme'];

    // Valider le thème
    if ($theme !== 'light' && $theme !== 'dark') {
        echo json_encode(["error" => "Thème invalide"]);
        exit;
    }

    // Connexion à la base de données
    $db = Database::getConnection();

    // Mettre à jour le thème de l'utilisateur dans la base de données
    $stmt = $db->prepare("INSERT INTO user_settings (user_id, theme) 
                          VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE theme = ?");
    $stmt->execute([$user_id, $theme, $theme]);

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Utilisateur non connecté ou thème non fourni"]);
}
?>
