<?php
namespace Src\Models;

class MedecinModel {
    private $db;


    public function __construct() {
        $this->db = new \PDO(
            "mysql:host=localhost;dbname=medistatview;charset=utf8mb4", 
            "root",
            "",     
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }
    /**
     * Vérifie si un email existe déjà dans la base de données
     * 
     * @param string $email L'email à vérifier
     * @return bool True si l'email existe, false sinon
     */
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM medecins WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un CIN existe déjà dans la base de données
     * 
     * @param string $cin Le CIN à vérifier
     * @return bool True si le CIN existe, false sinon
     */
    public function cinExists($cin) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM medecins WHERE cin = ?");
        $stmt->execute([$cin]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un numéro INPE existe déjà dans la base de données
     * 
     * @param string $numInpe Le numéro INPE à vérifier
     * @return bool True si le numéro INPE existe, false sinon
     */
    public function inpeExists($numInpe) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM medecins WHERE num_inpe = ?");
        $stmt->execute([$numInpe]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un numéro d'ordre existe déjà dans la base de données
     * 
     * @param string $numOrdre Le numéro d'ordre à vérifier
     * @return bool True si le numéro d'ordre existe, false sinon
     */
    public function ordreExists($numOrdre) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM medecins WHERE num_ordre = ?");
        $stmt->execute([$numOrdre]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtient ou crée une spécialité et retourne son ID
     * 
     * @param string $specialite Nom de la spécialité
     * @return int ID de la spécialité
     */
    public function getOrCreateSpecialite($specialite) {
        // Vérifier si la spécialité existe déjà
        $stmt = $this->db->prepare("SELECT id FROM specialites WHERE nom = ?");
        $stmt->execute([$specialite]);
        $specialiteId = $stmt->fetchColumn();
        
        // Si elle n'existe pas, la créer
        if (!$specialiteId) {
            $stmt = $this->db->prepare("INSERT INTO specialites (nom) VALUES (?)");
            $stmt->execute([$specialite]);
            $specialiteId = $this->db->lastInsertId();
        }
        
        return $specialiteId;
    }

    /**
     * Obtient ou crée une ville et retourne son ID
     * 
     * @param string $ville Nom de la ville
     * @param string $codePostal Code postal de la ville
     * @return int ID de la ville
     */
    public function getOrCreateVille($ville, $codePostal = '') {
        // Vérifier si la ville existe déjà
        $stmt = $this->db->prepare("SELECT id FROM villes WHERE nom = ?");
        $stmt->execute([$ville]);
        $villeId = $stmt->fetchColumn();
        
        // Si elle n'existe pas, la créer
        if (!$villeId) {
            $stmt = $this->db->prepare("INSERT INTO villes (nom, code_postal) VALUES (?, ?)");
            $stmt->execute([$ville, $codePostal]);
            $villeId = $this->db->lastInsertId();
        }
        
        return $villeId;
    }

    /**
     * Inscrit un nouveau médecin
     * 
     * @param array $data Les données du médecin
     * @return int|bool L'ID du médecin créé ou false en cas d'erreur
     */
    public function register($data) {
        try {
            // Commencer une transaction
            $this->db->beginTransaction();
            
            // 1. Récupérer/créer la spécialité
            $specialiteId = $this->getOrCreateSpecialite($data['specialite']);
            
            // 2. Récupérer/créer la ville
            $villeId = $this->getOrCreateVille($data['ville'], $data['code_postal'] ?? '');
            
            // 3. Convertir la civilité au format de l'énumération
            $civilite = ($data['civilite'] === 'dr') ? 'Dr.' : 'Pr.';
            
            // 4. Hacher le mot de passe
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // 5. Insérer le médecin
            $stmt = $this->db->prepare("
                INSERT INTO medecins (
                    civilite, nom, prenom, cin, date_naissance, specialite_id, 
                    num_inpe, num_ordre, carte_professionnelle, adresse_cabinet, 
                    ville_id, telephone_cabinet, telephone_mobile, email, password, statut
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            
            $stmt->execute([
                $civilite,
                $data['nom'],
                $data['prenom'],
                $data['cin'],
                $data['date_naissance'],
                $specialiteId,
                $data['num_inpe'],
                $data['num_ordre'],
                $data['carte_professionnelle_path'],
                $data['adresse_cabinet'],
                $villeId,
                $data['telephone_cabinet'],
                $data['telephone_mobile'],
                $data['email'],
                $passwordHash
            ]);
            
            $medecinId = $this->db->lastInsertId();
            
            // 6. Créer une notification pour l'administrateur
            $notificationMessage = "Nouvelle demande d'inscription médecin: $civilite " . $data['nom'] . " " . $data['prenom'];
            
            $stmt = $this->db->prepare("
                INSERT INTO notifications (
                    utilisateur_id, type_utilisateur, titre, message, lien
                ) VALUES (1, 'medecin', 'Nouvelle inscription', ?, '/admin/medecins/verification')
            ");
            $stmt->execute([$notificationMessage]);
            
            // Valider la transaction
            $this->db->commit();
            
            return $medecinId;
            
        } catch (\PDOException $e) {
            // En cas d'erreur, annuler la transaction
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}