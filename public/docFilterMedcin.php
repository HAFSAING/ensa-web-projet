<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStatView - Recherche de Médecins</title>
    <style>
        :root {
            --primary-color: #1d566b;
            --secondary-color: #216b4e;
            --accent-color1: #7bba9a;
            --accent-color2: #86b3c3;
            --accent-color3: #CC0000;
            --yellow-color: #FFBF00;
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
        }
        
        /* Header Styles */
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

        /* Page de médecins */
        .page-title {
            background-color: var(--light-bg);
            padding: 1rem 0;
            border-bottom: 3px solid #ffbf00;
        }

        .title-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .title-container h1 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: bold;
            position: relative;
            padding-bottom: 0.5rem;
        }

        /* Layout principal */
        .main-layout {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            gap: 2rem;
            padding: 0 1rem;
        }

        /* Sidebar de filtres */
        .sidebar {
            width: 320px;
            flex-shrink: 0;
        }

        .filter-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-card h2 {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color1);
        }

        /* Inputs de recherche */
        .search-input {
            position: relative;
            margin-bottom: 1.2rem;
        }

        .search-input input {
            width: 100%;
            padding: 0.8rem 2.8rem 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
        }

        .search-input button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 50px;
            background-color: var(--yellow-color);
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }

        .search-input button:hover {
            background-color: #e6ac00;
        }

        /* Selects */
        .select-container {
            margin-bottom: 1.2rem;
        }

        .select-container label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .custom-select {
            position: relative;
            width: 100%;
        }

        .custom-select select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            appearance: none;
            background-color: white;
            cursor: pointer;
        }

        .custom-select::after {
            content: '▼';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--primary-color);
        }

        /* Checkboxes personnalisées */
        .checkbox-container {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 0.8rem;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-container label {
            cursor: pointer;
        }

        /* Bouton de recherche */
        .search-button {
            width: 100%;
            padding: 1rem;
            background-color: var(--yellow-color);
            color: var(--text-dark);
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .search-button:hover {
            background-color: #e6ac00;
            transform: translateY(-2px);
        }

        .search-button i {
            font-size: 1.2rem;
        }

        /* Zone de contenu principal */
        .content {
            flex-grow: 1;
        }

        /* Cards des médecins */
        .doctor-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .doctor-card-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .doctor-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-color2);
        }

        .doctor-info h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 0.3rem;
        }

        .doctor-specialty {
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .doctor-location {
            display: flex;
            align-items: center;
            color: #555;
            gap: 0.5rem;
        }

        .doctor-location i {
            color: var(--accent-color1);
        }

        .doctor-card-body {
            padding: 1.5rem;
        }

        .doctor-specialties {
            margin-bottom: 1.2rem;
        }

        .specialties-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .specialty-item {
            background-color: rgba(123, 186, 154, 0.1);
            color: var(--secondary-color);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .specialty-item i {
            color: var(--accent-color1);
            font-size: 0.8rem;
        }

        .services-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
            margin-top: 0.8rem;
        }

        .service-item {
            background-color: #f0f0f0;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .appointment-button {
            display: block;
            text-align: center;
            padding: 0.8rem 1.5rem;
            background-color: var(--yellow-color);
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            margin-left: auto;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: fit-content;
        }

        .appointment-button:hover {
            background-color: #e6ac00;
            transform: translateY(-2px);
        }

        .appointment-button i {
            font-size: 1.2rem;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .main-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                padding: 1rem 0;
            }
            
            .main-nav {
                order: 3;
                width: 100%;
                margin-top: 1rem;
            }
            
            .nav-list {
                justify-content: space-around;
                width: 100%;
            }
            
            .doctor-card-header {
                flex-direction: column;
                text-align: center;
            }
            
            .doctor-location {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .doctor-card-body {
                padding: 1rem;
            }
            
            .specialty-item, .service-item {
                font-size: 0.8rem;
            }
            
            .appointment-button {
                width: 100%;
            }
        }
        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 4rem 2rem 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-column h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--accent-color1);
        }

        .footer-column p {
            color: #ccc;
            margin-bottom: 1.2rem;
            line-height: 1.6;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--accent-color1);
            transform: translateX(5px);
        }

        .footer-contact p {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }

        .contact-icon {
            margin-right: 0.8rem;
            color: var(--accent-color1);
            display: inline-flex;
            width: 24px;
            justify-content: center;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: #fff;
            text-decoration: none;
        }

        .social-icon:hover {
            background-color: var(--accent-color1);
            transform: translateY(-3px);
        }

        .google-map {
            width: 100%;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #ccc;
        }

        .legal-links {
            margin-top: 1rem;
        }

        .legal-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.9rem;
        }

        .legal-links a:hover {
            color: var(--accent-color1);
        }

        /* Responsive design pour le footer */
        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
            
            .footer-column.footer-map {
                grid-column: span 2;
            }
        }

        @media (max-width: 576px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .footer-column.footer-map {
                grid-column: span 1;
            }
            
            .legal-links {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
        }
    </style>
</head>
<body>
    <!-- Header avec navigation et icônes -->
    <header>
        <div class="container">
            <div class="header-content">
                <svg width="180" height="50" viewBox="0 0 180 50">
                    <rect x="10" y="15" width="20" height="20" fill="#77c4a0" />
                    <polygon points="30,15 40,25 30,35" fill="#9fdec0" />
                    <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                    <text x="50" y="40" fill="#9fdec0" font-size="12">SERVICES</text>
                </svg>

                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <i class="fas fa-home"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docFilterMedcin.php" class="nav-link active">
                                <i class="fas fa-user-md"></i>
                                <span>Médecin</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="userPharmacie.php" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Pharmacie</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="medicaments.php" class="nav-link">
                                <i class="fas fa-capsules"></i>
                                <span>Médicaments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Questions.php" class="nav-link">
                                <i class="fas fa-question-circle"></i>
                                <span>Questions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Magazine.php" class="nav-link">
                                <i class="fas fa-book-medical"></i>
                                <span>Magazine</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Titre de la page -->
    <div class="page-title">
        <div class="title-container">
            <h1>Médecin</h1>
        </div>
    </div>

    <!-- Layout principal -->
    <div class="main-layout">
        <!-- Sidebar avec filtres -->
        <aside class="sidebar">
            <div class="filter-card">
                <h2>Filtrer par</h2>
                
                <!-- Recherche par nom -->
                <div class="search-input">
                    <input type="text" placeholder="Nom du professionnel de santé">
                    <button>OK</button>
                </div>

                <!-- Sélection de spécialité -->
                <div class="select-container">
                    <label for="specialite">Spécialité</label>
                    <div class="custom-select">
                        <select id="specialite">
                            <option value="">Toutes les spécialités</option>
                            <option value="1">Cardiologie</option>
                            <option value="2">Dermatologie</option>
                            <option value="3">Gastro-entérologie</option>
                            <option value="4">Gynécologie</option>
                            <option value="5">Neurologie</option>
                            <option value="6">Ophtalmologie</option>
                            <option value="7">ORL</option>
                            <option value="8">Pédiatrie</option>
                            <option value="9">Psychiatrie</option>
                            <option value="10">Rhumatologie</option>
                        </select>
                    </div>
                </div>

                <!-- Sélection de pays -->
                <div class="select-container">
                    <label for="pays">Pays</label>
                    <div class="custom-select">
                        <select id="pays">
                            <option value="maroc">Maroc</option>
                            <option value="france">France</option>
                            <option value="belgique">Belgique</option>
                            <option value="suisse">Suisse</option>
                            <option value="canada">Canada</option>
                        </select>
                    </div>
                </div>

                <!-- Sélection de ville -->
                <div class="select-container">
                    <label for="ville">Ville</label>
                    <div class="custom-select">
                        <select id="ville">
                            <option value="">Toutes les villes</option>
                            <option value="casablanca">Casablanca</option>
                            <option value="rabat">Rabat</option>
                            <option value="marrakech">Marrakech</option>
                            <option value="fes">Fès</option>
                            <option value="tanger">Tanger</option>
                            <option value="agadir">Agadir</option>
                            <option value="oujda">Oujda</option>
                        </select>
                    </div>
                </div>

                <!-- Sélection de genre -->
                <div class="select-container">
                    <label for="genre">Genre</label>
                    <div class="custom-select">
                        <select id="genre">
                            <option value="">Tous</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>
                </div>

                <!-- Sélection de langue parlée -->
                <div class="select-container">
                    <label for="langue">Langue parlée</label>
                    <div class="custom-select">
                        <select id="langue">
                            <option value="">Toutes les langues</option>
                            <option value="francais">Français</option>
                            <option value="arabe">Arabe</option>
                            <option value="anglais">Anglais</option>
                            <option value="espagnol">Espagnol</option>
                        </select>
                    </div>
                </div>

                <!-- Options supplémentaires -->
                <div class="checkbox-container">
                    <input type="checkbox" id="visite">
                    <label for="visite">Visite à domicile</label>
                </div>

                <div class="checkbox-container">
                    <input type="checkbox" id="garde">
                    <label for="garde">Services de garde 24/7</label>
                </div>

                <!-- Bouton de recherche -->
                <button class="search-button">
                    <i class="fas fa-search"></i>
                    RECHERCHER
                </button>
            </div>
        </aside>

        <!-- Contenu principal -->
        <main class="content">
            <!-- Carte du premier médecin -->
            <div class="doctor-card">
                <div class="doctor-card-header">
                    <img src="/api/placeholder/100/100" alt="Dr Hasna EL FAIZ" class="doctor-image">
                    <div class="doctor-info">
                        <h3>Dr Hasna EL FAIZ</h3>
                        <div class="doctor-specialty">Gynécologue Obstétricien</div>
                        <div class="doctor-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Agadir Principal Agadir Maroc</span>
                        </div>
                    </div>
                </div>
                <div class="doctor-card-body">
                    <div class="doctor-specialties">
                        <div class="specialties-list">
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Médecin Spécialiste En Gynécologie - Obstétrique</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Accouchement</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Chirurgie Gynécologique</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Maladies des seins</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Stérilité du Couple</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Échographie</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Cœlioscopie</div>
                        </div>
                    </div>
                    <div class="services-list">
                        <div class="service-item">Amplification du point G</div>
                        <div class="service-item">Sécrétions vaginales</div>
                        <div class="service-item">Frottis cervico-vaginal</div>
                        <div class="service-item">Accouchement Normal et Césarienne</div>
                        <div class="service-item">L'obstétrique</div>
                    </div>
                    <button class="appointment-button">
                        <i class="far fa-calendar-alt"></i>
                        Prendre Rendez-vous
                    </button>
                </div>
            </div>

            <!-- Carte du deuxième médecin -->
            <div class="doctor-card">
                <div class="doctor-card-header">
                    <img src="/api/placeholder/100/100" alt="Dr Benabdellah NAWAL" class="doctor-image">
                    <div class="doctor-info">
                        <h3>Dr Benabdellah NAWAL</h3>
                        <div class="doctor-specialty">Néphrologue</div>
                        <div class="doctor-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Centre ville Oujda Maroc</span>
                        </div>
                    </div>
                </div>
                <div class="doctor-card-body">
                    <div class="doctor-specialties">
                        <div class="specialties-list">
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Spécialiste en NÉPHROLOGIE DIALYSE ET TRANSPLANTATION RÉNALE</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Ancien interne des hôpitaux de Paris</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Diplômée en formation médicale spécialisée au CHU Pitié salpêtrière France</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Diplômée de l'université de Paris Descartes</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Diplômée de l'université de Strasbourg</div>
                            <div class="specialty-item"><i class="fas fa-check-circle"></i> Néphrologie clinique et Prise en charge des lithiases</div>
                        </div>
                    </div>
                    <div class="services-list">
                        <div class="service-item">Ponction biopsie rénale</div>
                        <div class="service-item">Prise en charge lithiase rénale</div>
                        <div class="service-item">Traitement médical et préventif des lithiases rénale</div>
                    </div>
                    <button class="appointment-button">
                        <i class="far fa-calendar-alt"></i>
                        Prendre Rendez-vous
                    </button>
                </div>
            </div>
            
            <!-- Ajoutez d'autres cartes de médecins ici si nécessaire -->
        </main>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>MediStatView</h3>
                <p>Votre plateforme de santé connectée pour un suivi médical optimal en toute sécurité.</p>
                <div class="social-links">
                    <a href="#" class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-column footer-links-column">
                <h3>Liens Rapides</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="#features">Nos Services</a></li>
                    <li><a href="#access-cards">Espaces Personnalisés</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Actualités Santé</a></li>
                    <li><a href="#">À Propos</a></li>
                </ul>
            </div>
            
            <div class="footer-column footer-contact">
                <h3>Contact</h3>
                <p><span class="contact-icon">📍</span> 123 Avenue de la Santé, 75001 casa</p>
                <p><span class="contact-icon">📞</span> +212 5 23 45 67 89</p>
                <p><span class="contact-icon">✉️</span> contact@gmail.com</p>
                <p><span class="contact-icon">🕒</span> Lun - Ven: 9h00 - 18h00</p>
            </div>
            
            <div class="footer-column footer-map">
                <h3>Nous Trouver</h3>
                <div class="google-map">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.142047342751!2d2.3345!3d48.8608!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDjCsDA5JzUxLjgiTiAywrAyMCcwNi42IkU!5e0!3m2!1sfr!2sfr!4v1651234567890!5m2!1sfr!2sfr" 
                        width="100%" 
                        height="200" 
                        style="border:0; border-radius:8px;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; 2025 MediStatView. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Ajouter Font Awesome pour les icônes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>