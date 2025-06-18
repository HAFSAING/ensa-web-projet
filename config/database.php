<?php

define('DB_HOST', 'localhost'); 
define('DB_NAME', 'medistatview'); 
define('DB_USER', 'root');
define('DB_PASS', ''); 

function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

        // Options de connexion PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion à la base de données : " . $e->getMessage() . " | Code: " . $e->getCode());
        throw new Exception("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
    }
}
?>  