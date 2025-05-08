<?php
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'medistatview'); 
define('DB_USER', 'root');
define('DB_PASS', ''); 

// Fonction pour établir la connexion à la base de données
function getDatabaseConnection() {
    try {
        // DSN (Data Source Name) pour MySQL
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

        // Options de connexion PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Établir la connexion
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        // Retourner la connexion PDO
        return $pdo;
    } catch (PDOException $e) {
        // En cas d'erreur de connexion, afficher un message d'erreur
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Retourner la connexion PDO
return getDatabaseConnection();
?>