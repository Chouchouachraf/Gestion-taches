<?php
// Inclure la connexion à la base de données
require_once '../config/database.php';

// Initialiser la connexion à la base de données
$db = Database::getConnection();

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations envoyées par le formulaire
    $email = $_POST['email'] ?? ''; // Utilise $_POST['email']
    $password = $_POST['password'] ?? ''; // Utilise $_POST['password']

    // Vérifier si l'email et le mot de passe sont renseignés
    if (!empty($email) && !empty($password)) {
        // Préparer la requête pour vérifier si l'utilisateur existe dans la base de données
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Vérifier si un utilisateur a été trouvé
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier si le mot de passe est correct
            if (password_verify($password, $user['password'])) {
                // Connecter l'utilisateur (enregistrer l'ID de l'utilisateur dans la session, par exemple)
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Rediriger vers la page d'accueil ou une autre page après la connexion
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Identifiants invalides.";
            }
        } else {
            $error_message = "Identifiants invalides.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h1>Se connecter</h1>

    <?php if (isset($error_message)): ?>
        <div class="error"><?= $error_message ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
