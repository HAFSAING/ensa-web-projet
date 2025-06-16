<?php
namespace Src\Controllers;

use Src\Models\MedecinModel;
use Src\Utils\FileUploader;
use Src\Utils\Session;

class MedecinController {
    private $medecinModel;
    
    /**
     * Constructeur du contrôleur Médecin
     */
    public function __construct() {
        $this->medecinModel = new MedecinModel();
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function showRegistrationForm() {
        // Inclure la vue du formulaire d'inscription
        require_once __DIR__ . '/../../public/docInscrire.php';
    }
    
    /**
     * Traite l'inscription d'un médecin
     * 
     * @param array $data Les données du formulaire
     * @param array $files Les fichiers uploadés
     * @return bool True si l'inscription est réussie, false sinon
     */
    public function register($data, $files) {
        // Initialiser le tableau d'erreurs
        $errors = [];
        
        // Vérifier les champs obligatoires
        $requiredFields = [
            'civilite', 'specialite', 'nom', 'prenom', 'cin', 'date_naissance', 
            'num_inpe', 'num_ordre', 'adresse_cabinet', 'ville', 
            'telephone_cabinet', 'telephone_mobile', 'email', 'password', 'confirm_password'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ $field est requis.";
            }
        }
        
        // Vérifier que les mots de passe correspondent
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        
        // Vérifier le format de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'adresse email invalide.";
        }
        
        // Vérifier que l'email n'est pas déjà utilisé
        if ($this->medecinModel->emailExists($data['email'])) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }
        
        // Vérifier que le CIN n'est pas déjà utilisé
        if ($this->medecinModel->cinExists($data['cin'])) {
            $errors[] = "Ce numéro de CIN est déjà utilisé.";
        }
        
        // Vérifier que le numéro INPE n'est pas déjà utilisé
        if ($this->medecinModel->inpeExists($data['num_inpe'])) {
            $errors[] = "Ce numéro INPE est déjà utilisé.";
        }
        
        // Vérifier que le numéro d'ordre n'est pas déjà utilisé
        if ($this->medecinModel->ordreExists($data['num_ordre'])) {
            $errors[] = "Ce numéro d'ordre est déjà utilisé.";
        }
        
        // Gérer l'upload de la carte professionnelle
        $carteProPath = null;
        if (isset($files['carte_professionnelle'])) {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $uploadDir = __DIR__ . '/../../public/uploads/cartes_pro';
            
            $uploadResult = FileUploader::upload(
                $files['carte_professionnelle'],
                $uploadDir,
                $allowedExtensions,
                2097152 // 2MB
            );
            
            if (!$uploadResult['status']) {
                $errors[] = $uploadResult['message'];
            } else {
                $carteProPath = $uploadResult['filename'];
            }
        } else {
            $errors[] = "Veuillez télécharger votre carte professionnelle.";
        }
        
        // S'il y a des erreurs, les stocker dans la session et retourner false
        if (!empty($errors)) {
            Session::set('inscription_errors', $errors);
            Session::set('form_data', $data);
            return false;
        }
        
        // Préparer les données pour l'inscription
        $registrationData = [
            'civilite' => $data['civilite'],
            'specialite' => $data['specialite'],
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'cin' => $data['cin'],
            'date_naissance' => $data['date_naissance'],
            'num_inpe' => $data['num_inpe'],
            'num_ordre' => $data['num_ordre'],
            'carte_professionnelle_path' => $carteProPath,
            'adresse_cabinet' => $data['adresse_cabinet'],
            'ville' => $data['ville'],
            'code_postal' => $data['code_postal'] ?? '',
            'telephone_cabinet' => $data['telephone_cabinet'],
            'telephone_mobile' => $data['telephone_mobile'],
            'email' => $data['email'],
            'password' => $data['password']
        ];
        
        try {
            // Inscrire le médecin
            $medecinId = $this->medecinModel->register($registrationData);
            
            if ($medecinId) {
                // Définir un message de succès
                Session::set('inscription_success', true);
                return true;
            } else {
                $errors[] = "Erreur lors de l'inscription. Veuillez réessayer.";
                Session::set('inscription_errors', $errors);
                return false;
            }
        } catch (\Exception $e) {
            $errors[] = "Erreur lors de l'inscription: " . $e->getMessage();
            Session::set('inscription_errors', $errors);
            return false;
        }
    }

    public function showConfirmation() {
        // Inclure la vue de confirmation
        require_once __DIR__ . '/../../public/docConfirmation.php';
    }
}