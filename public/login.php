<?php

// Inclure le header
include('header.php');

// Rediriger les utilisateurs connectés vers la page d'accueil
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inclure la connexion à la base de données
    require_once '../config/database.php';

    $db = Database::getConnection(); // Récupérer l'instance PDO

    // Récupérer les données du formulaire
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérifier si les champs ne sont pas vides
    if (!empty($email) && !empty($password)) {
        // Requête pour récupérer l'utilisateur
        $query = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification des informations d'identification
        if ($user && password_verify($password, $user['password'])) {
            // Stocker l'ID utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php"); // Redirection vers la page d'accueil
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">
        <header class="text-center mb-4">
            <h1>Se connecter</h1>
        </header>

        <!-- Affichage des erreurs -->
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form action="login.php" method="POST" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="text-center mt-3">Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
