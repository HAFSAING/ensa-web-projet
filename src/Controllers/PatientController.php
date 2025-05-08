<?php
// src/Controllers/PatientController.php

require_once __DIR__ . '/../Models/PatientModel.php';
require_once __DIR__ . '/../Utils/Session.php';
require_once __DIR__ . '/../Utils/FileUploader.php';

class PatientController {
    private $patientModel;
    private $session;
    
    public function __construct() {
        $this->patientModel = new PatientModel();
        $this->session = new Session();
    }
    
    // Afficher le formulaire d'inscription
    public function showRegistrationForm() {
        $villes = $this->patientModel->getAllVilles();
        include __DIR__ . '/../../public/userInscrire.php';
    }
    
    // Traiter l'inscription
    public function register() {
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données
            $errors = $this->validateRegistrationData($_POST);
            
            if (!empty($errors)) {
                $this->session->set('errors', $errors);
                $this->session->set('old_input', $_POST);
                header('Location: userInscrire.php');
                exit;
            }
            
            // Vérifier si l'email existe déjà
            if ($this->patientModel->emailExists($_POST['email'])) {
                $this->session->set('errors', ['email' => 'Cet email est déjà utilisé']);
                $this->session->set('old_input', $_POST);
                header('Location: userInscrire.php');
                exit;
            }
            
            // Vérifier si le CIN existe déjà
            if ($this->patientModel->cinExists($_POST['cin'])) {
                $this->session->set('errors', ['cin' => 'Ce CIN est déjà utilisé']);
                $this->session->set('old_input', $_POST);
                header('Location: userInscrire.php');
                exit;
            }
            
            // Création du patient
            $result = $this->patientModel->createPatient($_POST);
            
            if ($result['success']) {
                // Envoyer l'email de confirmation
                $this->sendConfirmationEmail($_POST['email'], $result['token']);
                
                // Rediriger vers la page de confirmation
                $this->session->set('registration_success', true);
                header('Location: userConfirmation.php');
                exit;
            } else {
                $this->session->set('errors', ['general' => 'Erreur lors de l\'inscription: ' . $result['error']]);
                $this->session->set('old_input', $_POST);
                header('Location: userInscrire.php');
                exit;
            }
        }
    }
    
    // Valider les données d'inscription
    private function validateRegistrationData($data) {
        $errors = [];
        
        // Validation du nom
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
        }
        
        // Validation du prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire';
        }
        
        // Validation du CIN
        if (empty($data['cin'])) {
            $errors['cin'] = 'Le CIN est obligatoire';
        } elseif (!preg_match('/^[A-Z0-9]{4,20}$/', $data['cin'])) {
            $errors['cin'] = 'Format de CIN invalide';
        }
        
        // Validation de la date de naissance
        if (empty($data['date_naissance'])) {
            $errors['date_naissance'] = 'La date de naissance est obligatoire';
        } elseif (strtotime($data['date_naissance']) > time()) {
            $errors['date_naissance'] = 'La date de naissance ne peut pas être dans le futur';
        }
        
        // Validation du sexe
        if (empty($data['sexe']) || !in_array($data['sexe'], ['M', 'F'])) {
            $errors['sexe'] = 'Le sexe est obligatoire (M ou F)';
        }
        
        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide';
        }
        
        // Validation du téléphone
        if (empty($data['telephone'])) {
            $errors['telephone'] = 'Le téléphone est obligatoire';
        } elseif (!preg_match('/^[0-9+]{10,15}$/', $data['telephone'])) {
            $errors['telephone'] = 'Format de téléphone invalide';
        }
        
        // Validation du mot de passe
        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est obligatoire';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        // Validation de la confirmation du mot de passe
        if (empty($data['password_confirm'])) {
            $errors['password_confirm'] = 'La confirmation du mot de passe est obligatoire';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
        }
        
        // Validation de la question de sécurité
        if (empty($data['security_question'])) {
            $errors['security_question'] = 'La question de sécurité est obligatoire';
        }
        
        // Validation de la réponse à la question de sécurité
        if (empty($data['security_answer'])) {
            $errors['security_answer'] = 'La réponse à la question de sécurité est obligatoire';
        }
        
        return $errors;
    }
    
    // Envoyer l'email de confirmation
    private function sendConfirmationEmail($email, $token) {
        $to = $email;
        $subject = 'Confirmation de votre inscription - MediStatView';
        
        $confirmationLink = 'http://' . $_SERVER['HTTP_HOST'] . '/userConfirmation.php?token=' . $token;
        
        $message = "
        <html>
        <head>
            <title>Confirmation d'inscription</title>
        </head>
        <body>
            <h2>Confirmation de votre inscription</h2>
            <p>Merci de vous être inscrit sur MediStatView.</p>
            <p>Pour confirmer votre inscription, veuillez cliquer sur le lien ci-dessous :</p>
            <p><a href='$confirmationLink'>Confirmer mon inscription</a></p>
            <p>Si vous n'avez pas créé de compte, veuillez ignorer cet email.</p>
            <p>Cordialement,<br>L'équipe MediStatView</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: MediStatView <noreply@medistatview.com>' . "\r\n";
        
        mail($to, $subject, $message, $headers);
    }
    
    // Confirmer l'inscription
    public function confirmRegistration() {
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $patient = $this->patientModel->getPatientByToken($token);
            
            if ($patient) {
                $result = $this->patientModel->confirmRegistration($token);
                
                if ($result) {
                    $this->session->set('confirmation_success', true);
                    $this->session->set('patient_name', $patient['prenom'] . ' ' . $patient['nom']);
                } else {
                    $this->session->set('confirmation_error', 'Erreur lors de la confirmation. Veuillez réessayer.');
                }
            } else {
                $this->session->set('confirmation_error', 'Token de confirmation invalide ou déjà utilisé.');
            }
        }
        
        include __DIR__ . '/../../public/userConfirmation.php';
    }
    
    // Afficher le formulaire de connexion
    public function showLoginForm() {
        include __DIR__ . '/../../public/userConnecter.php';
    }
    
    // Traiter la connexion
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $this->session->set('login_error', 'Veuillez remplir tous les champs');
                header('Location: userConnecter.php');
                exit;
            }
            
            $result = $this->patientModel->verifyLogin($email, $password);
            
            if ($result['success']) {
                // Démarrer la session utilisateur
                $this->session->set('patient_id', $result['patient']['id']);
                $this->session->set('patient_name', $result['patient']['prenom'] . ' ' . $result['patient']['nom']);
                $this->session->set('patient_email', $result['patient']['email']);
                $this->session->set('is_patient', true);
                
                // Rediriger vers le tableau de bord
                header('Location: userRendezVous.php');
                exit;
            } else {
                $this->session->set('login_error', $result['error'] ?? 'Email ou mot de passe incorrect');
                header('Location: userConnecter.php');
                exit;
            }
        }
    }
    
    // Déconnexion
    public function logout() {
        $this->session->destroy();
        header('Location: index.php');
        exit;
    }
    
    // Afficher la page de rendez-vous
    public function showAppointmentsPage() {
        // Vérifier si l'utilisateur est connecté
        if (!$this->session->get('is_patient')) {
            header('Location: userConnecter.php');
            exit;
        }
        
        $patientId = $this->session->get('patient_id');
        
        // Ici, vous devrez ajouter la logique pour récupérer les rendez-vous du patient
        
        include __DIR__ . '/../../public/userRendezVous.php';
    }
    
    // Récupérer la question de sécurité pour un email
    public function getSecurityQuestion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
            $email = $_POST['email'];
            $patient = $this->patientModel->getPatientByEmail($email);
            
            if ($patient) {
                $response = ['success' => true, 'question' => $patient['security_question']];
            } else {
                $response = ['success' => false, 'error' => 'Email non trouvé'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }
    
    // Réinitialiser le mot de passe avec la question de sécurité
    public function resetPasswordWithSecurityQuestion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $security_answer = $_POST['security_answer'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $errors = [];
            
            if (empty($email)) {
                $errors['email'] = 'L\'email est obligatoire';
            }
            
            if (empty($security_answer)) {
                $errors['security_answer'] = 'La réponse à la question de sécurité est obligatoire';
            }
            
            if (empty($new_password)) {
                $errors['new_password'] = 'Le nouveau mot de passe est obligatoire';
            } elseif (strlen($new_password) < 8) {
                $errors['new_password'] = 'Le mot de passe doit contenir au moins 8 caractères';
            }
            
            if ($new_password !== $confirm_password) {
                $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
            }
            
            if (!empty($errors)) {
                $response = ['success' => false, 'errors' => $errors];
            } else {
                $result = $this->patientModel->resetPasswordWithSecurityQuestion($email, $security_answer, $new_password);
                $response = $result;
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }
}