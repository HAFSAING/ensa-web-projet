<?php
session_start();

// Inclure la connexion à la base de données
require_once __DIR__ . '/../config/database.php';

$conn = getDatabaseConnection(); 


$conn->set_charset("utf8");

// Initialiser la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$alertType = '';


function getUserData($conn, $user_id) {
    $sql = "SELECT u.*, p.cin, p.date_naissance, p.sexe, p.adresse, p.ville_id, p.mutuelle_id, 
                   m.nom as mutuelle_nom, v.nom as ville_nom
            FROM utilisateurs u
            JOIN patients p ON u.id = p.utilisateur_id
            LEFT JOIN mutuelles m ON p.mutuelle_id = m.id
            LEFT JOIN villes v ON p.ville_id = v.id
            WHERE u.id = :user_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer la liste des villes
function getVilles($conn) {
    $villes = [];
    $sql = "SELECT id, nom FROM villes ORDER BY nom";
    $stmt = $conn->query($sql);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMutuelles($conn) {
    $mutuelles = [];
    $sql = "SELECT id, nom, code FROM mutuelles ORDER BY nom";
    $stmt = $conn->query($sql);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les statistiques du patient
function getPatientStats($conn, $user_id) {
    // Nombre de consultations
    $sql_consultations = "SELECT COUNT(*) as total FROM consultations WHERE patient_id = ?";
    $stmt = $conn->prepare($sql_consultations);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $consultations = $result->fetch_assoc()['total'];
    
    // Nombre de médecins consultés (uniques)
    $sql_medecins = "SELECT COUNT(DISTINCT medecin_id) as total FROM consultations WHERE patient_id = ?";
    $stmt = $conn->prepare($sql_medecins);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $medecins = $result->fetch_assoc()['total'];
    
    // Nombre de documents
    $sql_documents = "SELECT COUNT(*) as total FROM documents WHERE patient_id = ?";
    $stmt = $conn->prepare($sql_documents);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $documents = $result->fetch_assoc()['total'];
    
    return [
        'consultations' => $consultations,
        'medecins' => $medecins,
        'documents' => $documents
    ];
}

// Traiter la mise à jour des informations personnelles
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    // Récupérer les données du formulaire
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $username = $_POST['username'];
    $date_naissance = $_POST['date_naissance'];
    $sexe = $_POST['sexe'];
    $adresse = $_POST['adresse'];
    $ville_id = $_POST['ville'];
    $mutuelle_id = $_POST['mutuelle'];
    
    // Vérifier si l'email ou le nom d'utilisateur est déjà utilisé par un autre utilisateur
    $check_sql = "SELECT id FROM utilisateurs WHERE (email = ? OR username = ?) AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssi", $email, $username, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $message = "Cet email ou nom d'utilisateur est déjà utilisé par un autre utilisateur.";
        $alertType = "danger";
    } else {
        // Commencer une transaction pour garantir l'intégrité des données
        $conn->begin_transaction();
        
        try {
            // Mettre à jour les informations dans la table utilisateurs
            $update_user_sql = "UPDATE utilisateurs SET prenom = ?, nom = ?, email = ?, telephone = ?, username = ? WHERE id = ?";
            $update_user_stmt = $conn->prepare($update_user_sql);
            $update_user_stmt->bind_param("sssssi", $prenom, $nom, $email, $telephone, $username, $user_id);
            $update_user_stmt->execute();
            
            // Mettre à jour les informations dans la table patients
            $update_patient_sql = "UPDATE patients SET date_naissance = ?, sexe = ?, adresse = ?, ville_id = ?, mutuelle_id = ? WHERE utilisateur_id = ?";
            $update_patient_stmt = $conn->prepare($update_patient_sql);
            $update_patient_stmt->bind_param("sssiii", $date_naissance, $sexe, $adresse, $ville_id, $mutuelle_id, $user_id);
            $update_patient_stmt->execute();
            
            // Valider la transaction
            $conn->commit();
            
            $message = "Votre profil a été mis à jour avec succès!";
            $alertType = "success";
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $conn->rollback();
            $message = "Erreur lors de la mise à jour du profil: " . $e->getMessage();
            $alertType = "danger";
        }
    }
}

// Traiter la mise à jour du mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Vérifier si le mot de passe actuel est correct
    $check_pwd_sql = "SELECT password FROM utilisateurs WHERE id = ?";
    $check_pwd_stmt = $conn->prepare($check_pwd_sql);
    $check_pwd_stmt->bind_param("i", $user_id);
    $check_pwd_stmt->execute();
    $result = $check_pwd_stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!password_verify($current_password, $user['password'])) {
        $message = "Le mot de passe actuel est incorrect.";
        $alertType = "danger";
    } else if ($new_password !== $confirm_password) {
        $message = "Les nouveaux mots de passe ne correspondent pas.";
        $alertType = "danger";
    } else {
        // Hasher le nouveau mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe
        $update_pwd_sql = "UPDATE utilisateurs SET password = ? WHERE id = ?";
        $update_pwd_stmt = $conn->prepare($update_pwd_sql);
        $update_pwd_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_pwd_stmt->execute()) {
            $message = "Votre mot de passe a été mis à jour avec succès!";
            $alertType = "success";
        } else {
            $message = "Erreur lors de la mise à jour du mot de passe.";
            $alertType = "danger";
        }
    }
}

// Traiter la mise à jour de la question de sécurité
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_security') {
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];
    
    $update_security_sql = "UPDATE patients SET question_securite = ?, reponse_securite = ? WHERE utilisateur_id = ?";
    $update_security_stmt = $conn->prepare($update_security_sql);
    $update_security_stmt->bind_param("ssi", $security_question, $security_answer, $user_id);
    
    if ($update_security_stmt->execute()) {
        $message = "Votre question de sécurité a été mise à jour avec succès!";
        $alertType = "success";
    } else {
        $message = "Erreur lors de la mise à jour de la question de sécurité.";
        $alertType = "danger";
    }
}

// Traiter la mise à jour des préférences de notification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_preferences') {
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $data_sharing = isset($_POST['data_sharing']) ? 1 : 0;
    
    $update_pref_sql = "UPDATE preferences_patients SET 
                         email_notifications = ?, 
                         sms_notifications = ?, 
                         newsletter = ?, 
                         partage_donnees = ? 
                         WHERE patient_id = ?";
    
    $update_pref_stmt = $conn->prepare($update_pref_sql);
    $update_pref_stmt->bind_param("iiiii", $email_notifications, $sms_notifications, $newsletter, $data_sharing, $user_id);
    
    if ($update_pref_stmt->execute()) {
        $message = "Vos préférences ont été mises à jour avec succès!";
        $alertType = "success";
    } else {
        // Vérifier si l'entrée existe déjà
        $check_pref_sql = "SELECT * FROM preferences_patients WHERE patient_id = ?";
        $check_pref_stmt = $conn->prepare($check_pref_sql);
        $check_pref_stmt->bind_param("i", $user_id);
        $check_pref_stmt->execute();
        $check_pref_result = $check_pref_stmt->get_result();
        
        if ($check_pref_result->num_rows == 0) {
            // Insérer une nouvelle entrée
            $insert_pref_sql = "INSERT INTO preferences_patients 
                               (patient_id, email_notifications, sms_notifications, newsletter, partage_donnees) 
                               VALUES (?, ?, ?, ?, ?)";
            $insert_pref_stmt = $conn->prepare($insert_pref_sql);
            $insert_pref_stmt->bind_param("iiiii", $user_id, $email_notifications, $sms_notifications, $newsletter, $data_sharing);
            
            if ($insert_pref_stmt->execute()) {
                $message = "Vos préférences ont été enregistrées avec succès!";
                $alertType = "success";
            } else {
                $message = "Erreur lors de l'enregistrement des préférences.";
                $alertType = "danger";
            }
        } else {
            $message = "Erreur lors de la mise à jour des préférences.";
            $alertType = "danger";
        }
    }
}

// Récupérer les données de l'utilisateur
$userData = getUserData($conn, $user_id);
$villes = getVilles($conn);
$mutuelles = getMutuelles($conn);
$stats = getPatientStats($conn, $user_id);

// Récupérer les préférences de notification
$preferences_sql = "SELECT email_notifications, sms_notifications, newsletter, partage_donnees 
                   FROM preferences_patients WHERE patient_id = ?";
$preferences_stmt = $conn->prepare($preferences_sql);
$preferences_stmt->bind_param("i", $user_id);
$preferences_stmt->execute();
$preferences_result = $preferences_stmt->get_result();

$preferences = [];
if ($preferences_result->num_rows > 0) {
    $preferences = $preferences_result->fetch_assoc();
} else {
    // Valeurs par défaut
    $preferences = [
        'email_notifications' => 1,
        'sms_notifications' => 1,
        'newsletter' => 0,
        'partage_donnees' => 0
    ];
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Patient - MediStatView</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #1d566b;
            --secondary-color: #216b4e;
            --accent-color1: #7bba9a;
            --accent-color2: #86b3c3;
            --accent-color3: #CC0000;
            --light-bg: #f8f9fa;
            --text-dark: #333;
            --text-light: #fff;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
            --border-color: #e0e0e0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-bg);
            line-height: 1.6;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        header {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem 2rem;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        /* Navigation principale */
        .main-nav {
            flex-grow: 1;
            display: flex;
            justify-content: center;
        }
        
        .nav-list {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--text-light);
            text-decoration: none;
            padding: 0.7rem 1rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .nav-link i {
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
            color: var(--accent-color2);
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link:hover i {
            color: var(--accent-color1);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .nav-link.active i {
            color: var(--accent-color1);
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background-color: var(--accent-color1);
            border-radius: 10px;
        }
        
        /* User Menu */
        .user-menu {
            position: relative;
        }
        
        .user-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .user-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        
        .user-name {
            font-weight: 600;
        }
        
        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: var(--shadow);
            border-radius: 8px;
            width: 200px;
            margin-top: 0.5rem;
            display: none;
            z-index: 100;
        }
        
        .dropdown-menu.active {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }
        
        .dropdown-item i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        .dropdown-divider {
            border-top: 1px solid var(--border-color);
            margin: 0.5rem 0;
        }
        
        /* Profile layout */
        .profile {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2rem;
        }
        
        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: var(--primary-color);
        }
        
        .profile-subtitle {
            font-size: 1rem;
            color: #666;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.7rem 1.4rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }
        
        .btn-primary {
            background-color: var(--accent-color1);
            color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #6aa889;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline:hover {
            background-color: rgba(29, 86, 107, 0.1);
            transform: translateY(-2px);
        }
        
        /* Profile sections */
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }
        
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .profile-main {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .profile-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .profile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .profile-card-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .profile-card-title i {
            color: var(--accent-color1);
        }
        
        .profile-avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: var(--accent-color1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            position: relative;
        }
        
        .avatar-edit-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            border: 2px solid white;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .avatar-edit-btn:hover {
            background-color: var(--primary-color);
            transform: scale(1.1);
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            text-align: center;
        }
        
        .profile-username {
            color: #666;
            text-align: center;
        }
        
        .profile-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #2ecc71;
        }
        
        .status-text {
            font-size: 0.9rem;
            color: #2ecc71;
        }
        
        .profile-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .profile-info-list {
            list-style: none;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #555;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-label i {
            color: var(--accent-color2);
            width: 20px;
            text-align: center;
        }
        
        .info-value {
            color: var(--text-dark);
        }
        
        /* Form Controls */
        .form-section {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--accent-color1);
            outline: none;
            box-shadow: 0 0 0 3px rgba(123, 186, 154, 0.2);
        }
        
        .form-select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
            appearance: none;
        }
        /* Tabs */
        .tabs {
            width: 100%;
        }

        .tab-header {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            padding: 0.8rem 1.2rem;
            border: none;
            background-color: #f1f1f1;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #555;
        }

        .tab-btn:hover {
            background-color: #e0e0e0;
        }

        .tab-btn.active {
            background-color: var(--primary-color);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Checkbox groups */
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-group label {
            cursor: pointer;
        }

        /* Alert styles */
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            transition: opacity 0.5s ease;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Form buttons */
        .form-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
        </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>MediStatView</h1>
                </div>
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="fas fa-home"></i>
                                Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="consultations.php" class="nav-link">
                                <i class="fas fa-calendar-check"></i>
                                Consultations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="documents.php" class="nav-link">
                                <i class="fas fa-file-medical"></i>
                                Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="medecins.php" class="nav-link">
                                <i class="fas fa-user-md"></i>
                                Médecins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patient_profile.php" class="nav-link active">
                                <i class="fas fa-user-circle"></i>
                                Profil
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <button class="user-btn" id="userMenuBtn">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($userData['prenom'], 0, 1) . substr($userData['nom'], 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo $userData['prenom'] . ' ' . $userData['nom']; ?></div>
                            <div class="user-role">Patient</div>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="patient_profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i> Mon profil
                        </a>
                        <a href="notifications.php" class="dropdown-item">
                            <i class="fas fa-bell"></i> Notifications
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="profile">
        <div class="container">
            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $alertType; ?>" role="alert">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <div class="page-header">
                <div>
                    <h2 class="page-title">Mon profil</h2>
                    <p class="profile-subtitle">Gérez vos informations personnelles et vos préférences</p>
                </div>
                <div class="action-buttons">
                    <a href="dashboard.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                </div>
            </div>

            <div class="profile-content">
                <div class="profile-sidebar">
                    <!-- Carte de profil -->
                    <div class="profile-card">
                        <div class="profile-avatar-container">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($userData['prenom'], 0, 1) . substr($userData['nom'], 0, 1)); ?>
                                <button class="avatar-edit-btn" title="Modifier la photo de profil">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <h3 class="profile-name"><?php echo $userData['prenom'] . ' ' . $userData['nom']; ?></h3>
                            <p class="profile-username">@<?php echo $userData['username']; ?></p>
                            <div class="profile-status">
                                <span class="status-indicator"></span>
                                <span class="status-text">Compte actif</span>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $stats['consultations']; ?></div>
                                <div class="stat-label">Consultations</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $stats['medecins']; ?></div>
                                <div class="stat-label">Médecins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $stats['documents']; ?></div>
                                <div class="stat-label">Documents</div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <h3 class="profile-card-title">
                                <i class="fas fa-address-card"></i> Informations de contact
                            </h3>
                        </div>
                        <ul class="profile-info-list">
                            <li class="info-item">
                                <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                                <span class="info-value"><?php echo $userData['email']; ?></span>
                            </li>
                            <li class="info-item">
                                <span class="info-label"><i class="fas fa-phone"></i> Téléphone</span>
                                <span class="info-value"><?php echo $userData['telephone']; ?></span>
                            </li>
                            <li class="info-item">
                                <span class="info-label"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                                <span class="info-value"><?php echo $userData['adresse']; ?></span>
                            </li>
                            <li class="info-item">
                                <span class="info-label"><i class="fas fa-city"></i> Ville</span>
                                <span class="info-value"><?php echo $userData['ville_nom']; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="profile-main">
                    <!-- Onglets de profil -->
                    <div class="tabs">
                        <div class="tab-header">
                            <button class="tab-btn active" data-tab="personal">Informations personnelles</button>
                            <button class="tab-btn" data-tab="security">Sécurité</button>
                            <button class="tab-btn" data-tab="preferences">Préférences</button>
                        </div>

                        <!-- Section Informations personnelles -->
                        <div class="tab-content active" id="personal">
                            <div class="profile-card">
                                <div class="profile-card-header">
                                    <h3 class="profile-card-title">
                                        <i class="fas fa-user-edit"></i> Modifier vos informations personnelles
                                    </h3>
                                </div>
                                <form action="patient_profile.php" method="POST">
                                    <input type="hidden" name="action" value="update_profile">
                                    <div class="form-section">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="prenom" class="form-label">Prénom</label>
                                                <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo $userData['prenom']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="nom" class="form-label">Nom</label>
                                                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo $userData['nom']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" id="email" name="email" class="form-control" value="<?php echo $userData['email']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="telephone" class="form-label">Téléphone</label>
                                                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?php echo $userData['telephone']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                                <input type="text" id="username" name="username" class="form-control" value="<?php echo $userData['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                                <input type="date" id="date_naissance" name="date_naissance" class="form-control" value="<?php echo $userData['date_naissance']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="sexe" class="form-label">Sexe</label>
                                                <select id="sexe" name="sexe" class="form-select" required>
                                                    <option value="M" <?php echo ($userData['sexe'] == 'M') ? 'selected' : ''; ?>>Masculin</option>
                                                    <option value="F" <?php echo ($userData['sexe'] == 'F') ? 'selected' : ''; ?>>Féminin</option>
                                                    <option value="Autre" <?php echo ($userData['sexe'] == 'Autre') ? 'selected' : ''; ?>>Autre</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="adresse" class="form-label">Adresse</label>
                                                <input type="text" id="adresse" name="adresse" class="form-control" value="<?php echo $userData['adresse']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="ville" class="form-label">Ville</label>
                                                <select id="ville" name="ville" class="form-select">
                                                    <option value="">Sélectionnez une ville</option>
                                                    <?php foreach ($villes as $ville): ?>
                                                    <option value="<?php echo $ville['id']; ?>" <?php echo ($userData['ville_id'] == $ville['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $ville['nom']; ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="mutuelle" class="form-label">Mutuelle</label>
                                                <select id="mutuelle" name="mutuelle" class="form-select">
                                                    <option value="">Aucune mutuelle</option>
                                                    <?php foreach ($mutuelles as $mutuelle): ?>
                                                    <option value="<?php echo $mutuelle['id']; ?>" <?php echo ($userData['mutuelle_id'] == $mutuelle['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $mutuelle['nom'] . ' (' . $mutuelle['code'] . ')'; ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-buttons">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer les modifications
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Section Sécurité -->
                        <div class="tab-content" id="security">
                            <div class="profile-card">
                                <div class="profile-card-header">
                                    <h3 class="profile-card-title">
                                        <i class="fas fa-lock"></i> Changement de mot de passe
                                    </h3>
                                </div>
                                <form action="patient_profile.php" method="POST">
                                    <input type="hidden" name="action" value="update_password">
                                    <div class="form-section">
                                        <div class="form-group">
                                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                                <input type="password" id="new_password" name="new_password" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-buttons">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Modifier le mot de passe
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="profile-card">
                                <div class="profile-card-header">
                                    <h3 class="profile-card-title">
                                        <i class="fas fa-shield-alt"></i> Question de sécurité
                                    </h3>
                                </div>
                                <form action="patient_profile.php" method="POST">
                                    <input type="hidden" name="action" value="update_security">
                                    <div class="form-section">
                                        <div class="form-group">
                                            <label for="security_question" class="form-label">Question de sécurité</label>
                                            <input type="text" id="security_question" name="security_question" class="form-control" placeholder="Ex: Nom de jeune fille de votre mère?" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="security_answer" class="form-label">Réponse</label>
                                            <input type="text" id="security_answer" name="security_answer" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-buttons">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Section Préférences -->
                        <div class="tab-content" id="preferences">
                            <div class="profile-card">
                                <div class="profile-card-header">
                                    <h3 class="profile-card-title">
                                        <i class="fas fa-bell"></i> Préférences de notification
                                    </h3>
                                </div>
                                <form action="patient_profile.php" method="POST">
                                    <input type="hidden" name="action" value="update_preferences">
                                    <div class="form-section">
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="email_notifications" name="email_notifications" <?php echo ($preferences['email_notifications'] == 1) ? 'checked' : ''; ?>>
                                            <label for="email_notifications">Recevoir des notifications par email</label>
                                        </div>
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="sms_notifications" name="sms_notifications" <?php echo ($preferences['sms_notifications'] == 1) ? 'checked' : ''; ?>>
                                            <label for="sms_notifications">Recevoir des notifications par SMS</label>
                                        </div>
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="newsletter" name="newsletter" <?php echo ($preferences['newsletter'] == 1) ? 'checked' : ''; ?>>
                                            <label for="newsletter">S'abonner à la newsletter</label>
                                        </div>
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="data_sharing" name="data_sharing" <?php echo ($preferences['partage_donnees'] == 1) ? 'checked' : ''; ?>>
                                            <label for="data_sharing">Autoriser le partage anonyme de mes données à des fins statistiques</label>
                                        </div>
                                    </div>
                                    <div class="form-buttons">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer les préférences
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-text">
                    &copy; <?php echo date('Y'); ?> MediStatView - Tous droits réservés
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Gestion du menu déroulant utilisateur
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        
        userMenuBtn.addEventListener('click', () => {
            userDropdown.classList.toggle('active');
        });
        
        // Fermer le menu déroulant si on clique ailleurs
        document.addEventListener('click', (event) => {
            if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.remove('active');
            }
        });
        
        // Gestion des onglets
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Suppression de la classe active de tous les boutons et contenus
                tabBtns.forEach(item => item.classList.remove('active'));
                tabContents.forEach(item => item.classList.remove('active'));
                
                // Ajout de la classe active au bouton cliqué
                btn.classList.add('active');
                
                // Affichage du contenu correspondant
                const tabId = btn.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Afficher le message d'alerte pendant 5 secondes puis le faire disparaître
        const alertElement = document.querySelector('.alert');
        if (alertElement) {
            setTimeout(() => {
                alertElement.style.opacity = '0';
                setTimeout(() => {
                    alertElement.style.display = 'none';
                }, 500);
            }, 5000);
        }
    </script>
</body>
</html>