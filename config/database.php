<?php
// Configuration de la base de données
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'medistatview'); 
define('DB_USER', 'root');
define('DB_PASS', ''); // Mettez à jour avec le mot de passe correct si nécessaire

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

        return $pdo;
    } catch (PDOException $e) {
        // Journaliser l'erreur avec des détails pour le débogage
        error_log("Erreur de connexion à la base de données : " . $e->getMessage() . " | Code: " . $e->getCode());
        
        // En environnement de développement, afficher plus de détails (à commenter en production)
        // echo "Erreur de connexion : " . $e->getMessage(); exit;
        
        // Message générique pour l'utilisateur
        throw new Exception("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
    }
}
?>  