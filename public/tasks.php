<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'priority';

if ($sort === 'priority') {
    $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY FIELD(priority, 'high', 'medium', 'low'), due_date ASC";
} elseif ($sort === 'due_date') {
    $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC, FIELD(priority, 'high', 'medium', 'low')";
}

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

    <div class="mb-4">
        <a href="create_task.php" class="btn btn-primary">Add New Task</a>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <form method="get" action="" class="d-inline">
            <select name="sort" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="priority" <?= $sort === 'priority' ? 'selected' : '' ?>>Sort by Priority</option>
                <option value="due_date" <?= $sort === 'due_date' ? 'selected' : '' ?>>Sort by Due Date</option>
            </select>
        </form>
    </div>

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
                    // Fetch subtasks for the current task
                    $query = "SELECT * FROM subtasks WHERE task_id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$task['id']]);
                    $subtasks = $stmt->fetchAll();
                    ?>
                    <?php if (!empty($subtasks)): ?>
                        <tr>
                            <td colspan="5
