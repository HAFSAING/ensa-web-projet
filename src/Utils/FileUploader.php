<?php
namespace Src\Utils;

class FileUploader {
    /**
     * Télécharge un fichier
     * 
     * @param array $file L'information du fichier ($_FILES['key'])
     * @param string $destinationDir Le dossier de destination
     * @param array $allowedExtensions Les extensions autorisées
     * @param int $maxSize La taille maximale en octets
     * @return array Résultat avec status, message et filename
     */
    public static function upload($file, $destinationDir, $allowedExtensions = [], $maxSize = 2097152) {
        $result = [
            'status' => false,
            'message' => '',
            'filename' => null
        ];
        
        // Vérifier si le fichier existe et s'il n'y a pas d'erreur
        if (!isset($file) || $file['error'] !== 0) {
            $result['message'] = 'Erreur lors du téléchargement du fichier: code ' . ($file['error'] ?? 'inconnu');
            return $result;
        }
        
        // Récupérer les informations du fichier
        $filename = $file['name'];
        $filesize = $file['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Vérifier l'extension
        if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions)) {
            $result['message'] = 'Extension de fichier non autorisée. Extensions acceptées: ' . implode(', ', $allowedExtensions);
            return $result;
        }
        
        // Vérifier la taille
        if ($filesize > $maxSize) {
            $result['message'] = 'La taille du fichier ne doit pas dépasser ' . self::formatBytes($maxSize);
            return $result;
        }
        
        // Créer un nom de fichier unique
        $newFilename = uniqid('file_') . '.' . $ext;
        
        // S'assurer que le répertoire de destination existe
        if (!file_exists($destinationDir)) {
            if (!mkdir($destinationDir, 0777, true)) {
                $result['message'] = 'Impossible de créer le répertoire de destination';
                return $result;
            }
        }
        
        // Chemin complet du fichier
        $destination = $destinationDir . '/' . $newFilename;
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $result['message'] = 'Échec du téléchargement du fichier';
            return $result;
        }
        
        // Succès
        $result['status'] = true;
        $result['message'] = 'Fichier téléchargé avec succès';
        $result['filename'] = $destination;
        
        return $result;
    }
    
    /**
     * Formate les octets en une taille lisible par l'homme
     * 
     * @param int $bytes Nombre d'octets
     * @param int $precision Précision
     * @return string Taille formatée
     */
    private static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}