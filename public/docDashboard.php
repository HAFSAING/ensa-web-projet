<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Médecin - MediStatView</title>
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
        }

        /* Header avec navigation et icônes */
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

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .profile-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--accent-color1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .profile-info {
            text-align: left;
        }

        .profile-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .profile-title {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            min-width: 200px;
            background-color: white;
            box-shadow: var(--shadow);
            border-radius: 8px;
            padding: 0.5rem 0;
            z-index: 1;
            margin-top: 0.5rem;
        }

        .dropdown-content a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--text-dark);
            text-decoration: none;
            padding: 0.7rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-content a:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        .dropdown-content a i {
            color: var(--secondary-color);
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 0.5rem 0;
        }

        .profile-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Layout du tableau de bord */
        .dashboard-container {
            display: flex;
            max-width: 1400px;
            margin: 2rem auto;
            gap: 2rem;
            padding: 0 1rem;
        }

        .sidebar {
            width: 280px;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            flex-shrink: 0;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--text-dark);
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background-color: var(--light-bg);
            color: var(--secondary-color);
        }

        .sidebar-menu a.active {
            background-color: var(--primary-color);
            color: var(--text-light);
        }

        .sidebar-menu a.active i {
            color: var(--accent-color1);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            color: #777;
        }

        .main-content {
            flex-grow: 1;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background-color: var(--light-bg);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }

        .stat-label {
            color: #777;
            font-size: 0.9rem;
        }

        /* Prochains rendez-vous */
        .appointment-list {
            list-style: none;
        }

        .appointment-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .appointment-item:last-child {
            border-bottom: none;
        }

        .appointment-date {
            width: 100px;
            height: 80px;
            background-color: var(--light-bg);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            color: var(--primary-color);
        }

        .appointment-day {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .appointment-month {
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .appointment-time {
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        .appointment-info {
            flex-grow: 1;
        }

        .appointment-patient {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .appointment-type {
            color: #777;
            font-size: 0.9rem;
        }

        .appointment-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-left: 0.5rem;
        }

        .status-upcoming {
            background-color: #e0f7fa;
            color: #0288d1;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .appointment-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-button {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-bg);
            color: var(--primary-color);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Liste de patients */
        .patient-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-table th,
        .patient-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .patient-table th {
            color: var(--primary-color);
            font-weight: 600;
            background-color: rgba(29, 86, 107, 0.05);
        }

        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 1rem;
        }

        .patient-name-cell {
            display: flex;
            align-items: center;
        }

        .patient-name {
            font-weight: 500;
        }

        .patient-data {
            color: #555;
        }

        .health-indicator {
            width: 100px;
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }

        .health-bar {
            height: 100%;
            border-radius: 5px;
        }

        .health-excellent {
            background-color: #4caf50;
            width: 90%;
        }

        .health-good {
            background-color: #8bc34a;
            width: 75%;
        }

        .health-fair {
            background-color: #ffc107;
            width: 50%;
        }

        .health-poor {
            background-color: #f44336;
            width: 30%;
        }

        .action-icon {
            color: var(--primary-color);
            cursor: pointer;
            margin-right: 0.8rem;
            transition: all 0.2s ease;
        }

        .action-icon:hover {
            color: var(--secondary-color);
            transform: scale(1.2);
        }

        /* Graphique & Statistiques */
        .chart-container {
            padding: 1rem;
            height: 300px;
            position: relative;
        }

        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }

        .legend-label {
            font-size: 0.9rem;
            color: #555;
        }

        .color-1 {
            background-color: var(--primary-color);
        }

        .color-2 {
            background-color: var(--accent-color1);
        }

        .color-3 {
            background-color: var(--accent-color2);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                margin-bottom: 1rem;
            }

            .sidebar-menu {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .sidebar-menu li {
                flex: 1 1 auto;
                margin-bottom: 0;
            }

            .sidebar-menu a {
                justify-content: center;
                padding: 0.6rem;
            }

            .sidebar-menu a span {
                display: none;
            }

            .sidebar-menu a i {
                font-size: 1.5rem;
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .appointment-date {
                width: 80px;
                height: 70px;
            }

            .appointment-day {
                font-size: 1.5rem;
            }

            .patient-table th:nth-child(3),
            .patient-table td:nth-child(3),
            .patient-table th:nth-child(4),
            .patient-table td:nth-child(4) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .appointment-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.5rem 0;
            }

            .appointment-date {
                margin-bottom: 1rem;
                width: 100%;
                height: auto;
                flex-direction: row;
                gap: 0.5rem;
                padding: 0.5rem;
                justify-content: flex-start;
            }

            .appointment-actions {
                margin-top: 1rem;
                align-self: flex-end;
            }

            .patient-table th:nth-child(2),
            .patient-table td:nth-child(2) {
                display: none;
            }
        }
    </style>
    <!-- Ajouter Font Awesome pour les icônes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <a href="#" class="nav-link active">
                                <i class="fas fa-home"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docPatient.php" class="nav-link">
                                <i class="fas fa-user-injured"></i>
                                <span>Patients</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docRendezVous.php" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Rendez-vous</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docDossier.php" class="nav-link">
                                <i class="fas fa-file-medical"></i>
                                <span>Dossiers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docPrescriptions.php" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Prescriptions</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="profile-dropdown">
                    <button class="profile-button">
                        <div class="profile-avatar">DR</div>
                        <div class="profile-info">
                            <div class="profile-name">Dr. Robert</div>
                            <div class="profile-title">Cardiologue</div>
                        </div>
                        <i class="fas fa-chevron-down" style="margin-left: 10px; font-size: 0.8rem;"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="docProfile.php"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="#"><i class="fas fa-bell"></i> Notifications</a>
                        <div class="dropdown-divider"></div>
                        <a href="#"><i class="fas fa-question-circle"></i> Aide & Support</a>
                        <a href="docDeconnection.php" style="color: #d32f2f;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="active">
                        <i class="fas fa-th-large"></i>
                        <span>Vue d'ensemble</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-user-injured"></i>
                        <span>Liste des patients</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-calendar-check"></i>
                        <span>Rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-file-medical-alt"></i>
                        <span>Dossiers médicaux</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-notes-medical"></i>
                        <span>Notes cliniques</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-prescription"></i>
                        <span>Prescriptions</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-flask"></i>
                        <span>Résultats d'analyses</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistiques</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-envelope"></i>
                        <span>Messagerie</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Section statistiques -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Vue d'ensemble</h2>
                    <div>
                        <select class="period-selector" style="padding: 0.5rem; border-radius: 5px; border: 1px solid #ddd;">
                            <option>Aujourd'hui</option>
                            <option>Cette semaine</option>
                            <option selected>Ce mois</option>
                            <option>Cette année</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--primary-color);">
                                <i class="fas fa-user-injured"></i>
                            </div>
                            <div class="stat-value">42</div>
                            <div class="stat-label">Patients Totaux</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--secondary-color);">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value">18</div>
                            <div class="stat-label">Rendez-vous ce mois</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--accent-color1);">
                                <i class="fas fa-file-medical"></i>
                            </div>
                            <div class="stat-value">27</div>
                            <div class="stat-label">Consultations</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: var(--accent-color2);">
                                <i class="fas fa-prescription"></i>
                            </div>
                            <div class="stat-value">33</div>
                            <div class="stat-label">Prescriptions</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section graphique -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Activité mensuelle</h2>
                    <div>
                        <button class="action-button">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color color-1"></div>
                            <div class="legend-label">Consultations</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color color-2"></div>
                            <div class="legend-label">Nouveaux patients</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color color-3"></div>
                            <div class="legend-label">Prescriptions</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prochains rendez-vous -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Prochains rendez-vous</h2>
                    <a href="#" style="color: var(--secondary-color); text-decoration: none; font-weight: 500;">Voir tous</a>
                </div>
                <div class="card-body">
                    <ul class="appointment-list">
                        <li class="appointment-item">
                            <div class="appointment-date">
                                <div class="appointment-day">10</div>
                                <div class="appointment-month">Mai</div>
                                <div class="appointment-time">09:00</div>
                            </div>
                            <div class="appointment-info">
                                <div class="appointment-patient">Sophie Martin <span class="appointment-status status-upcoming">À venir</span></div>
                                <div class="appointment-type">Consultation de suivi - Problème cardiaque</div>
                            </div>
                            <div class="appointment-actions">
                                <button class="action-button" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-button" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </li>
                        <li class="appointment-item">
                            <div class="appointment-date">
                                <div class="appointment-day">10</div>
                                <div class="appointment-month">Mai</div>
                                <div class="appointment-time">11:30</div>
                            </div>
                            <div class="appointment-info">
                                <div class="appointment-patient">Thomas Dubois <span class="appointment-status status-upcoming">À venir</span></div>
                                <div class="appointment-type">Première consultation - Hypertension</div>
                            </div>
                            <div class="appointment-actions">
                                <button class="action-button" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-button" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </li>
                        <li class="appointment-item">
                            <div class="appointment-date">
                                <div class="appointment-day">11</div>
                                <div class="appointment-month">Mai</div>
                                <div class="appointment-time">14:00</div>
                            </div>
                            <div class="appointment-info">
                                <div class="appointment-patient">Émilie Leroy <span class="appointment-status status-upcoming">À venir</span></div>
                                <div class="appointment-type">Suivi post-op
                                </div>
                            <div class="appointment-actions">
                                <button class="action-button" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-button" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </li>
                        <li class="appointment-item">
                            <div class="appointment-date">
                                <div class="appointment-day">12</div>
                                <div class="appointment-month">Mai</div>
                                <div class="appointment-time">10:15</div>
                            </div>
                            <div class="appointment-info">
                                <div class="appointment-patient">Laurent Petit <span class="appointment-status status-cancelled">Annulé</span></div>
                                <div class="appointment-type">Consultation de routine</div>
                            </div>
                            <div class="appointment-actions">
                                <button class="action-button" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-button" title="Reprogrammer">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Liste des patients récents -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Patients récents</h2>
                    <a href="#" style="color: var(--secondary-color); text-decoration: none; font-weight: 500;">Voir tous</a>
                </div>
                <div class="card-body">
                    <table class="patient-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Âge</th>
                                <th>État</th>
                                <th>Dernière visite</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="patient-name-cell">
                                    <div class="patient-avatar">SM</div>
                                    <div class="patient-name">Sophie Martin</div>
                                </td>
                                <td class="patient-data">42 ans</td>
                                <td>
                                    <div class="health-indicator">
                                        <div class="health-bar health-good"></div>
                                    </div>
                                </td>
                                <td class="patient-data">05 Mai, 2025</td>
                                <td>
                                    <i class="fas fa-file-medical action-icon" title="Dossier"></i>
                                    <i class="fas fa-prescription action-icon" title="Prescrire"></i>
                                    <i class="fas fa-calendar-plus action-icon" title="Rendez-vous"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="patient-name-cell">
                                    <div class="patient-avatar">TD</div>
                                    <div class="patient-name">Thomas Dubois</div>
                                </td>
                                <td class="patient-data">58 ans</td>
                                <td>
                                    <div class="health-indicator">
                                        <div class="health-bar health-fair"></div>
                                    </div>
                                </td>
                                <td class="patient-data">03 Mai, 2025</td>
                                <td>
                                    <i class="fas fa-file-medical action-icon" title="Dossier"></i>
                                    <i class="fas fa-prescription action-icon" title="Prescrire"></i>
                                    <i class="fas fa-calendar-plus action-icon" title="Rendez-vous"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="patient-name-cell">
                                    <div class="patient-avatar">EL</div>
                                    <div class="patient-name">Émilie Leroy</div>
                                </td>
                                <td class="patient-data">35 ans</td>
                                <td>
                                    <div class="health-indicator">
                                        <div class="health-bar health-excellent"></div>
                                    </div>
                                </td>
                                <td class="patient-data">28 Avr, 2025</td>
                                <td>
                                    <i class="fas fa-file-medical action-icon" title="Dossier"></i>
                                    <i class="fas fa-prescription action-icon" title="Prescrire"></i>
                                    <i class="fas fa-calendar-plus action-icon" title="Rendez-vous"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="patient-name-cell">
                                    <div class="patient-avatar">LP</div>
                                    <div class="patient-name">Laurent Petit</div>
                                </td>
                                <td class="patient-data">67 ans</td>
                                <td>
                                    <div class="health-indicator">
                                        <div class="health-bar health-poor"></div>
                                    </div>
                                </td>
                                <td class="patient-data">25 Avr, 2025</td>
                                <td>
                                    <i class="fas fa-file-medical action-icon" title="Dossier"></i>
                                    <i class="fas fa-prescription action-icon" title="Prescrire"></i>
                                    <i class="fas fa-calendar-plus action-icon" title="Rendez-vous"></i>
                                </td>
                            </tr>
                            <tr>
                                <td class="patient-name-cell">
                                    <div class="patient-avatar">JB</div>
                                    <div class="patient-name">Jeanne Bonnet</div>
                                </td>
                                <td class="patient-data">49 ans</td>
                                <td>
                                    <div class="health-indicator">
                                        <div class="health-bar health-good"></div>
                                    </div>
                                </td>
                                <td class="patient-data">22 Avr, 2025</td>
                                <td>
                                    <i class="fas fa-file-medical action-icon" title="Dossier"></i>
                                    <i class="fas fa-prescription action-icon" title="Prescrire"></i>
                                    <i class="fas fa-calendar-plus action-icon" title="Rendez-vous"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Créer un graphique d'activité avec Chart.js
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('activityChart').getContext('2d');
            const activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [
                        {
                            label: 'Consultations',
                            data: [18, 22, 19, 24, 27, 25, 28, 26, 30, 32, 27, 24],
                            backgroundColor: 'rgba(29, 86, 107, 0.1)',
                            borderColor: 'rgba(29, 86, 107, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: 'rgba(29, 86, 107, 1)',
                            pointRadius: 4
                        },
                        {
                            label: 'Nouveaux patients',
                            data: [7, 5, 8, 9, 6, 8, 7, 9, 10, 8, 7, 5],
                            backgroundColor: 'rgba(123, 186, 154, 0.1)',
                            borderColor: 'rgba(123, 186, 154, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: 'rgba(123, 186, 154, 1)',
                            pointRadius: 4
                        },
                        {
                            label: 'Prescriptions',
                            data: [15, 18, 16, 22, 25, 23, 26, 24, 27, 29, 25, 22],
                            backgroundColor: 'rgba(134, 179, 195, 0.1)',
                            borderColor: 'rgba(134, 179, 195, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: 'rgba(134, 179, 195, 1)',
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>