<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales - MediStatView</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1d566b;
            --secondary-color: #86b3c3;
            --accent-color: #5dc1b9;
            --light-color: #e7f1f5;
            --hover-color: #144255;
            --text-color: #333333;
            --transition-speed: 0.3s;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f7f9;
            scroll-behavior: smooth;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.2rem 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo-text h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .logo-text p {
            margin: 0;
            font-size: 0.8rem;
            letter-spacing: 3px;
            opacity: 0.9;
        }
        
        main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .banner {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            margin-bottom: 2rem;
            text-align: center;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .banner.fade-out {
            opacity: 0;
            transform: translateY(-20px);
        }
        
        .banner h2 {
            color: var(--primary-color);
            margin-top: 0;
            font-size: 2.2rem;
            margin-bottom: 1rem;
        }
        
        .banner p {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .buttons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .choice-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 280px;
            padding: 2rem 1.5rem;
            background-color: white;
            border: 2px solid var(--light-color);
            border-radius: 16px;
            text-decoration: none;
            color: var(--primary-color);
            transition: all var(--transition-speed) ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .choice-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background-color: var(--secondary-color);
            transform: scaleX(0);
            transform-origin: 0 0;
            transition: transform var(--transition-speed) ease;
        }
        
        .choice-button:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: var(--secondary-color);
        }
        
        .choice-button:hover::before {
            transform: scaleX(1);
        }
        
        .button-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-color);
            border-radius: 50%;
            color: var(--primary-color);
            transition: all var(--transition-speed) ease;
        }
        
        .choice-button:hover .button-icon {
            background-color: var(--secondary-color);
            color: white;
            transform: rotate(360deg);
        }
        
        .choice-button h3 {
            margin: 0 0 0.8rem 0;
            font-size: 1.4rem;
            text-align: center;
        }
        
        .choice-button p {
            margin: 0;
            text-align: center;
            font-size: 1rem;
            color: #666;
        }
        
        .content-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            margin: 2rem 0;
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .content-section.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        .section-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .section-header i {
            margin-right: 1rem;
            font-size: 1.8rem;
        }
        
        .section-header h2 {
            margin: 0;
            color: white;
            font-size: 1.8rem;
            border: none;
        }
        
        .section-content {
            font-size: 1.05rem;
        }
        
        .section-content h3 {
            color: var(--primary-color);
            border-bottom: 2px solid #eee;
            padding-bottom: 0.8rem;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
        }
        
        .section-content h3::before {
            content: '•';
            color: var(--accent-color);
            margin-right: 0.8rem;
            font-size: 1.8rem;
            line-height: 1;
        }
        
        .section-content h3:first-child {
            margin-top: 0;
        }
        
        .section-content ul {
            padding-left: 2rem;
            margin-bottom: 1rem;
        }
        
        .section-content ul li {
            margin-bottom: 0.5rem;
        }
        
        .section-content p {
            margin-bottom: 1rem;
        }
        
        .back-buttons {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0 0.5rem;
        }
        
        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            color: white;
            padding: 0.9rem 1.5rem;
            text-decoration: none;
            border-radius: 8px;
            transition: all var(--transition-speed);
            font-weight: 500;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            min-width: 150px;
        }
        
        .action-button:hover {
            background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }
        
        .action-button i {
            margin-right: 0.8rem;
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-nav {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .footer-nav a {
            color: white;
            text-decoration: none;
            margin: 0.5rem 1rem;
            opacity: 0.9;
            transition: opacity var(--transition-speed);
        }
        
        .footer-nav a:hover {
            opacity: 1;
            text-decoration: underline;
        }
        
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all var(--transition-speed);
            opacity: 0;
            visibility: hidden;
            z-index: 99;
        }
        
        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .scroll-top:hover {
            background-color: var(--accent-color);
            transform: translateY(-5px);
        }
        
        .progress-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
            z-index: 1000;
        }
        
        .progress-bar {
            height: 4px;
            background: var(--accent-color);
            width: 0%;
        }
        
        @media (max-width: 768px) {
            .banner {
                padding: 2rem 1.5rem;
            }
            
            .buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .choice-button {
                width: 100%;
                max-width: 320px;
            }
            
            .content-section {
                padding: 1.5rem;
            }
            
            .back-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .action-button {
                width: 100%;
            }
            
            .section-header {
                padding: 1rem;
            }
            
            .section-header i {
                font-size: 1.4rem;
            }
            
            .section-header h2 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>
    

    <header>
        <div class="header-content">
            <div class="logo">
                <svg width="180" height="50" viewBox="0 0 180 50">
                    <rect x="10" y="15" width="20" height="20" fill="#76b5c5" />
                    <polygon points="30,15 40,25 30,35" fill="#a7c5d1" />
                    <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                    <text x="50" y="40" fill="#a7c5d1" font-size="12">SERVICES</text>
                </svg>
            </div>
        </div>
    </header>
    
    <main>
        <div id="banner" class="banner">
            <h2>Mentions légales</h2>
            <p>Veuillez choisir la section que vous souhaitez consulter</p>
            
            <div class="buttons">
                <div class="choice-button" onclick="showSection('terms-section')">
                    <div class="button-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Conditions d'utilisation</h3>
                    <p>Règles et modalités d'utilisation du service MediStatView</p>
                </div>
                
                <div class="choice-button" onclick="showSection('privacy-section')">
                    <div class="button-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Politique de confidentialité</h3>
                    <p>Comment nous protégeons et utilisons vos données</p>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <a href="userInscrire.php" class="action-button">
                    <i class="fas fa-user-plus"></i> Retour à l'inscription
                </a>
            </div>
        </div>
        
        <div id="terms-section" class="content-section">
            <div class="section-header">
                <i class="fas fa-file-contract"></i>
                <h2>Conditions d'utilisation</h2>
            </div>
            
            <div class="section-content">
                <h3>Acceptation des conditions</h3>
                <p>En utilisant l'application MediStatView, vous acceptez de vous conformer aux présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser cette application.</p>
                
                <h3>Description du service</h3>
                <p>MediStatView est une application de gestion des données médicales permettant de suivre les patients, leurs symptômes, diagnostics et traitements, ainsi que d'analyser des statistiques épidémiologiques.</p>
                
                <h3>Utilisation du service</h3>
                <p>Vous vous engagez à utiliser le service conformément à toutes les lois applicables et à ne pas l'utiliser à des fins illégales ou non autorisées.</p>
                
                <h3>Compte utilisateur</h3>
                <p>Pour accéder à certaines fonctionnalités, vous devez créer un compte. Vous êtes responsable de maintenir la confidentialité de vos informations de connexion et de toutes les activités qui se produisent sous votre compte.</p>
                
                <h3>Modifications du service</h3>
                <p>Nous nous réservons le droit de modifier ou d'interrompre le service à tout moment, avec ou sans préavis.</p>
            </div>
            
            <div class="back-buttons">
                <button class="action-button" onclick="showSection('banner')">
                    <i class="fas fa-arrow-left"></i> Retour aux choix
                </button>
                <a href="userInscrire.php" class="action-button">
                    <i class="fas fa-user-plus"></i> Retour à l'inscription
                </a>
            </div>
        </div>
        
        <div id="privacy-section" class="content-section">
            <div class="section-header">
                <i class="fas fa-shield-alt"></i>
                <h2>Politique de confidentialité</h2>
            </div>
            
            <div class="section-content">
                <h3>Collecte des données</h3>
                <p>MediStatView collecte des données médicales, y compris des informations sur les patients, leurs symptômes, diagnostics et traitements, dans le but de fournir le service.</p>
                
                <h3>Utilisation des données</h3>
                <p>Les données collectées sont utilisées pour :</p>
                <ul>
                    <li>Fournir et maintenir le service</li>
                    <li>Générer des statistiques épidémiologiques</li>
                    <li>Améliorer l'expérience utilisateur</li>
                    <li>Respecter les obligations légales</li>
                </ul>
                
                <h3>Protection des données</h3>
                <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données contre tout accès non autorisé, modification, divulgation ou destruction.</p>
                
                <h3>Partage des données</h3>
                <p>Nous ne partageons pas vos données avec des tiers, sauf :</p>
                <ul>
                    <li>Avec votre consentement explicite</li>
                    <li>Pour se conformer à une obligation légale</li>
                    <li>Pour protéger nos droits, notre propriété ou notre sécurité</li>
                </ul>
                
                <h3>Conservation des données</h3>
                <p>Les données sont conservées aussi longtemps que nécessaire pour fournir le service ou conformément aux exigences légales.</p>
                
                <h3>Vos droits</h3>
                <p>Vous avez le droit d'accéder à vos données, de les rectifier, de les supprimer ou d'en limiter le traitement.</p>
            </div>
            
            <div class="back-buttons">
                <button class="action-button" onclick="showSection('banner')">
                    <i class="fas fa-arrow-left"></i> Retour aux choix
                </button>
                <a href="userInscrire.php" class="action-button">
                    <i class="fas fa-user-plus"></i> Retour à l'inscription
                </a>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 MediStatView - Tous droits réservés</p>
            <div class="footer-nav">
                <a href="index.php">Accueil</a>
                <a href="contact.php">Contact</a>
                <a href="userterms&privacy.php">Mentions légales</a>
            </div>
        </div>
    </footer>
    
    <div class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        // Show selected section with animation
        function showSection(sectionId) {
            // Get all sections
            const banner = document.getElementById('banner');
            const sections = document.querySelectorAll('.content-section');
            
            // Handle banner fade
            if (sectionId !== 'banner') {
                banner.classList.add('fade-out');
                setTimeout(() => {
                    banner.style.display = 'none';
                    banner.classList.remove('fade-out');
                }, 300);
            } else {
                // Hide all content sections
                sections.forEach(section => {
                    section.classList.remove('active');
                    setTimeout(() => {
                        section.style.display = 'none';
                    }, 300);
                });
                
                // Show banner with animation
                setTimeout(() => {
                    banner.style.display = 'block';
                    setTimeout(() => {
                        banner.classList.remove('fade-out');
                    }, 50);
                }, 300);
            }
            
            // If showing a content section
            if (sectionId !== 'banner') {
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    // Hide other sections
                    sections.forEach(section => {
                        if (section.id !== sectionId) {
                            section.classList.remove('active');
                            setTimeout(() => {
                                section.style.display = 'none';
                            }, 300);
                        }
                    });
                    
                    // Show target section
                    setTimeout(() => {
                        targetSection.style.display = 'block';
                        setTimeout(() => {
                            targetSection.classList.add('active');
                            // Smooth scroll to section
                            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 50);
                    }, 300);
                }
            }
            
            // Update URL hash
            if (sectionId === 'banner') {
                history.pushState("", document.title, window.location.pathname);
            } else {
                const hashValue = sectionId.replace('-section', '');
                history.pushState(null, null, `#${hashValue}`);
            }
        }
        
        // Handle scroll to top button
        window.onscroll = function() {
            scrollFunction();
            progressBar();
        };
        
        function scrollFunction() {
            const scrollTopBtn = document.getElementById("scrollTop");
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                scrollTopBtn.classList.add("visible");
            } else {
                scrollTopBtn.classList.remove("visible");
            }
        }
        
        // Update progress bar
        function progressBar() {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            document.getElementById("progressBar").style.width = scrolled + "%";
        }
        
        // Scroll to top when button clicked
        document.getElementById("scrollTop").addEventListener("click", function() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
        
        // Check URL hash on page load
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.substring(1);
            if (hash === 'terms' || hash === 'privacy') {
                showSection(hash + '-section');
            } else {
                showSection('banner');
            }
        });
    </script>
</body>
</html>