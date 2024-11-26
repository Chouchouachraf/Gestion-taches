<?php
include('header.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$db = Database::getConnection();

// Handle form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'add') {
            // Add new category
            $category_name = trim($_POST['category_name']);
            if (!empty($category_name)) {
                $query = "INSERT INTO categories (user_id, name) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$user_id, $category_name]);
                $success_message = "Category added successfully!";
            } else {
                $error_message = "Category name cannot be empty.";
            }
        } elseif ($_POST['action'] === 'edit') {
            // Edit existing category
            $category_id = $_POST['category_id'];
            $new_name = trim($_POST['new_name']);
            if (!empty($new_name)) {
                $query = "UPDATE categories SET name = ? WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$new_name, $category_id, $user_id]);
                $success_message = "Category updated successfully!";
            } else {
                $error_message = "Category name cannot be empty.";
            }
        } elseif ($_POST['action'] === 'delete') {
            // Delete category
            $category_id = $_POST['category_id'];
            $query = "DELETE FROM categories WHERE id = ? AND user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$category_id, $user_id]);
            $success_message = "Category deleted successfully!";
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Fetch existing categories
$query = "SELECT * FROM categories WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <header class="mb-4">
            <h1 class="text-center">Manage Categories</h1>
            <div class="d-flex justify-content-end">
                <a href="home.php" class="btn btn-secondary">Home</a>
                <a href="logout.php" class="btn btn-danger ms-2">Logout</a>
            </div>
        </header>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php elseif (isset($error_message)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Form to Add New Category -->
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>

        <!-- Display Existing Categories -->
        <h2 class="mt-4">Your Categories</h2>
        <?php if (empty($categories)) : ?>
            <p>No categories found. Add a new category to get started!</p>
        <?php else : ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td>
                                <!-- Edit Form -->
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                    <input type="text" name="new_name" class="form-control d-inline w-auto" placeholder="New name" required>
                                    <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                </form>

                                <!-- Delete Form with Confirmation -->
                                <form method="POST" action="" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
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
