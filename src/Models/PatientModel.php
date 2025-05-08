<?php
namespace Src\Models;

class PatientModel {
    private $db;
    
    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Erreur de connexion à la base de données: " . $this->db->connect_error);
        }
    }
    
    public function __destruct() {
        $this->db->close();
    }
    
    // Créer un nouveau patient
    public function createPatient($data) {
        // Hashage du mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Génération d'un token de confirmation
        $token = bin2hex(random_bytes(32));
        
        $stmt = $this->db->prepare("INSERT INTO patients (nom, prenom, cin, date_naissance, sexe, email, 
                                    telephone, adresse, ville_id, mutuelle, password, security_question, 
                                    security_answer, remember_token, statut) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')");
        
        $stmt->bind_param("ssssssssisssss", 
            $data['nom'], 
            $data['prenom'], 
            $data['cin'], 
            $data['date_naissance'],
            $data['sexe'],
            $data['email'],
            $data['telephone'],
            $data['adresse'],
            $data['ville_id'],
            $data['mutuelle'],
            $hashedPassword,
            $data['security_question'],
            $data['security_answer'],
            $token
        );
        
        if ($stmt->execute()) {
            $patient_id = $this->db->insert_id;
            $stmt->close();
            return ['success' => true, 'patient_id' => $patient_id, 'token' => $token];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $error];
        }
    }
    
    // Vérifier si l'email existe déjà
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    
    // Vérifier si le CIN existe déjà
    public function cinExists($cin) {
        $stmt = $this->db->prepare("SELECT id FROM patients WHERE cin = ?");
        $stmt->bind_param("s", $cin);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    
    // Confirmer l'inscription d'un patient
    public function confirmRegistration($token) {
        $stmt = $this->db->prepare("UPDATE patients SET statut = 'actif', email_verified_at = CURRENT_TIMESTAMP WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected > 0;
    }
    
    // Récupérer un patient par son token
    public function getPatientByToken($token) {
        $stmt = $this->db->prepare("SELECT id, nom, prenom, email FROM patients WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        $stmt->close();
        
        return $patient;
    }
    
    // Récupérer un patient par son email
    public function getPatientByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        $stmt->close();
        
        return $patient;
    }
    
    // Mettre à jour les informations d'un patient
    public function updatePatient($id, $data) {
        $updateFields = [];
        $bindTypes = "";
        $bindValues = [];
        
        // Construction dynamique de la requête de mise à jour
        foreach ($data as $field => $value) {
            if ($field !== 'id') {
                $updateFields[] = "$field = ?";
                $bindTypes .= "s";
                $bindValues[] = $value;
            }
        }
        
        // Ajout de l'ID pour la clause WHERE
        $bindTypes .= "i";
        $bindValues[] = $id;
        
        $sql = "UPDATE patients SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        // Utilisation de call_user_func_array pour passer un tableau de références
        $bindReferences = [];
        $bindReferences[] = &$bindTypes;
        foreach ($bindValues as $key => $value) {
            $bindReferences[] = &$bindValues[$key];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $bindReferences);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    // Vérifier les identifiants de connexion
    public function verifyLogin($email, $password) {
        $patient = $this->getPatientByEmail($email);
        
        if (!$patient) {
            return false;
        }
        
        if ($patient['statut'] !== 'actif') {
            return ['success' => false, 'error' => 'Compte non activé'];
        }
        
        if (password_verify($password, $patient['password'])) {
            // Mettre à jour la date de dernière connexion
            $this->updateLastLogin($patient['id']);
            return ['success' => true, 'patient' => $patient];
        }
        
        return ['success' => false, 'error' => 'Mot de passe incorrect'];
    }
    
    // Mettre à jour la date de dernière connexion
    private function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE patients SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Récupérer toutes les villes
    public function getAllVilles() {
        $stmt = $this->db->prepare("SELECT id, nom, code_postal FROM villes ORDER BY nom");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $villes = [];
        while ($row = $result->fetch_assoc()) {
            $villes[] = $row;
        }
        
        $stmt->close();
        return $villes;
    }
    
    // Réinitialiser le mot de passe avec la question de sécurité
    public function resetPasswordWithSecurityQuestion($email, $security_answer, $new_password) {
        $patient = $this->getPatientByEmail($email);
        
        if (!$patient) {
            return ['success' => false, 'error' => 'Email non trouvé'];
        }
        
        if ($patient['security_answer'] !== $security_answer) {
            return ['success' => false, 'error' => 'Réponse de sécurité incorrecte'];
        }
        
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("UPDATE patients SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $result = $stmt->execute();
        $stmt->close();
        
        return ['success' => $result];
    }
}