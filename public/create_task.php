<?php
include('header.php');
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input values from the form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'] ?? 'low'; // Default to 'low' if not provided
    $subtasks = $_POST['subtasks'] ?? []; // Subtasks array

    // Validate the form data
    if (empty($title) || empty($status)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Insert the new task into the database
        $db = Database::getConnection(); // Get the PDO instance
        $query = "INSERT INTO tasks (user_id, title, description, status, due_date, priority) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $title, $description, $status, $due_date, $priority]);

        // Get the ID of the newly created task
        $task_id = $db->lastInsertId();

        // Insert subtasks into the database
        if (!empty($subtasks)) {
            foreach ($subtasks as $subtask) {
                if (!empty($subtask)) { // Skip empty subtask fields
                    $query = "INSERT INTO subtasks (task_id, title, status) VALUES (?, ?, 'pending')";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$task_id, $subtask]);
                }
            }
        }

        header("Location: tasks.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <header class="text-center mb-4">
        <h1>Create New Task</h1>
    </header>

    <div class="mb-4">
        <a href="tasks.php" class="btn btn-secondary">Back to Tasks</a>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <form action="create_task.php" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Task Title</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="priority" class="form-label">Priority</label>
            <select name="priority" id="priority" class="form-select" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" id="due_date" name="due_date" class="form-control">
        </div>

        <div class="mb-3">
            <label for="subtasks" class="form-label">Subtasks</label>
            <input type="text" name="subtasks[]" class="form-control mb-2" placeholder="Subtask 1">
            <div id="subtasks-container"></div>
            <button type="button" class="btn btn-secondary" onclick="addSubtaskField()">Add Subtask</button>
        </div>

        <button type="submit" class="btn btn-primary">Create Task</button>
    </form>
</div>

<script>
    function addSubtaskField() {
        const container = document.getElementById('subtasks-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'subtasks[]';
        input.className = 'form-control mb-2';
        input.placeholder = `Subtask ${container.children.length + 2}`;
        container.appendChild(input);
    }
</script>

</body>
</html>
