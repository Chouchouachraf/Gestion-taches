<?php
include('header.php');
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the task ID is passed
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Ensure that the task belongs to the logged-in user
    $db = Database::getConnection(); // Get the database connection
    $query = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query); // Use $db instead of $pdo
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if ($task) {
        // Task exists, delete it
        $deleteQuery = "DELETE FROM tasks WHERE id = ?";
        $deleteStmt = $db->prepare($deleteQuery); // Use $db instead of $pdo
        $deleteStmt->execute([$task_id]);

        header("Location: tasks.php");
        exit();
    } else {
        echo "Error: Task not found or you do not have permission to delete it.";
    }
} else {
    echo "Error: Task ID not specified.";
}
?>
