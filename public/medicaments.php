<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStatView - Médicaments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Main content area */
        .main-content {
            padding: 2rem 0;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            position: relative;
            font-size: 2.2rem;
            font-weight: 700;
        }

        .page-title::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background-color: var(--accent-color1);
            margin: 0.8rem auto 0;
            border-radius: 2px;
        }

        /* Search and filter section */
        .search-section {
            background-color: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-title {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.4rem;
        }

        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(33, 107, 78, 0.2);
        }

        .search-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            justify-content: flex-end;
        }

        /* Medication cards section */
        .medications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 0 auto;
            max-width: 1200px;
            padding: 0 1rem;
        }

        .medication-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .medication-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .medication-header {
            padding: 1rem;
            background-color: var(--accent-color2);
            color: var(--text-light);
            position: relative;
        }

        .medication-name {
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .medication-maker {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .medication-body {
            padding: 1.5rem;
        }

        .medication-info {
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            margin-bottom: 0.7rem;
            align-items: flex-start;
        }

        .info-label {
            font-weight: 600;
            min-width: 120px;
            color: var(--primary-color);
        }

        .medication-price {
            background-color: var(--light-bg);
            padding: 0.7rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .price-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .availability {
            font-size: 0.9rem;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .in-stock {
            background-color: #d4edda;
            color: #155724;
        }

        .low-stock {
            background-color: #fff3cd;
            color: #856404;
        }

        .out-of-stock {
            background-color: #f8d7da;
            color: #721c24;
        }

        .medication-footer {
            padding: 1rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 0.7rem 1.4rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
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
            border: 2px solid var(--accent-color2);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--accent-color2);
            color: white;
            transform: translateY(-2px);
        }

        /* Flag icons for country selection */
        .flag-icon {
            width: 20px;
            height: 15px;
            margin-right: 8px;
            vertical-align: middle;
            display: inline-block;
            background-size: cover;
        }

        /* Loading spinner */
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px;
        }

        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--accent-color1);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* No results message */
        .no-results {
            text-align: center;
            padding: 3rem;
            font-size: 1.2rem;
            color: #666;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }

        .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            background-color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pagination-btn:hover {
            background-color: var(--accent-color2);
            color: white;
            border-color: var(--accent-color2);
        }

        .pagination-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 4rem 2rem 2rem;
            margin-top: 3rem;
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

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #ccc;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: modalFadeIn 0.3s;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
            color: #777;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .close-modal:hover {
            background-color: #f0f0f0;
        }
        
        .modal-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color1);
        }

        .modal-body h3 {
            color: var(--secondary-color);
            margin: 1.5rem 0 0.5rem;
        }

        .modal-body ul {
            padding-left: 1.5rem;
        }

        .modal-body p {
            margin-bottom: 1rem;
        }

        .medication-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 0.5rem 0;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
            
            .search-buttons {
                justify-content: center;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .medication-footer {
                flex-direction: column;
                gap: 0.8rem;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php">
                    <svg width="180" height="50" viewBox="0 0 180 50">
                        <rect x="10" y="15" width="20" height="20" fill="#77c4a0" />
                        <polygon points="30,15 40,25 30,35" fill="#9fdec0" />
                        <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                        <text x="50" y="40" fill="#9fdec0" font-size="12">SERVICES</text>
                    </svg>
                </a>

                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="docFilterMedcin.php" class="nav-link">
                                <i class="fas fa-user-md"></i>
                                <span>Médecin</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Pharmacie</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="userMedicaments.php" class="nav-link active">
                                <i class="fas fa-capsules"></i>
                                <span>Médicaments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-question-circle"></i>
                                <span>Questions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
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
        <h1 class="page-title">Base de données médicaments</h1>
        
        <!-- Search and Filter Section -->
        <section class="search-section">
            <h2 class="search-title">Filtrer par</h2>
            <form class="search-form" id="search-form">
                <div class="form-group">
                    <label for="medication-name">Nom du médicament</label>
                    <input type="text" id="medication-name" class="form-control" placeholder="Rechercher un médicament...">
                </div>
                
                <div class="form-group">
                    <label for="pharmaceutical-form">Forme pharmaceutique</label>
                    <select id="pharmaceutical-form" class="form-control">
                        <option value="">Toutes les formes</option>
                        <!-- Ces options seront remplies dynamiquement par JavaScript -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="presentation">Présentation</label>
                    <select id="presentation" class="form-control">
                        <option value="">Toutes les présentations</option>
                        <!-- Ces options seront remplies dynamiquement par JavaScript -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price-range">Plage de prix (DH)</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="number" id="price-min" class="form-control" placeholder="Min" min="0">
                        <span>-</span>
                        <input type="number" id="price-max" class="form-control" placeholder="Max" min="0">
                    </div>
                </div>
                
                <div class="search-buttons">
                    <button type="button" class="btn btn-outline" id="reset-button">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </section>
        
        <!-- Results Section -->
        <section class="medications-container">
            <div id="loading" class="loading-container">
                <div class="loading-spinner"></div>
            </div>
            
            <div id="no-results" class="no-results" style="display: none;">
                <i class="fas fa-search fa-3x" style="color: var(--accent-color2); margin-bottom: 1rem;"></i>
                <p>Aucun médicament trouvé. Veuillez modifier vos critères de recherche.</p>
            </div>
            
            <div class="medications-grid" id="medications-grid">
                <!-- Medication cards will be inserted here by JavaScript -->
            </div>
            
            <div class="pagination" id="pagination">
                <!-- Pagination buttons will be inserted here by JavaScript -->
            </div>
        </section>
    </div>
    
    <!-- Medication Detail Modal -->
    <div class="modal" id="medication-modal">
        <div class="modal-content">
            <button class="close-modal" id="close-modal">&times;</button>
            <h2 class="modal-title" id="modal-title">Détails du médicament</h2>
            <div class="modal-body" id="modal-body">
                <!-- Medication details will be inserted here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Footer -->
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
                    <li><a href="docFilterMedcin.php">Médecins</a></li>
                    <li><a href="userMedicaments.php">Médicaments</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Actualités Santé</a></li>
                    <li><a href="#">À Propos</a></li>
                </ul>
            </div>
            
            <div class="footer-column footer-contact">
                <h3>Contact</h3>
                <p><span class="contact-icon">📍</span> 123 Avenue de la Santé, 10000 Rabat, Maroc</p>
                <p><span class="contact-icon">📞</span> +212 5 23 45 67 89</p>
                <p><span class="contact-icon">✉️</span> contact@medistatview.ma</p>
                <p><span class="contact-icon">🕒</span> Lun - Ven: 9h00 - 18h00</p>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; 2025 MediStatView. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Inclusion de SheetJS (xlsx) pour le parsing du fichier Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- JavaScript pour l'API et l'interaction -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables pour la pagination
            let currentPage = 1;
            const itemsPerPage = 12;
            let allMedications = [];
            
            // Contrôles DOM
            const medicationsGrid = document.getElementById('medications-grid');
            const loadingElement = document.getElementById('loading');
            const noResultsElement = document.getElementById('no-results');
            const paginationElement = document.getElementById('pagination');
            const searchForm = document.getElementById('search-form');
            const resetButton = document.getElementById('reset-button');
            const formSelect = document.getElementById('pharmaceutical-form');
            const presentationSelect = document.getElementById('presentation');
            
            // Modal elements
            const medicationModal = document.getElementById('medication-modal');
            const closeModal = document.getElementById('close-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalBody = document.getElementById('modal-body');
            
            // Fonction pour charger les données XLSX
            async function loadMedicationData() {
                try {
                    const response = await fetch('medicaments_data.xlsx');
                    const arrayBuffer = await response.arrayBuffer();
                    const data = new Uint8Array(arrayBuffer);
                    const workbook = XLSX.read(data, { type: 'array' });
                    
                    // Récupérer la première feuille
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    
                    // Convertir en JSON
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    
                    // Traiter les données (ignorer la première ligne qui contient les en-têtes)
                    const headers = jsonData[0];
                    const medications = [];
                    
                    // Remplir les select de formulaire avec des valeurs uniques
                    const uniqueForms = new Set();
                    const uniquePresentations = new Set();
                    
                    for (let i = 1; i < jsonData.length; i++) {
                        const row = jsonData[i];
                        if (row.length < 4) continue; // Ignorer les lignes incomplètes
                        
                        const medication = {
                            id: i,
                            name: row[0] || 'Non spécifié',
                            form: row[1] || 'Non spécifié',
                            presentation: row[2] || 'Non spécifié',
                            price: parseFloat(row[3]) || 0,
                            stock: Math.floor(Math.random() * 3) // 0: out of stock, 1: low stock, 2: in stock
                        };
                        
                        medications.push(medication);
                        
                        // Ajouter aux ensembles uniques
                        if (medication.form) uniqueForms.add(medication.form);
                        if (medication.presentation) uniquePresentations.add(medication.presentation);
                    }
                    
                    // Remplir les select avec les valeurs uniques
                    fillSelectOptions(formSelect, Array.from(uniqueForms).sort());
                    fillSelectOptions(presentationSelect, Array.from(uniquePresentations).sort());
                    
                    return medications;
                } catch (error) {
                    console.error("Erreur lors du chargement des données :", error);
                    // En cas d'erreur, utiliser des données fictives
                    return generateMockMedications();
                }
            }
            
            // Remplir un élément select avec des options
            function fillSelectOptions(selectElement, options) {
                options.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option;
                    optionElement.textContent = option;
                    selectElement.appendChild(optionElement);
                });
            }
            
            // Données fictives au cas où le chargement du fichier XLSX échoue
            function generateMockMedications() {
                const medications = [];
                const drugNames = [
                    'Doliprane', 'Amoxicilline', 'Spasfon',



                    // Suite du script JavaScript précédent

            // Générer les cartes de médicaments
            function renderMedicationCards(medications) {
                medicationsGrid.innerHTML = '';
                
                if (medications.length === 0) {
                    noResultsElement.style.display = 'block';
                    paginationElement.innerHTML = '';
                    return;
                }
                
                noResultsElement.style.display = 'none';
                
                // Calculer les limites de la pagination
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, medications.length);
                const pageCount = Math.ceil(medications.length / itemsPerPage);
                
                // Afficher les médicaments pour la page actuelle
                for (let i = startIndex; i < endIndex; i++) {
                    const medication = medications[i];
                    const card = createMedicationCard(medication);
                    medicationsGrid.appendChild(card);
                }
                
                // Mettre à jour la pagination
                renderPagination(pageCount);
            }
            
            // Créer une carte de médicament
            function createMedicationCard(medication) {
                const card = document.createElement('div');
                card.className = 'medication-card';
                card.dataset.id = medication.id;
                
                // Déterminer le statut du stock
                let stockStatus = '';
                let stockClass = '';
                
                switch(medication.stock) {
                    case 0:
                        stockStatus = 'Rupture de stock';
                        stockClass = 'out-of-stock';
                        break;
                    case 1:
                        stockStatus = 'Stock limité';
                        stockClass = 'low-stock';
                        break;
                    default:
                        stockStatus = 'En stock';
                        stockClass = 'in-stock';
                }
                
                card.innerHTML = `
                    <div class="medication-header">
                        <h3 class="medication-name">${medication.name}</h3>
                        <p class="medication-maker">Laboratoire pharmaceutique</p>
                    </div>
                    <div class="medication-body">
                        <div class="medication-info">
                            <div class="info-row">
                                <span class="info-label">Forme:</span>
                                <span>${medication.form}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Présentation:</span>
                                <span>${medication.presentation}</span>
                            </div>
                        </div>
                        <div class="medication-price">
                            <span class="price-value">${medication.price.toFixed(2)} MAD</span>
                            <span class="availability ${stockClass}">${stockStatus}</span>
                        </div>
                    </div>
                    <div class="medication-footer">
                        <button class="btn btn-outline view-details" data-id="${medication.id}">Détails</button>
                        <button class="btn btn-primary">Commander</button>
                    </div>
                `;
                
                return card;
            }
            
            // Créer la pagination
            function renderPagination(pageCount) {
                paginationElement.innerHTML = '';
                
                if (pageCount <= 1) return;
                
                // Bouton précédent
                if (currentPage > 1) {
                    const prevBtn = document.createElement('button');
                    prevBtn.className = 'pagination-btn';
                    prevBtn.innerHTML = '&laquo;';
                    prevBtn.addEventListener('click', () => {
                        currentPage--;
                        renderMedicationCards(allMedications);
                    });
                    paginationElement.appendChild(prevBtn);
                }
                
                // Boutons de pages
                for (let i = 1; i <= pageCount; i++) {
                    // Limiter le nombre de boutons affichés
                    if (
                        i === 1 || 
                        i === pageCount || 
                        (i >= currentPage - 2 && i <= currentPage + 2)
                    ) {
                        const pageBtn = document.createElement('button');
                        pageBtn.className = 'pagination-btn';
                        if (i === currentPage) pageBtn.classList.add('active');
                        pageBtn.textContent = i;
                        pageBtn.addEventListener('click', () => {
                            currentPage = i;
                            renderMedicationCards(allMedications);
                            window.scrollTo(0, document.querySelector('.medications-container').offsetTop - 100);
                        });
                        paginationElement.appendChild(pageBtn);
                    } else if (
                        (i === currentPage - 3 && currentPage > 3) || 
                        (i === currentPage + 3 && currentPage < pageCount - 2)
                    ) {
                        // Ajouter des points de suspension
                        const ellipsis = document.createElement('span');
                        ellipsis.className = 'pagination-ellipsis';
                        ellipsis.textContent = '...';
                        paginationElement.appendChild(ellipsis);
                    }
                }
                
                // Bouton suivant
                if (currentPage < pageCount) {
                    const nextBtn = document.createElement('button');
                    nextBtn.className = 'pagination-btn';
                    nextBtn.innerHTML = '&raquo;';
                    nextBtn.addEventListener('click', () => {
                        currentPage++;
                        renderMedicationCards(allMedications);
                    });
                    paginationElement.appendChild(nextBtn);
                }
            }
            
            // Filtrer les médicaments selon les critères
            function filterMedications() {
                const nameFilter = document.getElementById('medication-name').value.toLowerCase();
                const formFilter = formSelect.value;
                const presentationFilter = presentationSelect.value;
                const minPrice = parseFloat(document.getElementById('price-min').value) || 0;
                const maxPrice = parseFloat(document.getElementById('price-max').value) || Number.MAX_VALUE;
                
                return allMedications.filter(med => {
                    const nameMatch = med.name.toLowerCase().includes(nameFilter);
                    const formMatch = !formFilter || med.form === formFilter;
                    const presentationMatch = !presentationFilter || med.presentation === presentationFilter;
                    const priceMatch = med.price >= minPrice && med.price <= maxPrice;
                    
                    return nameMatch && formMatch && presentationMatch && priceMatch;
                });
            }
            
            // Afficher les détails d'un médicament dans la modal
            function showMedicationDetails(id) {
                const medication = allMedications.find(med => med.id === parseInt(id));
                
                if (!medication) return;
                
                // Déterminer le statut du stock
                let stockStatus = '';
                let stockColor = '';
                
                switch(medication.stock) {
                    case 0:
                        stockStatus = 'Rupture de stock';
                        stockColor = '#d9534f';
                        break;
                    case 1:
                        stockStatus = 'Stock limité';
                        stockColor = '#f0ad4e';
                        break;
                    default:
                        stockStatus = 'En stock';
                        stockColor = '#5cb85c';
                }
                
                modalTitle.textContent = medication.name;
                
                modalBody.innerHTML = `
                    <div style="background-color: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span style="font-weight: 600; font-size: 1.4rem; color: var(--primary-color);">${medication.price.toFixed(2)} MAD</span>
                            <span style="padding: 0.4rem 0.8rem; border-radius: 20px; background-color: ${stockColor}; color: white; font-weight: 500;">
                                ${stockStatus}
                            </span>
                        </div>
                        <p><strong>Code :</strong> MED-${String(medication.id).padStart(4, '0')}</p>
                    </div>
                    
                    <h3>Caractéristiques</h3>
                    <p><strong>Forme pharmaceutique :</strong> ${medication.form}</p>
                    <p><strong>Présentation :</strong> ${medication.presentation}</p>
                    <p><strong>Classification :</strong> Médicament générique</p>
                    <p><strong>Remboursable :</strong> ${Math.random() > 0.5 ? 'Oui' : 'Non'}</p>
                    
                    <h3>Indications</h3>
                    <p>Ce médicament est indiqué pour le traitement symptomatique de certaines pathologies. Consultez votre médecin ou pharmacien pour plus d'informations.</p>
                    
                    <h3>Posologie recommandée</h3>
                    <p>La posologie habituelle est déterminée par votre médecin en fonction de votre condition. Suivez toujours les indications prescrites.</p>
                    
                    <h3>Effets indésirables</h3>
                    <ul>
                        <li>Nausées, vomissements</li>
                        <li>Maux de tête</li>
                        <li>Troubles gastro-intestinaux</li>
                    </ul>
                    
                    <h3>Contre-indications</h3>
                    <p>Ne pas utiliser en cas d'hypersensibilité à l'un des composants. Consultez votre médecin avant utilisation si vous souffrez de pathologies particulières.</p>
                    
                    <div style="margin-top: 2rem; display: flex; justify-content: center;">
                        <button class="btn btn-primary" style="padding: 0.8rem 2rem;">
                            Commander ce médicament
                        </button>
                    </div>
                `;
                
                // Afficher la modal
                medicationModal.style.display = 'flex';
            }
            
            // Initialiser l'application
            async function initialize() {
                try {
                    loadingElement.style.display = 'flex';
                    
                    // Charger les données
                    allMedications = await loadMedicationData();
                    
                    // Afficher les résultats
                    renderMedicationCards(allMedications);
                    
                    loadingElement.style.display = 'none';
                    
                    // Ajouter les gestionnaires d'événements
                    setupEventListeners();
                } catch (error) {
                    console.error("Erreur d'initialisation :", error);
                    loadingElement.style.display = 'none';
                    alert("Une erreur est survenue lors du chargement des données. Veuillez réessayer plus tard.");
                }
            }
            
            // Configuration des gestionnaires d'événements
            function setupEventListeners() {
                // Soumission du formulaire de recherche
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    currentPage = 1;
                    const filteredMedications = filterMedications();
                    renderMedicationCards(filteredMedications);
                });
                
                // Réinitialisation des filtres
                resetButton.addEventListener('click', function() {
                    searchForm.reset();
                    currentPage = 1;
                    renderMedicationCards(allMedications);
                });
                
                // Afficher les détails du médicament
                medicationsGrid.addEventListener('click', function(e) {
                    if (e.target.classList.contains('view-details')) {
                        const medicationId = e.target.getAttribute('data-id');
                        showMedicationDetails(medicationId);
                    }
                });
                
                // Fermer la modal
                closeModal.addEventListener('click', function() {
                    medicationModal.style.display = 'none';
                });
                
                // Fermer la modal en cliquant en dehors
                medicationModal.addEventListener('click', function(e) {
                    if (e.target === medicationModal) {
                        medicationModal.style.display = 'none';
                    }
                });
                
                // Fermer la modal avec la touche Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && medicationModal.style.display === 'flex') {
                        medicationModal.style.display = 'none';
                    }
                });
            }
            
            // Démarrer l'application
            initialize();
        });
    </script>
</body>
</html>