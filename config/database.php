<?php
// Configuration de la base de données
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'medistatview'); 
define('DB_USER', 'root'); 
define('DB_PASS', '2004'); 

// Connexion à la base de données
function connectDB() {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données: ' . $e->getMessage());
    }
}
?>