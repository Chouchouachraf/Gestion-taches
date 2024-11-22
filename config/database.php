<?php
// config/database.php

class Database {
    private static $host = 'localhost';
    private static $dbname = 'personnel_tasks';
    private static $username = 'root';  // Utilisateur de la base de données (ajustez si nécessaire)
    private static $password = '';  // Mot de passe de l'utilisateur (ajustez si nécessaire)
    private static $connection = null;

    // Méthode pour obtenir une connexion à la base de données
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                // Crée une nouvelle connexion à la base de données
                self::$connection = new PDO(
                    'mysql:host=' . self::$host . ';dbname=' . self::$dbname,
                    self::$username,
                    self::$password
                );
                // Définir le mode d'erreur PDO
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
                die();
            }
        }
        return self::$connection;
    }
}
?>
