<?php
class Database {
    private static $conn;

    // Private constructor to prevent multiple instances
    private function __construct() {}

    // Method to get the database connection
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                $host = "localhost";
                $dbname = "personnel_tasks"; // Change to your database name
                $username = "root"; // Database username
                $password = ""; // Database password, if any

                // Create a new PDO connection
                self::$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
?>
