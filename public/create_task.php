<?php
session_start();
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
    $priority = $_POST['priority'] ?? 'low';  // Default to 'low' if not provided

    // Validate the form data (basic validation)
    if (empty($title) || empty($status)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Insert the new task into the database
        $db = Database::getConnection();  // Get the PDO instance using the Database class

        $query = "INSERT INTO tasks (user_id, title, description, status, due_date,priority) VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $db->prepare($query);  // Use $db instead of $pdo
        $stmt->execute([$_SESSION['user_id'], $title, $description, $status, $due_date,$priority]);
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">
        <header class="text-center mb-4">
            <h1>Create New Task</h1>
        </header>

        <!-- Back to Tasks link -->
        <div class="mb-4">
            <a href="tasks.php" class="btn btn-secondary">Back to Tasks</a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
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

            <button type="submit" class="btn btn-primary">Create Task</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
