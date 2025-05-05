<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Médecin - MediStatView</title>
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

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .form-group input:focus {
            border-color: #216b4e;
            background-color: #fff;
            outline: none;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .sign-in-btn {
            width: 100%;
            padding: 15px;
            background-color: #216b4e;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .sign-in-btn:hover {
            background-color: #1a563f;
        }
        
        .mfa-option {
            font-size: 17px;
            color: #000000;
            margin-top: 16px;
            display: flex;
            align-items: center;
        }

        .mfa-option svg {
            margin-right: 8px;
            flex-shrink: 0;
        }

        .divider {
            margin: 25px 0;
            text-align: center;
            color: #aaa;
        }

        h3 {
            font-size: 17px;
            color: #1a563f;
            margin-bottom: 16px;
        }
        
        .register-box {
            text-align: center;
            margin-bottom: 10px;
        }

        .register-btn {
            padding: 12px 30px;
            background-color: #77c4a0;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            color: white;
            cursor: pointer;
        }

        .register-btn:hover {
            background-color: #60b38a;
        }

        .patient-portal-link {
            display: inline-block;
            margin-top: 20px;
            color: #216b4e;
            text-decoration: none;
            font-size: 14px;
            border-bottom: 1px dashed #77c4a0;
        }

        .patient-portal-link:hover {
            border-bottom: 1px solid #216b4e;
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
            
            .remember-forgot {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
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
            
            .sign-in-btn {
                padding: 12px;
            }
            
            .register-btn {
                padding: 10px 20px;
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
                    ESPACE MÉDECIN
                </div>
                
                <div class="language-selector">
                    Langue:
                    <select>
                        <option value="fr">Français</option>
                        <option value="en">English</option>
                        <option value="es">Español</option>
                        <option value="de">Deutsch</option>
                    </select>
                </div>

                <h1>Bienvenue sur l'espace médecin</h1>
                <p class="welcome-text">Connectez-vous à votre compte pour accéder à vos données cliniques et statistiques médicales</p>

                <form id="loginForm" action="doctor-login.php" method="post">
                    <div class="form-group">
                        <label for="username">Identifiant professionnel</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="remember-forgot">
                        <label><input type="checkbox" name="remember"> Se souvenir de moi</label>
                        <a href="reset-password.php">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="sign-in-btn">Se Connecter</button>

                    <div class="mfa-option">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                        </svg>
                        Utiliser l'authentification à deux facteurs
                    </div>
                </form>

                <div class="divider">ou</div>

                <div class="register-box">
                    <h3>Première connexion à l'espace médecin ?</h3>
                    <a href="docInscrire.php"><button class="register-btn">Demander un accès</button></a>
                </div>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="userInscrire.php" class="patient-portal-link">Accéder au portail patient</a>
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
</body>
</html>