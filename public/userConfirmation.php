

<?php
session_start();

// Vérifier si l'utilisateur vient bien de s'inscrire
if (!isset($_SESSION['inscription_success']) || $_SESSION['inscription_success'] !== true) {
    // Rediriger vers la page d'inscription si l'utilisateur n'a pas terminé l'inscription
    header("Location: userInscrire.php");
    exit;
}

unset($_SESSION['inscription_success']);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription - MediStatView</title>
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

        .confirmation-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .confirmation-icon {
            width: 80px;
            height: 80px;
            background-color: #5e8c99;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px;
        }

        .confirmation-icon svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 28px;
            color: #1d566b;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .next-steps {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .next-steps h3 {
            color: #1d566b;
            margin-bottom: 15px;
        }

        .next-steps ol {
            padding-left: 20px;
        }

        .next-steps li {
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #1d566b;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #154050;
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

        .footer-item svg {
            margin-right: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .confirmation-icon {
                width: 60px;
                height: 60px;
            }
            
            .confirmation-icon svg {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="confirmation-container">
            <div class="confirmation-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            
            <h1>Demande d'inscription reçue !</h1>
            
            <p>Merci pour votre demande d'accès à MediStatView. Votre dossier a été soumis avec succès et sera examiné par notre équipe.</p>
            
            <div class="next-steps">
                <h3>Prochaines étapes :</h3>
                <ol>
                    <li>Notre équipe vérifiera vos informations personnelles et validera votre compte.</li>
                    <li>Vous recevrez un email de confirmation à l'adresse que vous avez indiquée lorsque votre compte sera activé.</li>
                    <li>Ce processus prend généralement entre 24 et 48 heures ouvrables.</li>
                </ol>
            </div>
            
            <p>En attendant, vous pouvez consulter notre site web pour découvrir les fonctionnalités de la plateforme MediStatView.</p>
            
            <a href="userConnecter.php" class="btn">Retour à la page de connexion</a>
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
</body>
</html>