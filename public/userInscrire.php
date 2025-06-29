<?php
// Démarrage de la session au début du fichier
session_start();

// Inclure le fichier de configuration de la base de données
require_once __DIR__ . '/../config/database.php';

// Initialisation des variables avec les données de session
$old = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['inscription_errors'] ?? [];

// Nettoyage des variables de session après utilisation
unset($_SESSION['inscription_errors']);
unset($_SESSION['form_data']);

// Fonction pour récupérer les anciennes valeurs
function getOldValue($field, $default = '') {
    global $old;
    return isset($old[$field]) ? htmlspecialchars($old[$field]) : $default;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = getDatabaseConnection();

    $required_fields = [
        'nom', 'prenom', 'cin', 'date_naissance', 'sexe',
        'email', 'telephone', 'username', 'password', 'confirm_password'
    ];

    $errors = [];

    // Vérification des champs obligatoires
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Le champ $field est requis.";
        }
    }

    // Vérifier que les mots de passe correspondent
    if (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier que le format de l'email est valide
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'adresse email invalide.";
    }

    // Vérifications de base de données uniquement si les champs sont remplis
    if (!empty($_POST['email'])) {
        // Vérifier que l'email n'est pas déjà utilisé
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }
    }

    if (!empty($_POST['cin'])) {
        // Vérifier que le CIN n'est pas déjà utilisé
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE cin = ?");
        $stmt->execute([$_POST['cin']]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Ce numéro de CIN est déjà utilisé.";
        }
    }

    if (!empty($_POST['username'])) {
        // Vérifier que le nom d'utilisateur n'est pas déjà utilisé
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Ce nom d'utilisateur est déjà pris.";
        }
    }

    if (!empty($_POST['security_question']) && empty($_POST['security_answer'])) {
        $errors[] = "Veuillez fournir une réponse à la question de sécurité.";
    }

    // Vérifier l'acceptation des conditions
    if (!isset($_POST['terms'])) {
        $errors[] = "Vous devez accepter les conditions d'utilisation.";
    }

    // S'il n'y a pas d'erreurs, procéder à l'inscription
    if (empty($errors)) {
        try {
            // Début de la transaction
            $pdo->beginTransaction();
            
            // 1. Récupérer l'ID de la ville si sélectionnée
            $ville_id = null;
            if (!empty($_POST['ville_id'])) {
                $ville_id = $_POST['ville_id'];
            }
            
            // 2. Hacher le mot de passe
            $password_hash = $_POST['password'];
            
            // 3. Insérer le patient dans la base de données
            $stmt = $pdo->prepare("
                INSERT INTO patients (
                    nom, prenom, cin, date_naissance, sexe, email, 
                    telephone, adresse, ville_id, mutuelle, 
                    username, password, security_question, security_answer,
                    notifications, statut
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            
            $stmt->execute([
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['cin'],
                $_POST['date_naissance'],
                $_POST['sexe'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['adresse'] ?? null,
                $ville_id,
                $_POST['mutuelle'] ?? null,
                $_POST['username'],
                $password_hash,
                $_POST['security_question'] ?? null,
                $_POST['security_answer'] ?? null,
                isset($_POST['notifications']) ? 1 : 0
            ]);
            
            // Confirmer la transaction
            $pdo->commit();
            
            // Rediriger vers une page de confirmation
            $_SESSION['inscription_success'] = true;
            header("Location: userConfirmation.php");
            exit;
            
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = "Erreur lors de l'inscription: " . $e->getMessage();
        }
    }
    
    // S'il y a des erreurs, les stocker dans la session
    if (!empty($errors)) {
        $_SESSION['inscription_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Sauvegarder les données du formulaire
        
        // Rediriger vers le formulaire
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Connexion à la base de données pour charger les villes
$pdo = getDatabaseConnection();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Patient - MediStatView Maroc</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #a7c5d1 0%, #86b3c3 100%);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .portal-container {
            display: flex;
            flex-direction: row;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 1000px;
            overflow: hidden;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .sidebar {
            background: linear-gradient(180deg, #1d566b 0%, #133945 100%);
            width: 200px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
        }

        .sidebar-logo {
            margin-bottom: 40px;
            text-align: center;
        }

        .sidebar-logo svg {
            max-width: 100%;
            height: auto;
        }

        .main-content {
            background-color: #ffffff;
            flex: 1;
            padding: 40px 30px;
            position: relative;
        }
        
        .page-type-indicator {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #1d566b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .page-type-indicator svg {
            margin-right: 5px;
        }
        
        .language-selector {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            font-size: 16px;
            color: #666;
        }

        .language-selector select {
            margin-left: 10px;
            padding: 5px 10px;
            border: 1px solid #1d566b;
            border-radius: 10px;
            font-size: 12px;
        }

        h1 {
            font-size: 28px;
            color: #1d566b;
            margin-bottom: 10px;
        }

        .welcome-text {
            color: #666;
            margin-bottom: 30px;
        }

        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-column {
            flex: 1;
            min-width: 250px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #1d566b;
            background-color: #fff;
            outline: none;
        }

        .form-group.inline-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group.inline-group label {
            margin-bottom: 0;
        }

        .checkbox-group {
            margin-bottom: 20px;
        }

        .checkbox-group label {
            display: flex;
            align-items: flex-start;
            color: #555;
            font-size: 14px;
            line-height: 1.4;
        }

        .checkbox-group input {
            margin-right: 10px;
            margin-top: 3px;
        }

        .terms-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
        }

        .terms-text a {
            color: #1d566b;
            text-decoration: underline;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            background-color: #1d566b;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .register-btn:hover {
            background-color: #154050;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #1d566b;
            text-decoration: underline;
            font-weight: bold;
        }

        .form-group.cin-group {
            position: relative;
        }

        .cin-info {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 14px;
            color: #1d566b;
            cursor: help;
        }

        .cin-info:hover::after {
            content: "Carte Nationale d'Identité";
            position: absolute;
            right: 0;
            top: 20px;
            background: #1d566b;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 10;
        }

        .required-field::after {
            content: "*";
            color: #e74c3c;
            margin-left: 4px;
        }

        .divider {
            height: 1px;
            background-color: #ddd;
            margin: 30px 0;
        }

        .footer {
            background-color: #5e8c99;
            color: white;
            padding: 12px 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .footer-content {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            padding: 0 20px;
        }

        .footer-label {
            margin-right: 15px;
            font-weight: 500;
        }

        .footer-item {
            display: flex;
            align-items: center;
            margin-left: 20px;
            margin-bottom: 5px;
            color: white;
            text-decoration: none;
        }

        .footer-item svg, .footer-item img {
            margin-right: 10px;
            flex-shrink: 0;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
        }

        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }


        /* Media Queries pour la responsivité */
        @media (max-width: 900px) {
            .portal-container {
                max-width: 90%;
            }
        }

        @media (max-width: 768px) {
            .portal-container {
                flex-direction: column;
                max-width: 95%;
            }
            
            .sidebar {
                width: 100%;
                padding: 20px;
            }
            
            .sidebar-logo {
                margin-bottom: 20px;
            }
            
            .main-content {
                padding: 50px 20px 30px;
            }
            
            .language-selector {
                top: 10px;
                right: 10px;
            }
        }

        @media (max-width: 576px) {
            .main-wrapper {
                padding: 20px 10px;
            }
            
            .portal-container {
                max-width: 100%;
                border-radius: 8px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .welcome-text {
                font-size: 14px;
            }
            
            .form-container {
                flex-direction: column;
                gap: 0;
            }
            
            .footer-content {
                justify-content: center;
            }
            
            .footer-label {
                width: 100%;
                text-align: center;
                margin-bottom: 10px;
                margin-right: 0;
            }
            
            .footer-item {
                margin: 5px 10px;
            }
        }

        @media (max-width: 375px) {
            .main-content {
                padding: 40px 15px 20px;
            }
            
            .form-group input {
                padding: 12px;
            }
            
            .register-btn {
                padding: 12px;
            }
        }
        .sidebar-logo {
    margin-bottom: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    padding: 15px 10px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-logo:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.2),
        0 4px 10px rgba(255, 255, 255, 0.1);
}

.sidebar-logo a {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
    height: 100%;
    position: relative;
}

.sidebar-logo svg {
    max-width: 100%;
    height: auto;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.sidebar-logo:hover svg {
    transform: scale(1.05);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
}

.sidebar-logo::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
    border-radius: 12px;
}

.sidebar-logo::after {
    content: 'Retour à l\'accueil';
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    pointer-events: none;
    z-index: 1000;
}

.sidebar-logo:hover::after {
    opacity: 1;
    visibility: visible;
    bottom: -30px;
}

/* Animation au focus pour l'accessibilité */
.sidebar-logo a:focus {
    outline: 3px rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
    border-radius: 12px;
}

/* Effet de clic */
.sidebar-logo:active {
    transform: translateY(0) scale(0.98);
    transition: all 0.1s ease;
}

/* Ajustements responsive */
@media (max-width: 768px) {
    .sidebar-logo {
        margin-bottom: 20px;
        padding: 12px 8px;
    }
    
    .sidebar-logo::after {
        font-size: 11px;
        padding: 5px 10px;
    }
}

@media (max-width: 576px) {
    .sidebar-logo {
        padding: 10px 6px;
    }
    
    .sidebar-logo svg {
        max-width: 90%;
    }
    
    .sidebar-logo::after {
        font-size: 10px;
        padding: 4px 8px;
    }
}

/* Animation d'entrée pour le logo */
@keyframes logoFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.sidebar-logo {
    animation: logoFadeIn 0.8s ease-out 0.2s both;
}
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="portal-container">
            <div class="sidebar">
                <div class="sidebar-logo">
                    <a href="index.php" title="Retour à l'accueil principal" aria-label="Retourner à la page d'accueil">
                        <svg width="180" height="50" viewBox="0 0 180 50">
                            <rect x="10" y="15" width="20" height="20" fill="#76b5c5" />
                            <polygon points="30,15 40,25 30,35" fill="#a7c5d1" />
                            <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                            <text x="50" y="40" fill="#a7c5d1" font-size="12">PATIENT</text>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="main-content">
                <div class="page-type-indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                    </svg>
                    INSCRIPTION PATIENT
                </div>
                <div class="language-selector">
                    Langue:
                    <select>
                        <option value="fr">Français</option>
                        <option value="ar">العربية</option>
                        <option value="en">English</option>
                        <option value="es">Español</option>
                    </select>
                </div>

                <h1>Inscription Patient</h1>
                <p class="welcome-text">Créez votre compte patient pour accéder à vos données médicales et suivre vos consultations</p>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form id="registerForm" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                    <div class="form-container">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="nom" class="required-field">Nom</label>
                                <input type="text" id="nom" name="nom" value="<?= getOldValue('nom') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="prenom" class="required-field">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="<?= getOldValue('prenom') ?>" required>
                            </div>

                            <div class="form-group cin-group">
                                <label for="cin" class="required-field">CIN</label>
                                <span class="cin-info">?</span>
                                <input type="text" id="cin" name="cin" placeholder="Format: AB123456" value="<?= getOldValue('cin') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="date_naissance" class="required-field">Date de naissance</label>
                                <input type="date" id="date_naissance" name="date_naissance" value="<?= getOldValue('date_naissance') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="sexe" class="required-field">Sexe</label>
                                <select id="sexe" name="sexe" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="M" <?= getOldValue('sexe') === 'M' ? 'selected' : '' ?>>Homme</option>
                                    <option value="F" <?= getOldValue('sexe') === 'F' ? 'selected' : '' ?>>Femme</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-column">
                            <div class="form-group">
                                <label for="email" class="required-field">Email</label>
                                <input type="email" id="email" name="email" value="<?= getOldValue('email') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="telephone" class="required-field">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" placeholder="Ex: 0661234567" value="<?= getOldValue('telephone') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="ville_id">Ville</label>
                                <select id="ville_id" name="ville_id">
                                    <option value="">Sélectionnez une ville</option>
                                    <?php
                                    try {
                                        // Charger les villes depuis la base de données
                                        $stmt = $pdo->query("SELECT id, nom FROM villes ORDER BY nom ASC");
                                        while ($ville = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = getOldValue('ville_id') == $ville['id'] ? 'selected' : '';
                                            echo "<option value='{$ville['id']}' {$selected}>{$ville['nom']}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        // Gérer l'erreur silencieusement
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <input type="text" id="adresse" name="adresse" value="<?= getOldValue('adresse') ?>">
                            </div>

                            <div class="form-group">
                                <label for="mutuelle">Mutuelle</label>
                                <select id="mutuelle" name="mutuelle">
                                    <option value="">Sélectionnez</option>
                                    <option value="cnops" <?= getOldValue('mutuelle') === 'cnops' ? 'selected' : '' ?>>CNOPS</option>
                                    <option value="cnss" <?= getOldValue('mutuelle') === 'cnss' ? 'selected' : '' ?>>CNSS</option>
                                    <option value="ramed" <?= getOldValue('mutuelle') === 'ramed' ? 'selected' : '' ?>>RAMED</option>
                                    <option value="amo" <?= getOldValue('mutuelle') === 'amo' ? 'selected' : '' ?>>AMO</option>
                                    <option value="autre" <?= getOldValue('mutuelle') === 'autre' ? 'selected' : '' ?>>Autre</option>
                                    <option value="aucune" <?= getOldValue('mutuelle') === 'aucune' ? 'selected' : '' ?>>Aucune</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="form-container">
                        <div class="form-column">
                            <div class="form-group">
                                <label for="username" class="required-field">Nom d'utilisateur</label>
                                <input type="text" id="username" name="username" value="<?= getOldValue('username') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="password" class="required-field">Mot de passe</label>
                                <input type="password" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="form-column">
                            <div class="form-group">
                                <label for="confirm_password" class="required-field">Confirmer le mot de passe</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <div class="form-group">
                                <label for="security_question">Question de sécurité</label>
                                <select id="security_question" name="security_question">
                                    <option value="">Sélectionnez</option>
                                    <option value="q1" <?= getOldValue('security_question') === 'q1' ? 'selected' : '' ?>>Nom de jeune fille de votre mère</option>
                                    <option value="q2" <?= getOldValue('security_question') === 'q2' ? 'selected' : '' ?>>Nom de votre premier animal de compagnie</option>
                                    <option value="q3" <?= getOldValue('security_question') === 'q3' ? 'selected' : '' ?>>Ville de naissance de votre père</option>
                                    <option value="q4" <?= getOldValue('security_question') === 'q4' ? 'selected' : '' ?>>Nom de votre école primaire</option>
                                </select>
                            </div>
                            <div class="form-group" id="security_answer_group" style="display: none;">
                                <label for="security_answer">Réponse</label>
                                <input type="text" id="security_answer" name="security_answer" value="<?= getOldValue('security_answer') ?>">
                            </div>
                        </div>
                    </div>

                    
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="terms" required>
                            J'accepte les <a href="usertermes&privacy.php" target="_blank" >conditions d'utilisation et la politique de confidentialité</a> de MediStatView
                        </label>
                    </div>

                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="notifications" <?= isset($old['notifications']) ? 'checked' : '' ?>>
                            J'accepte de recevoir des notifications par email concernant mes rendez-vous et résultats
                        </label>
                    </div>

                    <p class="terms-text">
                        En vous inscrivant, vous acceptez que MediStatView traite vos données personnelles conformément à la loi 09-08 relative à la protection des personnes physiques à l'égard du traitement des données à caractère personnel.
                    </p>

                    <button type="submit" class="register-btn">Créer mon compte</button>

                    <div class="login-link">
                        Vous avez déjà un compte? <a href="userConnecter.php">Se connecter</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <span class="footer-label">SUPPORT:</span>
            
            <a href="tel:+212-520-000-000" class="footer-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                </svg>
                05-2000-0000
            </a>
            <a href="mailto:supportmaroc@medistatview.com" class="footer-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z"/>
                </svg>
                supportmaroc@medistatview.com
            </a>
            
            <a href="https://www.medistatview.ma" target="_blank" class="footer-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
                </svg>
                www.medistatview.ma
            </a>
        </div>
    </footer>
    <script>

        // Fonction pour afficher ou masquer le groupe du champ de réponse
        function toggleSecurityAnswer() {
            const questionField = document.getElementById('security_question');
            const answerGroup = document.getElementById('security_answer_group');
            
            // Vérifier si une question est sélectionnée et afficher/masquer le champ de réponse en conséquence
            if (questionField.value) {
                answerGroup.style.display = 'block';
            } else {
                answerGroup.style.display = 'none';
            }
        }

        // Initialiser l'état au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            toggleSecurityAnswer();
            
            // Ajouter l'écouteur d'événement pour le changement
            document.getElementById('security_question').addEventListener('change', toggleSecurityAnswer);
        });
    </script>
</body>
</html>
