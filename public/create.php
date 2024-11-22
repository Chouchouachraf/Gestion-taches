<?php
require_once '../config/database.php'; // Assurez-vous que le chemin est correct
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données via la méthode getConnection
$db = Database::getConnection();  // Utilisez getConnection() et non connect()

// Ajouter la tâche si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $user_id = $_SESSION['user_id'];

    // Insertion de la nouvelle tâche dans la base de données
    $sql = "INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $due_date);

    if ($stmt->execute()) {
        // Message de succès
        $_SESSION['success_message'] = "Tâche ajoutée avec succès !";
        header('Location: accueil.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de l'ajout de la tâche.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une tâche</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="create.php">Créer une tâche</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Formulaire de création de tâche</h1>

        <form action="create.php" method="POST">
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title" required><br>

            <label for="description">Description :</label>
            <textarea name="description" id="description"></textarea><br>

            <label for="due_date">Date limite :</label>
            <input type="date" name="due_date" id="due_date"><br>

            <button type="submit">Créer la tâche</button>
        </form>
    </div>
</body>
</html>

