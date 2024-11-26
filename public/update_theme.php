<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Non autorisé
    echo json_encode(['error' => 'Utilisateur non connecté.']);
    exit;
}

// Vérifier si le thème est passé dans la requête
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['theme'])) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['error' => 'Aucun thème fourni.']);
    exit;
}

// Récupérer l'ID utilisateur et le thème
$user_id = $_SESSION['user_id'];
$new_theme = $data['theme'];

// Mettre à jour la préférence de thème dans la base de données
try {
    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO user_settings (user_id, theme) 
                          VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE theme = VALUES(theme)");
    $stmt->execute([$user_id, $new_theme]);

    // Mettre à jour la session pour refléter le changement
    $_SESSION['theme'] = $new_theme;

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500); // Erreur serveur
    echo json_encode(['error' => 'Erreur lors de la mise à jour du thème.']);
}
?>
