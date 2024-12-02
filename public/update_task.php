<?php
include('header.php');
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if task ID is passed
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Fetch task details
    $db = Database::getConnection();  // Get database connection
    $query = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();


    if (!$task) {
        echo "Error: Task not found or you do not have permission to edit it.";
        exit();
    }

    // Handle form submission for updating the task
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $due_date = $_POST['due_date'];
        $priority = $_POST['priority'];

        // Update the task in the database
        $updateQuery = "UPDATE tasks SET title = ?, description = ?, status = ?, due_date = ?, priority = ? WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$title, $description, $status, $due_date, $priority, $task_id]);

        header("Location: tasks.php");
        exit();
    }
} else {
    echo "Error: Task ID not specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">
        <header class="text-center mb-4">
            <h1>Edit Task</h1>
        </header>

        <div class="mb-4">
            <a href="tasks.php" class="btn btn-secondary">Back to Tasks</a>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="pending" <?php if ($task['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="in_progress" <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                    <option value="completed" <?php if ($task['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="low" <?php if ($task['priority'] == 'low') echo 'selected'; ?>>Low</option>
                    <option value="medium" <?php if ($task['priority'] == 'medium') echo 'selected'; ?>>Medium</option>
                    <option value="high" <?php if ($task['priority'] == 'high') echo 'selected'; ?>>High</option>
                </select>
            </div>


            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="<?php echo htmlspecialchars($task['due_date']); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
