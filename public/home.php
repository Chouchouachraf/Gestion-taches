<?php
include('header.php');


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connexion à la base de données
require_once '../config/database.php';
$user_id = $_SESSION['user_id'];
$db = Database::getConnection();

// Récupérer les tâches de l'utilisateur connecté
$query = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

// Gestion du thème
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';  // Thème par défaut
}

$theme_class = ($_SESSION['theme'] === 'dark') ? 'dark-theme' : 'light-theme';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chargement des fichiers CSS pour les thèmes -->
    <link rel="stylesheet" href="light-theme.css"> 
    <link rel="stylesheet" href="dark-theme.css">
</head>
<body class="<?= $theme_class ?>"> <!-- Appliquer la classe du thème -->

    <div class="container my-5">
        <header class="mb-4">
            <h1 class="text-center">Welcome to your Task Dashboard</h1>
            <div class="d-flex justify-content-end">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <div class="mb-4">
            <a href="create_task.php" class="btn btn-primary">Add New Task</a>
            <a href="tasks.php" class="btn btn-secondary">View Tasks</a>
            <a href="create_category.php" class="btn btn-info">Manage Categories</a>
        </div>

        <h2>Your Tasks</h2>
        <?php if (empty($tasks)) : ?>
            <p>No tasks available. <a href="create_task.php">Add a new task!</a></p>
        <?php else : ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task) : ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($task['status'])) ?></td>
                            <td><?= htmlspecialchars($task['due_date']) ?></td>
                            <td>
                                <a href="update_task.php?id=<?= $task['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
