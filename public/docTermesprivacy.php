<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales - MediStatView Médecins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #216b4e;
            --secondary-color: #77c4a0;
            --accent-color: #5dc1b9;
            --light-color: #e7f5ef;
            --hover-color: #1a563f;
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
            background-color: #f5f9f7;
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
                    <rect x="10" y="15" width="20" height="20" fill="#77c4a0" />
                    <polygon points="30,15 40,25 30,35" fill="#9fdec0" />
                    <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                    <text x="50" y="40" fill="#9fdec0" font-size="12">MÉDECINS</text>
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
                    <p>Règles et modalités d'utilisation du service MediStatView pour les médecins</p>
                </div>
                
                <div class="choice-button" onclick="showSection('privacy-section')">
                    <div class="button-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Politique de confidentialité</h3>
                    <p>Comment nous protégeons et utilisons les données médicales</p>
                </div>
                
                <div class="choice-button" onclick="showSection('deontology-section')">
                    <div class="button-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Déontologie médicale</h3>
                    <p>Engagements éthiques et conformité au code de déontologie</p>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <a href="docInscrire.php" class="action-button">
                    <i class="fas fa-user-plus"></i> Retour à l'inscription
                </a>
            </div>
        </div>
        
        <div id="terms-section" class="content-section">
            <div class="section-header">
                <i class="fas fa-file-contract"></i>
                <h2>Conditions d'utilisation pour les médecins</h2>
            </div>
            
            <div class="section-content">
                <h3>Acceptation des conditions</h3>
                <p>En utilisant l'application MediStatView en tant que professionnel de santé, vous acceptez de vous conformer aux présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser cette application.</p>
                
                <h3>Description du service</h3>
                <p>MediStatView est une application destinée aux professionnels de santé exerçant au Maroc, permettant la gestion des dossiers patients, le suivi médical, ainsi que l'accès à des statistiques épidémiologiques pour soutenir la pratique médicale et la recherche clinique.</p>
                
                <h3>Vérification des qualifications</h3>
                <p>L'accès à MediStatView nécessite une vérification de vos qualifications professionnelles. Vous devez fournir des informations exactes concernant votre identité, votre numéro INPE (Identifiant National des Professionnels de la Santé) et votre inscription à l'Ordre National des Médecins. Toute fausse déclaration entraînera la suppression immédiate de votre compte.</p>
                
                <h3>Responsabilités du médecin</h3>
                <p>En tant que médecin utilisateur de MediStatView, vous vous engagez à :</p>
                <ul>
                    <li>Utiliser le service dans le respect du secret médical et du code de déontologie</li>
                    <li>Saisir des informations médicales précises et pertinentes</li>
                    <li>Ne pas partager vos identifiants de connexion avec des tiers</li>
                    <li>Informer vos patients de l'utilisation de MediStatView pour la gestion de leur dossier médical</li>
                    <li>Signaler toute anomalie ou incident de sécurité constaté sur la plateforme</li>
                </ul>
                
                <h3>Limitations de responsabilité</h3>
                <p>MediStatView est un outil d'aide à la pratique médicale et ne se substitue en aucun cas au jugement clinique du médecin. Les décisions médicales prises restent sous l'entière responsabilité du praticien. La plateforme ne saurait être tenue responsable des conséquences d'une utilisation inappropriée du service.</p>
                
                <h3>Modifications du service</h3>
                <p>Nous nous réservons le droit de modifier ou d'interrompre le service à tout moment, avec ou sans préavis. Les utilisateurs seront informés des changements majeurs affectant l'utilisation du service.</p>
            </div>
            
            <div class="back-buttons">
                <button class="action-button" onclick="showSection('banner')">
                    <i class="fas fa-arrow-left"></i> Retour aux choix
                </button>
                <a href="docInscrire.php" class="action-button">
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
                <h3>Données traitées</h3>
                <p>MediStatView traite plusieurs catégories de données :</p>
                <ul>
                    <li>Données d'identification des professionnels de santé (nom, prénom, coordonnées, numéros d'identification professionnelle)</li>
                    <li>Données des patients (données d'identification, antécédents médicaux, symptômes, diagnostics, traitements)</li>
                    <li>Données de connexion et d'utilisation du service</li>
                </ul>
                
                <h3>Base légale du traitement</h3>
                <p>Le traitement des données est effectué sur la base :</p>
                <ul>
                    <li>Du consentement des patients (recueilli par le médecin)</li>
                    <li>De l'exécution du contrat de service avec le professionnel de santé</li>
                    <li>Des obligations légales relatives à la conservation des données de santé</li>
                    <li>De l'intérêt légitime pour l'amélioration du service et la recherche médicale (données anonymisées)</li>
                </ul>
                
                <h3>Hébergement des données de santé</h3>
                <p>Les données médicales collectées sont hébergées par un hébergeur agréé pour les données de santé, conformément à la réglementation marocaine en vigueur et aux normes internationales de sécurité.</p>
                
                <h3>Sécurité des données</h3>
                <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles renforcées pour protéger les données médicales :</p>
                <ul>
                    <li>Chiffrement des données sensibles</li>
                    <li>Authentification forte pour l'accès au système</li>
                    <li>Journalisation des accès et modifications</li>
                    <li>Audits de sécurité réguliers</li>
                    <li>Formation du personnel à la sécurité des données</li>
                </ul>
                
                <h3>Partage des données</h3>
                <p>Les données médicales ne sont accessibles qu'aux professionnels de santé autorisés dans le cadre de la prise en charge des patients. Les données peuvent être partagées :</p>
                <ul>
                    <li>Entre professionnels de santé impliqués dans le parcours de soins du patient (avec son consentement)</li>
                    <li>Sous forme anonymisée ou agrégée pour des fins statistiques ou de recherche</li>
                    <li>Avec les autorités compétentes en cas d'obligation légale</li>
                </ul>
                
                <h3>Conservation des données</h3>
                <p>Les données médicales sont conservées conformément à la réglementation en vigueur, soit 20 ans à compter de la dernière consultation du patient, ou 10 ans après son décès. Les données des professionnels de santé sont conservées pendant toute la durée d'utilisation du service et jusqu'à 5 ans après la clôture du compte.</p>
                
                <h3>Droits des personnes concernées</h3>
                <p>En tant que professionnel de santé, vous disposez de droits sur vos données personnelles (accès, rectification, effacement, limitation, portabilité). Vous êtes également responsable d'informer vos patients de leurs droits et de la manière de les exercer concernant leurs données de santé.</p>
            </div>
            
            <div class="back-buttons">
                <button class="action-button" onclick="showSection('banner')">
                    <i class="fas fa-arrow-left"></i> Retour aux choix
                </button>
                <a href="docInscrire.php" class="action-button">
                    <i class="fas fa-user-plus"></i> Retour à l'inscription
                </a>
            </div>
        </div>
        
        <div id="deontology-section" class="content-section">
            <div class="section-header">
                <i class="fas fa-clipboard-check"></i>
                <h2>Déontologie médicale</h2>
            </div>
            
            <div class="section-content">
                <h3>Conformité au code de déontologie</h3>
                <p>MediStatView s'engage à respecter les principes du code de déontologie médicale et exige que ses utilisateurs médecins fassent de même lors de l'utilisation du service. L'outil a été conçu pour faciliter le respect des obligations déontologiques des médecins.</p>
                
                <h3>Secret médical</h3>
                <p>La plateforme MediStatView est conçue pour garantir le strict respect du secret médical. Les données des patients ne sont accessibles qu'aux professionnels habilités. Les médecins utilisateurs s'engagent à préserver la confidentialité des informations auxquelles ils ont accès via la plateforme.</p>
                
                <h3>Consentement des patients</h3>
                <p>Il est de la responsabilité du médecin d'informer ses patients de l'utilisation de MediStatView pour la gestion de leur dossier médical et d'obtenir leur consentement éclairé. Des outils de recueil du consentement sont disponibles sur la plateforme.</p>
                
                <h3>Qualité des soins</h3>
                <p>MediStatView vise à améliorer la qualité des soins en facilitant :</p>
                <ul>
                    <li>Le suivi médical rigoureux des patients</li>
                    <li>La coordination entre les différents professionnels de santé</li>
                    <li>L'accès aux données nécessaires à la prise de décision médicale</li>
                    <li>L'identification des tendances épidémiologiques pertinentes</li>
                </ul>
                
                <h3>Formation continue</h3>
                <p>La plateforme propose des ressources pédagogiques et des informations actualisées sur les bonnes pratiques médicales, contribuant ainsi à la formation continue des médecins utilisateurs.</p>
                
                <h3>Comité d'éthique</h3>
                <p>Un comité d'éthique composé de professionnels de santé veille au respect des principes déontologiques dans l'évolution de la plateforme et peut être consulté en cas de question éthique relative à son utilisation.</p>
            </div>
            
            <div class="back-buttons">
                <button class="action-button" onclick="showSection('banner')">
                    <i class="fas fa-arrow-left"></i> Retour aux choix
                </button>
                <a href="docInscrire.php" class="action-button">
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
                <a href="docTermesprivacy.php">Mentions légales</a>
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
            if (hash === 'terms' || hash === 'privacy' || hash === 'deontology') {
                showSection(hash + '-section');
            } else {
                showSection('banner');
            }
        });
    </script>
</body>
</html>