<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';

// Récupérer l'ID utilisateur connecté
$user_id = $_SESSION['user_id'];

// Obtenir le mode de tri (par défaut : priorité)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'priority';

// Construire la requête en fonction du tri
if ($sort === 'priority') {
    $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY FIELD(priority, 'high', 'medium', 'low'), due_date ASC";
} elseif ($sort === 'due_date') {
    $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC, FIELD(priority, 'high', 'medium', 'low')";
}

// Exécuter la requête pour récupérer les tâches
$db = Database::getConnection();
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tasks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <header class="mb-4">
            <h1 class="text-center">Your Tasks</h1>
            <div class="d-flex justify-content-end">
                <a href="home.php" class="btn btn-secondary">Home</a>
                <a href="logout.php" class="btn btn-danger ms-2">Logout</a>
            </div>
        </header>

        <!-- Add New Task Button -->
        <div class="mb-4">
            <a href="create_task.php" class="btn btn-primary">Add New Task</a>
        </div>

        <!-- Sort Options -->
        <div class="d-flex justify-content-end mb-3">
            <form method="get" action="" class="d-inline">
                <select name="sort" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <option value="priority" <?= $sort === 'priority' ? 'selected' : '' ?>>Sort by Priority</option>
                    <option value="due_date" <?= $sort === 'due_date' ? 'selected' : '' ?>>Sort by Due Date</option>
                </select>
            </form>
        </div>

        <!-- Display Tasks -->
        <?php if (empty($tasks)) : ?>
            <p>No tasks available. <a href="create_task.php">Add a new task!</a></p>
        <?php else : ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task) : ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($task['status'])) ?></td>
                            <td><?= htmlspecialchars($task['due_date']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($task['priority'])) ?></td>
                            <td>
                                <a href="update_task.php?id=<?= $task['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php
                        // Récupérer les sous-tâches associées à cette tâche
                        $query = "SELECT * FROM subtasks WHERE task_id = ?";
                        $sub_stmt = $db->prepare($query);
                        $sub_stmt->execute([$task['id']]);
                        $subtasks = $sub_stmt->fetchAll();
                        ?>
                        <?php if (!empty($subtasks)) : ?>
                            <tr>
                                <td colspan="5">
                                    <strong>Subtasks:</strong>
                                    <ul>
                                        <?php foreach ($subtasks as $subtask) : ?>
                                            <li>
                                                <?= htmlspecialchars($subtask['title']) ?> 
                                                (<em><?= htmlspecialchars($subtask['status']) ?></em>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
