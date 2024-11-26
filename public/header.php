<?php
// Start the session at the beginning of the page
session_start();

// Check if the theme is already set in the session
if (isset($_POST['theme'])) {
    $_SESSION['theme'] = $_POST['theme'];
}

// Set default theme if not set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light'; // Default theme
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="light-theme.css"> 
    <link rel="stylesheet" href="dark-theme.css">
    <!-- Custom Theme CSS -->
    <link id="theme-style" href="<?php echo $_SESSION['theme'] == 'dark' ? 'dark-theme.css' : 'light-theme.css'; ?>" rel="stylesheet">
</head>
<body class="<?= $_SESSION['theme'] == 'dark' ? 'dark-theme' : 'light-theme' ?>">

<!-- Theme Toggle Button -->
<button id="theme-toggle" class="btn btn-primary">
    <?= ($_SESSION['theme'] == 'dark') ? 'Switch to Light Mode' : 'Switch to Dark Mode' ?>
</button>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const themeToggleButton = document.querySelector("#theme-toggle");

        // Detect the current theme
        themeToggleButton.addEventListener("click", function () {
            const currentTheme = document.body.classList.contains("dark-theme") ? "dark" : "light";
            const newTheme = (currentTheme === "dark") ? "light" : "dark";

            // Toggle the body class for the theme
            document.body.classList.toggle("dark-theme", newTheme === "dark");
            document.body.classList.toggle("light-theme", newTheme === "light");

            // Update the button text
            themeToggleButton.textContent = (newTheme === "dark") ? "Switch to Light Mode" : "Switch to Dark Mode";

            // Send the theme change to the server using fetch (AJAX) to update the session
            fetch("update_theme.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ theme: newTheme })
            }).then(response => {
                if (!response.ok) {
                    console.error("Error updating theme.");
                }
            });
        });
    });
</script>
</body>
</html>
<!-- Your content here -->
