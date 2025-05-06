<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Médecin - MediStatView</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #c8e1d5 0%, #7bba9a 100%);
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
            background: linear-gradient(180deg, #216b4e 0%, #0f4430 100%);
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
            overflow-y: auto;
            max-height: 90vh;
        }

        .page-type-indicator {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #216b4e;
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
            border: 1px solid #216b4e;
            border-radius: 10px;
            font-size: 12px;
        }

        h1 {
            font-size: 28px;
            color: #216b4e;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        .welcome-text {
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 22px;
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
            border-color: #216b4e;
            background-color: #fff;
            outline: none;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .hint-text {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        .terms-container {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            margin-top: 15px;
        }

        .terms-checkbox input {
            margin-right: 10px;
            margin-top: 4px;
        }

        .terms-checkbox label {
            font-size: 14px;
            line-height: 1.5;
            color: #555;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            background-color: #216b4e;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .register-btn:hover {
            background-color: #1a563f;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #216b4e;
            text-decoration: none;
            font-size: 14px;
            border-bottom: 1px dashed #77c4a0;
        }

        .login-link a:hover {
            border-bottom: 1px solid #216b4e;
        }

        .section-title {
            font-size: 18px;
            color: #216b4e;
            margin: 30px 0 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
        }

        .footer {
            background-color: #5a9e81;
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

        .upload-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #77c4a0;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 5px;
        }

        .upload-btn:hover {
            background-color: #60b38a;
        }

        .file-name {
            display: inline-block;
            margin-left: 10px;
            font-size: 12px;
            color: #666;
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

            .form-row {
                flex-direction: column;
                gap: 0;
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
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="portal-container">
            <div class="sidebar">
                <div class="sidebar-logo">
                    <svg width="180" height="50" viewBox="0 0 180 50">
                        <rect x="10" y="15" width="20" height="20" fill="#77c4a0" />
                        <polygon points="30,15 40,25 30,35" fill="#9fdec0" />
                        <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                        <text x="50" y="40" fill="#9fdec0" font-size="12">MEDECINS</text>
                    </svg>
                </div>
            </div>

            <div class="main-content">
                <div class="page-type-indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                    </svg>
                    INSCRIPTION MÉDECIN
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

                <h1>Demande d'accès médecin</h1>
                <p class="welcome-text">Complétez ce formulaire pour demander un accès à la plateforme MediStatView pour les professionnels de santé au Maroc</p>

                <form id="registrationForm" action="docInscrire.php" method="post" enctype="multipart/form-data">
                    <h2 class="section-title">Informations personnelles</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="civilite">Civilité</label>
                            <select id="civilite" name="civilite" required>
                                <option value="">Sélectionner</option>
                                <option value="dr">Dr.</option>
                                <option value="pr">Pr.</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="specialite">Spécialité</label>
                            <select id="specialite" name="specialite" required>
                                <option value="">Sélectionner</option>
                                <option value="medecin-generaliste">Médecin Généraliste</option>
                                <option value="cardiologie">Cardiologie</option>
                                <option value="dermatologie">Dermatologie</option>
                                <option value="endocrinologie">Endocrinologie</option>
                                <option value="gastro-enterologie">Gastro-entérologie</option>
                                <option value="gynecologie">Gynécologie</option>
                                <option value="neurologie">Neurologie</option>
                                <option value="ophtalmologie">Ophtalmologie</option>
                                <option value="pediatrie">Pédiatrie</option>
                                <option value="psychiatrie">Psychiatrie</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cin">CIN (Carte d'Identité Nationale)</label>
                        <input type="text" id="cin" name="cin" required>
                    </div>

                    <div class="form-group">
                        <label for="date_naissance">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" required>
                    </div>

                    <h2 class="section-title">Informations professionnelles</h2>

                    <div class="form-group">
                        <label for="num_inpe">Numéro INPE (Identifiant National des Professionnels de la Santé)</label>
                        <input type="text" id="num_inpe" name="num_inpe" required>
                        <p class="hint-text">Numéro délivré par le Ministère de la Santé du Maroc</p>
                    </div>

                    <div class="form-group">
                        <label for="num_ordre">Numéro d'inscription à l'Ordre National des Médecins</label>
                        <input type="text" id="num_ordre" name="num_ordre" required>
                    </div>

                    <div class="form-group">
                        <label for="carte_professionnelle">Carte professionnelle (PDF, JPG ou PNG, max 2Mo)</label>
                        <input type="file" id="carte_professionnelle" name="carte_professionnelle" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" required>
                        <label for="carte_professionnelle" class="upload-btn">Choisir un fichier</label>
                        <span class="file-name" id="carte_fichier_nom">Aucun fichier choisi</span>
                    </div>

                    <h2 class="section-title">Coordonnées du cabinet</h2>

                    <div class="form-group">
                        <label for="adresse_cabinet">Adresse du cabinet</label>
                        <input type="text" id="adresse_cabinet" name="adresse_cabinet" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ville">Ville</label>
                            <select id="ville" name="ville" required>
                                <option value="">Sélectionner</option>
                                <option value="casablanca">Casablanca</option>
                                <option value="rabat">Rabat</option>
                                <option value="marrakech">Marrakech</option>
                                <option value="tanger">Tanger</option>
                                <option value="fes">Fès</option>
                                <option value="meknes">Meknès</option>
                                <option value="agadir">Agadir</option>
                                <option value="oujda">Oujda</option>
                                <option value="tetouan">Tétouan</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="code_postal">Code postal</label>
                            <input type="text" id="code_postal" name="code_postal" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telephone_cabinet">Téléphone du cabinet</label>
                            <input type="tel" id="telephone_cabinet" name="telephone_cabinet" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone_mobile">Téléphone mobile</label>
                            <input type="tel" id="telephone_mobile" name="telephone_mobile" required>
                        </div>
                    </div>

                    <h2 class="section-title">Informations de connexion</h2>

                    <div class="form-group">
                        <label for="email">Adresse email professionnelle</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" required>
                            <p class="hint-text">Au moins 8 caractères, incluant majuscules, minuscules et chiffres</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="terms-container">
                        <h3>Conditions d'utilisation</h3>
                        <div class="terms-checkbox">
                            <input type="checkbox" id="terms_agree" name="terms_agree" required>
                            <label for="terms_agree">
                                J'accepte les 
                                <a href="docTermesprivacy.php">
                                  conditions générales d'utilisation et la politique de confidentialité
                                </a> 
                                de MediStatView. Je certifie être inscrit(e) à l'Ordre National des Médecins et autorise la vérification de mes informations professionnelles.
                              </label>
                              
                        </div>
                    </div>

                    <button type="submit" class="register-btn">Soumettre ma demande</button>
                </form>

                <div class="login-link">
                    <a href="docConnecter.php">Vous avez déjà un compte ? Connectez-vous</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <span class="footer-label">SUPPORT MÉDICAL:</span>
            
            <a href="tel:05-62-44-25-08" class="footer-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                </svg>
                05 62 44 25 08
            </a>
            
            <a href="mailto:medecin@medistatview.com" class="footer-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z"/>
                </svg>
                medecin@medistatview.com
            </a>
            
            <a href="https://www.medistatview.com/medecins" target="_blank" class="footer-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
                </svg>
                www.medistatview.com/medecins
            </a>
        </div>
    </footer>

    <script>
        // Script pour afficher le nom du fichier sélectionné
        document.getElementById('carte_professionnelle').addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Aucun fichier choisi';
            document.getElementById('carte_fichier_nom').textContent = fileName;
        });
    </script>
</body>
</html>




<?php
// Inclure le fichier de configuration de la base de données
require_once 'config/database.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = connectDB();

    $required_fields = [
        'civilite', 'specialite', 'nom', 'prenom', 'cin', 'date_naissance', 
        'num_inpe', 'num_ordre', 'adresse_cabinet', 'ville', 'code_postal',
        'telephone_cabinet', 'telephone_mobile', 'email', 'password', 'confirm_password'
    ];

    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Le champ $field est requis.";
        }
    }

    // Vérifier que les mots de passe correspondent
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier que le format de l'email est valide
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'adresse email invalide.";
    }

    // Vérifier que l'email n'est pas déjà utilisé
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medecins WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Cette adresse email est déjà utilisée.";
    }

    // Vérifier que le CIN n'est pas déjà utilisé
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medecins WHERE cin = ?");
    $stmt->execute([$_POST['cin']]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Ce numéro de CIN est déjà utilisé.";
    }

    // Vérifier que le numéro INPE n'est pas déjà utilisé
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medecins WHERE num_inpe = ?");
    $stmt->execute([$_POST['num_inpe']]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Ce numéro INPE est déjà utilisé.";
    }

    // Vérifier que le numéro d'ordre n'est pas déjà utilisé
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medecins WHERE num_ordre = ?");
    $stmt->execute([$_POST['num_ordre']]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Ce numéro d'ordre est déjà utilisé.";
    }

    // Gérer l'upload de la carte professionnelle
    $carte_professionnelle_path = null;
    if (isset($_FILES['carte_professionnelle']) && $_FILES['carte_professionnelle']['error'] == 0) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['carte_professionnelle']['name'];
        $filesize = $_FILES['carte_professionnelle']['size'];
        $filetype = $_FILES['carte_professionnelle']['type'];
        
        // Vérifier l'extension du fichier
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Format de fichier non autorisé. Formats acceptés: PDF, JPG, JPEG, PNG.";
        }
        
        // Vérifier la taille du fichier (2MB max)
        if ($filesize > 2097152) {
            $errors[] = "La taille du fichier ne doit pas dépasser 2MB.";
        }
        
        if (empty($errors)) {
            // Générer un nom de fichier unique
            $new_filename = uniqid('carte_') . '.' . $ext;
            $upload_dir = 'uploads/cartes_pro/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $destination = $upload_dir . $new_filename;
            
            // Déplacer le fichier uploadé
            if (!move_uploaded_file($_FILES['carte_professionnelle']['tmp_name'], $destination)) {
                $errors[] = "Erreur lors de l'upload du fichier.";
            } else {
                $carte_professionnelle_path = $destination;
            }
        }
    } else {
        $errors[] = "Veuillez télécharger votre carte professionnelle.";
    }

    // S'il n'y a pas d'erreurs, procéder à l'inscription
    if (empty($errors)) {
        try {
            // Vérifier si la spécialité existe, sinon l'ajouter
            $stmt = $pdo->prepare("SELECT id FROM specialites WHERE nom = ?");
            $stmt->execute([$_POST['specialite']]);
            $specialite_id = $stmt->fetchColumn();
            
            if (!$specialite_id) {
                $stmt = $pdo->prepare("INSERT INTO specialites (nom) VALUES (?)");
                $stmt->execute([$_POST['specialite']]);
                $specialite_id = $pdo->lastInsertId();
            }
            
            // Vérifier si la ville existe, sinon l'ajouter
            $stmt = $pdo->prepare("SELECT id FROM villes WHERE nom = ?");
            $stmt->execute([$_POST['ville']]);
            $ville_id = $stmt->fetchColumn();
            
            if (!$ville_id) {
                $stmt = $pdo->prepare("INSERT INTO villes (nom, code_postal) VALUES (?, ?)");
                $stmt->execute([$_POST['ville'], $_POST['code_postal']]);
                $ville_id = $pdo->lastInsertId();
            }
            
            // Hacher le mot de passe
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            // Insérer le médecin dans la base de données
            $stmt = $pdo->prepare("
                INSERT INTO medecins (
                    civilite, nom, prenom, cin, date_naissance, specialite_id, 
                    num_inpe, num_ordre, carte_professionnelle, adresse_cabinet, 
                    ville_id, telephone_cabinet, telephone_mobile, email, password, statut
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            
            $stmt->execute([
                $_POST['civilite'],
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['cin'],
                $_POST['date_naissance'],
                $specialite_id,
                $_POST['num_inpe'],
                $_POST['num_ordre'],
                $carte_professionnelle_path,
                $_POST['adresse_cabinet'],
                $ville_id,
                $_POST['telephone_cabinet'],
                $_POST['telephone_mobile'],
                $_POST['email'],
                $password_hash
            ]);
            
            // Rediriger vers une page de confirmation
            $_SESSION['inscription_success'] = true;
            header("Location: docConfirmation.php");
            exit;
            
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription: " . $e->getMessage();
        }
    }
    
    // S'il y a des erreurs, les stocker dans la session
    if (!empty($errors)) {
        $_SESSION['inscription_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Sauvegarder les données du formulaire
        header("Location: docInscrire.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Médecin - MediStatView</title>
    <!-- Votre CSS ici -->
</head>
<body>
    <!-- Afficher les erreurs s'il y en a -->
    <?php if (isset($_SESSION['inscription_errors']) && !empty($_SESSION['inscription_errors'])): ?>
        <div class="error-container" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <h3>Erreurs:</h3>
            <ul>
                <?php foreach ($_SESSION['inscription_errors'] as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php 
        // Nettoyer la session
        unset($_SESSION['inscription_errors']); 
        ?>
    <?php endif; ?>

    <!-- Le reste de votre formulaire HTML ici -->
    <!-- Vous pouvez récupérer le contenu du formulaire depuis votre fichier HTML original -->
</body>
</html>