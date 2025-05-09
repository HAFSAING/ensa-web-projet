<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStatView - Pharmacies</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
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

        /* Header styles */
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

        /* Main content */
        .main-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            text-align: center;
        }

        /* Pharmacy search section */
        .pharmacy-search {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--primary-color);
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .use-location-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--secondary-color);
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .use-location-btn:hover {
            text-decoration: underline;
        }

        /* Filter section */
        .filter-section {
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .filter-label {
            font-weight: 500;
            color: var(--primary-color);
        }

        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .filter-btn {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            border-color: var(--accent-color1);
        }

        .filter-btn.active {
            background-color: var(--accent-color1);
            color: white;
            border-color: var(--accent-color1);
        }

        /* Map and list layout */
        .pharmacy-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 1.5rem;
        }

        /* Pharmacy list */
        .pharmacy-list {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            max-height: 600px;
            overflow-y: auto;
        }

        .pharmacy-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pharmacy-item:last-child {
            border-bottom: none;
        }

        .pharmacy-item:hover {
            background-color: rgba(123, 186, 154, 0.1);
        }

        .pharmacy-item.active {
            background-color: rgba(123, 186, 154, 0.2);
            border-left: 3px solid var(--accent-color1);
        }

        .pharmacy-name {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }

        .pharmacy-info {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .pharmacy-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .badge-day {
            background-color: #e3f2fd;
            color: #0d47a1;
        }

        .badge-night {
            background-color: #e8eaf6;
            color: #303f9f;
        }

        .badge-24h {
            background-color: #e0f2f1;
            color: #00695c;
        }

        /* Map container */
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            height: 600px;
        }

        #map {
            height: 100%;
            width: 100%;
        }

        /* No results message */
        .no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .pharmacy-content {
                grid-template-columns: 1fr;
            }
            
            .map-container {
                height: 400px;
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .form-group {
                min-width: 100%;
            }
            
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
            
            .nav-link {
                padding: 0.5rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .nav-link i {
                font-size: 1.1rem;
            }
        }

        /* Loader */
        .loader-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--secondary-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Popup styles */
        .custom-popup {
            max-width: 300px;
        }

        .popup-name {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .popup-info {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .popup-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .popup-actions {
            margin-top: 10px;
            display: flex;
            gap: 5px;
        }

        .popup-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            flex: 1;
        }

        .popup-btn-primary {
            background-color: var(--accent-color1);
            color: white;
        }

        .popup-btn-secondary {
            background-color: var(--accent-color2);
            color: white;
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
                            <a href="docFilterMedcin.php" class="nav-link ">
                                <i class="fas fa-user-md"></i>
                                <span>Médecin</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="userPharmacie.php" class="nav-link active" >
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

    <div class="main-content">
        <h1 class="page-title">Trouver une pharmacie près de chez vous</h1>
        
        <!-- Section de recherche de pharmacie -->
        <div class="pharmacy-search">
            <form id="search-form" class="search-form">
                <div class="form-group">
                    <label for="address">Adresse ou ville</label>
                    <input type="text" id="address" placeholder="Entrez votre adresse" required>
                    <button type="button" id="use-location" class="use-location-btn">
                        <i class="fas fa-location-arrow"></i> Utiliser ma position actuelle
                    </button>
                </div>
                <div class="form-group">
                    <label for="radius">Rayon de recherche</label>
                    <select id="radius">
                        <option value="1">1 km</option>
                        <option value="2" selected>2 km</option>
                        <option value="5">5 km</option>
                        <option value="10">10 km</option>
                        <option value="20">20 km</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>
        </div>
        
        <!-- Section des filtres -->
        <div class="filter-section">
            <span class="filter-label">Filtrer par type de garde:</span>
            <div class="filter-options">
                <button class="filter-btn active" data-filter="all">Toutes</button>
                <button class="filter-btn" data-filter="day">Garde de jour</button>
                <button class="filter-btn" data-filter="night">Garde de nuit</button>
                <button class="filter-btn" data-filter="24h">Ouvert 24h/24</button>
            </div>
        </div>
        
        <!-- Contenu des pharmacies (liste + carte) -->
        <div class="pharmacy-content">
            <!-- Liste des pharmacies -->
            <div class="pharmacy-list" id="pharmacy-list">
                <div class="no-results">
                    <i class="fas fa-search fa-2x"></i>
                    <p>Recherchez des pharmacies pour voir les résultats</p>
                </div>
            </div>
            
            <!-- Carte -->
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </div>
    
    <!-- Loader -->
    <div class="loader-container" id="loader">
        <div class="loader"></div>
    </div>



       <!-- Footer avec Google Maps et informations de contact -->
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


    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de la carte
            const map = L.map('map').setView([33.5731, -7.5898], 13); // Casablanca comme position par défaut
            
            // Ajout de la couche OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Variables globales
            let markers = [];
            let currentPositionMarker = null;
            let pharmacies = [];
            let currentFilter = 'all';
            
            // Utiliser la position actuelle
            document.getElementById('use-location').addEventListener('click', function() {
                if (navigator.geolocation) {
                    document.getElementById('loader').style.display = 'flex';
                    
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            // Utiliser l'API de géocodage inversé
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('address').value = data.display_name;
                                    
                                    // Centrer la carte
                                    map.setView([lat, lng], 14);
                                    
                                    // Ajouter un marqueur pour la position actuelle
                                    if (currentPositionMarker) {
                                        map.removeLayer(currentPositionMarker);
                                    }
                                    
                                    currentPositionMarker = L.marker([lat, lng], {
                                        icon: L.divIcon({
                                            className: 'current-position-marker',
                                            html: '<i class="fas fa-map-marker-alt" style="color: #CC0000; font-size: 24px;"></i>',
                                            iconSize: [24, 24],
                                            iconAnchor: [12, 24]
                                        })
                                    }).addTo(map).bindPopup('Votre position actuelle');
                                    
                                    document.getElementById('loader').style.display = 'none';
                                })
                                .catch(error => {
                                    console.error('Erreur lors du géocodage inversé:', error);
                                    document.getElementById('loader').style.display = 'none';
                                    alert('Impossible de déterminer votre adresse. Veuillez l\'entrer manuellement.');
                                });
                        },
                        function(error) {
                            document.getElementById('loader').style.display = 'none';
                            let errorMsg = 'Erreur inconnue';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMsg = "Vous avez refusé l'accès à votre position.";
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMsg = "Votre position n'est pas disponible.";
                                    break;
                                case error.TIMEOUT:
                                    errorMsg = "La demande de position a expiré.";
                                    break;
                            }
                            alert(errorMsg);
                        }
                    );
                } else {
                    alert("La géolocalisation n'est pas prise en charge par votre navigateur.");
                }
            });
            
            // Recherche de pharmacies
            document.getElementById('search-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const address = document.getElementById('address').value;
                const radius = document.getElementById('radius').value;
                
                if (!address) {
                    alert('Veuillez entrer une adresse');
                    return;
                }
                
                searchPharmacies(address, radius);
            });
            
            // Fonction de recherche de pharmacies
            function searchPharmacies(address, radius) {
                document.getElementById('loader').style.display = 'flex';
                
                // Géocoder l'adresse en utilisant Nominatim (OpenStreetMap)
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            throw new Error('Adresse non trouvée');
                        }
                        
                        const location = data[0];
                        const lat = parseFloat(location.lat);
                        const lon = parseFloat(location.lon);
                        
                        // Centrer la carte
                        map.setView([lat, lon], 14);
                        
                        // Ajouter un marqueur pour la position recherchée
                        if (currentPositionMarker) {
                            map.removeLayer(currentPositionMarker);
                        }
                        
                        currentPositionMarker = L.marker([lat, lon], {
                            icon: L.divIcon({
                                className: 'current-position-marker',
                                html: '<i class="fas fa-map-marker-alt" style="color: #CC0000; font-size: 24px;"></i>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 24]
                            })
                        }).addTo(map).bindPopup('Position recherchée');
                        
                        // Rechercher les pharmacies à proximité en utilisant l'API Overpass (OpenStreetMap)
                        const overpassQuery = `
                            [out:json];
                            (
                              node["amenity"="pharmacy"](around:${radius * 1000},${lat},${lon});
                              way["amenity"="pharmacy"](around:${radius * 1000},${lat},${lon});
                              relation["amenity"="pharmacy"](around:${radius * 1000},${lat},${lon});
                            );
                            out center;
                        `;
                        
                        return fetch('https://overpass-api.de/api/interpreter', {
                            method: 'POST',
                            body: overpassQuery
                        });
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Effacer les marqueurs existants
                        clearMarkers();
                        
                        // Générer des données aléatoires pour le type de garde des pharmacies
                        pharmacies = data.elements.map(element => {
                            const guardTypes = ['day', 'night', '24h'];
                            const randomGuardType = guardTypes[Math.floor(Math.random() * guardTypes.length)];
                            
                            let lat, lon, name = 'Pharmacie', opening_hours = '', phone = '';
                            
                            if (element.type === 'node') {
                                lat = element.lat;
                                lon = element.lon;
                            } else if (element.center) {
                                lat = element.center.lat;
                                lon = element.center.lon;
                            }
                            
                            if (element.tags) {
                                name = element.tags.name || 'Pharmacie';
                                opening_hours = element.tags.opening_hours || 'Horaires non spécifiés';
                                phone = element.tags.phone || element.tags['contact:phone'] || 'Non spécifié';
                            }
                            
                            return {
                                id: element.id,
                                name: name,
                                lat: lat,
                                lon: lon,
                                guardType: randomGuardType,
                                address: element.tags?.['addr:street'] ? 
                                    `${element.tags['addr:housenumber'] || ''} ${element.tags['addr:street']}` : 
                                    'Adresse non spécifiée',
                                openingHours: opening_hours,
                                phone: phone
                            };
                        }).filter(pharmacy => pharmacy.lat && pharmacy.lon); // Filtrer les pharmacies sans coordonnées
                        
                        // Afficher les pharmacies
                        displayPharmacies(pharmacies);
                        
                        document.getElementById('loader').style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                        document.getElementById('loader').style.display = 'none';
                        alert('Erreur lors de la recherche: ' + error.message);
                    });
            }
            
            // Fonction pour effacer les marqueurs existants
            function clearMarkers() {
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];
                document.getElementById('pharmacy-list').innerHTML = '';
            }
            
            // Fonction pour afficher les pharmacies
            function displayPharmacies(pharmacies) {
                // Filtrer les pharmacies selon le filtre actuel
                const filteredPharmacies = currentFilter === 'all' ? 
                    pharmacies : 
                    pharmacies.filter(pharmacy => pharmacy.guardType === currentFilter);
                
                const listContainer = document.getElementById('pharmacy-list');
                
                if (filteredPharmacies.length === 0) {
                    listContainer.innerHTML = `
                        <div class="no-results">
                            <i class="fas fa-pills fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>
                            <p>Aucune pharmacie trouvée avec ce filtre</p>
                        </div>
                    `;
                    return;
                }
                
                // Vider la liste actuelle
                listContainer.innerHTML = '';
                
                // Ajouter les pharmacies à la liste et à la carte
                filteredPharmacies.forEach((pharmacy, index) => {
                    // Créer l'élément de liste
                    const pharmacyElement = document.createElement('div');
                    pharmacyElement.className = 'pharmacy-item';
                    pharmacyElement.dataset.id = pharmacy.id;
                    
                    // Badge selon le type de garde
                    let badgeClass = '';
                    let badgeText = '';
                    
                    switch(pharmacy.guardType) {
                        case 'day':
                            badgeClass = 'badge-day';
                            badgeText = 'Garde de jour';
                            break;
                        case 'night':
                            badgeClass = 'badge-night';
                            badgeText = 'Garde de nuit';
                            break;
                        case '24h':
                            badgeClass = 'badge-24h';
                            badgeText = 'Ouvert 24h/24';
                            break;
                    }
                    
                    pharmacyElement.innerHTML = `
                        <div class="pharmacy-name">${pharmacy.name}</div>
                        <div class="pharmacy-info"><i class="fas fa-map-marker-alt"></i> ${pharmacy.address}</div>
                        <div class="pharmacy-info"><i class="fas fa-clock"></i> ${pharmacy.openingHours}</div>
                        <div class="pharmacy-info"><i class="fas fa-phone"></i> ${pharmacy.phone}</div>
                        <span class="pharmacy-badge ${badgeClass}">${badgeText}</span>
                    `;
                    
                    // Ajouter un événement de clic
                    pharmacyElement.addEventListener('click', function() {
                        // Supprimez la classe active de tous les éléments
                        document.querySelectorAll('.pharmacy-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        
                        // Ajoutez la classe active à l'élément cliqué
                        this.classList.add('active');
                        
                        // Centrez la carte sur la pharmacie
                        map.setView([pharmacy.lat, pharmacy.lon], 16);
                        
                        // Ouvrez le popup correspondant
                        markers[index].openPopup();
                    });
                    
                    listContainer.appendChild(pharmacyElement);
                    
                    // Icône selon le type de garde
                    let iconColor = '#1d566b'; // Couleur par défaut
                    
                    switch(pharmacy.guardType) {
                        case 'day':
                            iconColor = '#0d47a1'; // Bleu pour garde de jour
                            break;
                        case 'night':
                            iconColor = '#303f9f'; // Bleu foncé pour garde de nuit
                            break;
                        case '24h':
                            iconColor = '#00695c'; // Vert foncé pour 24h/24
                            break;
                    }
                    
                    // Créer le marqueur avec une icône personnalisée
                    const marker = L.marker([pharmacy.lat, pharmacy.lon], {
                        icon: L.divIcon({
                            className: 'pharmacy-marker',
                            html: `<i class="fas fa-pills" style="color: ${iconColor}; font-size: 20px;"></i>`,
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    });
                    
                    // Créer le contenu du popup
                    const popupContent = `
                        <div class="custom-popup">
                            <div class="popup-name">${pharmacy.name}</div>
                            <div class="popup-info"><i class="fas fa-map-marker-alt"></i> ${pharmacy.address}</div>
                            <div class="popup-info"><i class="fas fa-clock"></i> ${pharmacy.openingHours}</div>
                            <div class="popup-info"><i class="fas fa-phone"></i> ${pharmacy.phone}</div>
                            <div class="popup-badge ${badgeClass}">${badgeText}</div>
                            <div class="popup-actions">
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${pharmacy.lat},${pharmacy.lon}" target="_blank" class="popup-btn popup-btn-primary">Itinéraire</a>
                                <a href="tel:${pharmacy.phone.replace(/\s/g, '')}" class="popup-btn popup-btn-secondary">Appeler</a>
                            </div>
                        </div>
                    `;
                    
                    // Ajouter le popup au marqueur
                    marker.bindPopup(popupContent);
                    
                    // Ajouter le marqueur à la carte et au tableau des marqueurs
                    marker.addTo(map);
                    markers.push(marker);
                });
            }
            
            // Gestion des filtres
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Mettre à jour le filtre actif visuellement
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Mettre à jour le filtre actuel
                    currentFilter = this.dataset.filter;
                    
                    // Réafficher les pharmacies avec le nouveau filtre
                    if (pharmacies.length > 0) {
                        displayPharmacies(pharmacies);
                    }
                });
            });
            
            // Fonction pour simuler des données de pharmacies réelles
            function generateRealPharmaciesData(lat, lng, count) {
                const pharmacies = [];
                const guardTypes = ['day', 'night', '24h'];
                const pharmacyNames = [
                    "Pharmacie Centrale", "Pharmacie du Marché", "Pharmacie Principale", 
                    "Pharmacie de la Gare", "Pharmacie des Alpes", "Pharmacie du Soleil", 
                    "Pharmacie de l'Avenir", "Pharmacie de la Liberté", "Pharmacie du Parc",
                    "Pharmacie du Centre", "Pharmacie Moderne", "Pharmacie de la Place"
                ];
                
                for (let i = 0; i < count; i++) {
                    // Générer des coordonnées aléatoires dans un rayon donné
                    const radius = Math.random() * 0.01; // environ 1km
                    const angle = Math.random() * Math.PI * 2;
                    const offsetLat = radius * Math.cos(angle);
                    const offsetLng = radius * Math.sin(angle);
                    
                    const guardType = guardTypes[Math.floor(Math.random() * guardTypes.length)];
                    const name = pharmacyNames[Math.floor(Math.random() * pharmacyNames.length)];
                    
                    pharmacies.push({
                        id: 'ph_' + i,
                        name: name,
                        lat: lat + offsetLat,
                        lng: lng + offsetLng,
                        guardType: guardType,
                        address: "123 Rue Example",
                        openingHours: guardType === '24h' ? "24h/24" : (guardType === 'day' ? "8h-19h" : "19h-8h"),
                        phone: "05" + Math.floor(Math.random() * 90000000 + 10000000)
                    });
                }
                
                return pharmacies;
            }
            
            // Fonction pour obtenir les pharmacies depuis une API extérieure
            // Note: Cette fonction peut être utilisée si vous avez accès à une API de pharmacies
            function getPharmaciesFromAPI(lat, lng, radius) {
                return new Promise((resolve, reject) => {
                    // URL de l'API (à remplacer par une API réelle)
                    const apiUrl = `https://api.exemple.com/pharmacies?lat=${lat}&lng=${lng}&radius=${radius}`;
                    
                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            // Traiter les données de l'API
                            const pharmacies = data.results.map(item => ({
                                id: item.id,
                                name: item.name,
                                lat: item.geometry.location.lat,
                                lng: item.geometry.location.lng,
                                guardType: determineGuardType(item),
                                address: item.vicinity,
                                openingHours: item.opening_hours ? (item.opening_hours.open_now ? "Ouvert maintenant" : "Fermé") : "Horaires non disponibles",
                                phone: item.phone || "Non disponible"
                            }));
                            
                            resolve(pharmacies);
                        })
                        .catch(error => {
                            console.error("Erreur API:", error);
                            // En cas d'erreur, générer des données fictives
                            const fakePharmacies = generateRealPharmaciesData(lat, lng, 10);
                            resolve(fakePharmacies);
                        });
                });
            }
            
            // Fonction pour déterminer le type de garde basé sur les données de l'API
            function determineGuardType(apiData) {
                // Cette fonction peut être adaptée selon le format des données de votre API
                if (apiData.opening_hours && apiData.opening_hours.periods) {
                    // Vérifier si la pharmacie est ouverte 24h/24
                    const has24hPeriod = apiData.opening_hours.periods.some(period => 
                        period.open.time === "0000" && period.close.time === "0000");
                    
                    if (has24hPeriod) return '24h';
                    
                    // Vérifier les heures d'ouverture pour déterminer si c'est une garde de jour ou de nuit
                    const hasNightHours = apiData.opening_hours.periods.some(period => 
                        parseInt(period.open.time) >= 1900 || parseInt(period.close.time) <= 800);
                    
                    return hasNightHours ? 'night' : 'day';
                }
                
                // Par défaut, retourner un type aléatoire
                const types = ['day', 'night', '24h'];
                return types[Math.floor(Math.random() * types.length)];
            }
            
            // Fournir un exemple initial (optionnel)
            // Vous pouvez décommenter cette section pour avoir des données d'exemple au chargement
            /*
            setTimeout(() => {
                document.getElementById('address').value = "Casablanca, Maroc";
                document.getElementById('search-form').dispatchEvent(new Event('submit'));
            }, 1000);
            */
        });
    </script>
</body>
</html>