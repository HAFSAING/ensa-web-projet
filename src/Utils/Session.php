<?php
namespace Src\Utils;

class Session {
    /**
     * Démarre la session si elle n'est pas déjà démarrée
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Définit une valeur de session
     * 
     * @param string $key Clé de la session
     * @param mixed $value Valeur à stocker
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de session
     * 
     * @param string $key Clé de la session
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed La valeur de session ou la valeur par défaut
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe dans la session
     * 
     * @param string $key Clé à vérifier
     * @return bool True si la clé existe, false sinon
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Supprime une valeur de session
     * 
     * @param string $key Clé à supprimer
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Définit un message flash (qui ne persiste que jusqu'à la prochaine requête)
     * 
     * @param string $type Type de message (success, error, warning, info)
     * @param string $message Le message à afficher
     */
    public static function setFlash($type, $message) {
        self::start();
        $_SESSION['flash_messages'][$type] = $message;
    }

    /**
     * Récupère les messages flash et les supprime
     * 
     * @return array Les messages flash
     */
    public static function getFlash() {
        self::start();
        $flash = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $flash;
    }

    /**
     * Vérifie si des messages flash existent
     * 
     * @return bool True si des messages flash existent, false sinon
     */
    public static function hasFlash() {
        self::start();
        return isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages']);
    }

    /**
     * Détruit la session
     */
    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = [];
    }
}