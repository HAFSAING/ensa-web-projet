<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Patient - MediStatView</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
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
        
        /* User Menu */
        .user-menu {
            position: relative;
        }
        
        .user-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .user-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        
        .user-name {
            font-weight: 600;
        }
        
        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: var(--shadow);
            border-radius: 8px;
            width: 200px;
            margin-top: 0.5rem;
            display: none;
            z-index: 100;
        }
        
        .dropdown-menu.active {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }
        
        .dropdown-item i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        .dropdown-divider {
            border-top: 1px solid var(--border-color);
            margin: 0.5rem 0;
        }
        
        /* Dashboard layout */
        .dashboard {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2rem;
        }
        
        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: var(--primary-color);
        }
        
        .dashboard-greeting {
            font-size: 1rem;
            color: #666;
        }
        
        .dashboard-date {
            font-weight: 500;
            color: var(--secondary-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.7rem 1.4rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline:hover {
            background-color: rgba(29, 86, 107, 0.1);
            transform: translateY(-2px);
        }
        
        /* Dashboard cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .dash-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(123, 186, 154, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color1);
            font-size: 1.2rem;
        }
        
        .appointments-icon {
            background-color: rgba(134, 179, 195, 0.2);
            color: var(--accent-color2);
        }
        
        .meds-icon {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }
        
        .stats-icon {
            background-color: rgba(33, 107, 78, 0.1);
            color: var(--secondary-color);
        }
        
        .card-content {
            margin-bottom: 1rem;
        }
        
        .metrics {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .metric {
            background-color: var(--light-bg);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .metric i {
            margin-right: 0.3rem;
            color: var(--secondary-color);
        }
        
        .metric-value {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .card-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.3s;
        }
        
        .card-footer a:hover {
            color: var(--secondary-color);
        }
        
        /* Appointment list */
        .appointment-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .appointment-item:last-child {
            border-bottom: none;
        }
        
        .appointment-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background-color: var(--light-bg);
            border-radius: 10px;
            margin-right: 1rem;
        }
        
        .appointment-day {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .appointment-month {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #666;
        }
        
        .appointment-info {
            flex: 1;
        }
        
        .appointment-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }
        
        .appointment-details {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .appointment-time, .appointment-location {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .appointment-time i, .appointment-location i {
            color: var(--accent-color2);
        }
        
        .appointment-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        /* Medication list */
        .med-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .med-item:last-child {
            border-bottom: none;
        }
        
        .med-icon {
            width: 45px;
            height: 45px;
            background-color: var(--light-bg);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--accent-color3);
            font-size: 1.2rem;
        }
        
        .med-info {
            flex: 1;
        }
        
        .med-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }
        
        .med-details {
            color: #666;
            font-size: 0.9rem;
        }
        
        .med-timing {
            background-color: var(--light-bg);
            border-radius: 20px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        
        /* Health stats */
        .stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .stat-row:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .stat-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: rgba(123, 186, 154, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color1);
            font-size: 1rem;
        }
        
        .pressure-icon {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }
        
        .weight-icon {
            background-color: rgba(134, 179, 195, 0.2);
            color: var(--accent-color2);
        }
        
        .chol-icon {
            background-color: rgba(33, 107, 78, 0.1);
            color: var(--secondary-color);
        }
        
        .stat-value {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .stat-trend {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }
        
        .trend-up {
            color: #e74c3c;
        }
        
        .trend-down {
            color: #2ecc71;
        }
        
        .trend-stable {
            color: #f39c12;
        }
        
        /* Messages section */
        .messages-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .messages-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .messages-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .message-list {
            padding: 0 1.5rem;
        }
        
        .message-item {
            display: flex;
            padding: 1.2rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .message-item:last-child {
            border-bottom: none;
        }
        
        .message-sender {
            margin-right: 1rem;
        }
        
        .sender-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .message-content {
            flex: 1;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .sender-name {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .message-time {
            color: #777;
            font-size: 0.9rem;
        }
        
        .message-preview {
            color: #666;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .message-badge {
            background-color: var(--accent-color1);
            color: white;
            border-radius: 20px;
            padding: 0.2rem 0.6rem;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        .messages-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem 0;
            margin-top: auto;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .copyright {
            font-size: 0.9rem;
        }
        
        .footer-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .footer-link {
            color: #ccc;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: var(--accent-color1);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
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
            
            .dashboard {
                padding: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .nav-list {
                flex-wrap: wrap;
                gap: 0.2rem;
            }
            
            .nav-item {
                flex-basis: 33.333%;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            .nav-item {
                flex-basis: 50%;
            }
            
            .nav-link {
                font-size: 0.8rem;
            }
            
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .message-preview {
                -webkit-line-clamp: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
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
                            <a href="patientDashboard.php" class="nav-link active">
                                <i class="fas fa-home"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patientDossier.php" class="nav-link">
                                <i class="fas fa-folder-open"></i>
                                <span>Mon dossier</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patientRendezVous.php" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Rendez-vous</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patientMessages.php" class="nav-link">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patientStatistiques.php" class="nav-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Statistiques</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="user-menu">
                    <button class="user-btn" onclick="toggleDropdown()">
                        <div class="user-avatar">MB</div>
                        <div class="user-info">
                            <span class="user-name">Marie Benoit</span>
                            <span class="user-role">Patient</span>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="userProfile.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Mon profil</span>
                        </a>
                        <a href="userParametres.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="Deconnection.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard content -->
    <div class="dashboard">
        <div class="container">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Tableau de bord</h1>
                    <p class="dashboard-greeting">Bonjour Marie, <span class="dashboard-date">le 8 mai 2025</span></p>
                </div>
                <div class="action-buttons">
                    <a href="patientRendezVous.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Nouveau rendez-vous</span>
                    </a>
                    <a href="patientDossier.php" class="btn btn-outline">
                        <i class="fas fa-file-medical"></i>
                        <span>Mon dossier médical</span>
                    </a>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Prochains rendez-vous</h2>
                        <div class="card-icon appointments-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="appointment-item">
                            <div class="appointment-date">
                                <span class="appointment-day">12</span>
                                <span class="appointment-month">mai</span>
                            </div>
                            <div class="appointment-info">
                                <div class="appointment-name">Dr. Thomas Laurent</div>
                                <div class="appointment-details">
                                    <span class="appointment-time">
                                        <i class="far fa-clock"></i>
                                        10:30
                                    </span>
                                    <span class="appointment-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Cabinet médical
                                    </span>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <button class="btn btn-sm btn-outline">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        <div class="appointment-item">
                            <div class="appointment-date">
                                <span class="appointment-day">18</span>
                                <span class="appointment-month">mai</span>
                            </div>
                            <div class="appointment-info">
                                <div class="appointment-name">Dr. Sophie Martin</div>
                                <div class="appointment-details">
                                    <span class="appointment-time">
                                        <i class="far fa-clock"></i>
                                        14:00
                                    </span>
                                    <span class="appointment-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Clinique Saint-Pierre
                                    </span>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <button class="btn btn-sm btn-outline">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="patientRendezVous.php">
                            Voir tous les rendez-vous
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <div class="metrics">
                            <span class="metric">
                                <i class="fas fa-calendar"></i>
                                <span class="metric-value">2</span> à venir
                            </span>
                        </div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Mes médicaments</h2>
                        <div class="card-icon
                        <div class="card-icon meds-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="med-item">
                            <div class="med-icon">
                                <i class="fas fa-capsules"></i>
                            </div>
                            <div class="med-info">
                                <div class="med-name">Atorvastatine 20mg</div>
                                <div class="med-details">1 comprimé le soir</div>
                            </div>
                            <div class="med-timing">Tous les jours</div>
                        </div>
                        <div class="med-item">
                            <div class="med-icon">
                                <i class="fas fa-tablets"></i>
                            </div>
                            <div class="med-info">
                                <div class="med-name">Lévothyrox 75µg</div>
                                <div class="med-details">1 comprimé le matin à jeun</div>
                            </div>
                            <div class="med-timing">Tous les jours</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="patientMedicaments.php">
                            Voir tous les médicaments
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <div class="metrics">
                            <span class="metric">
                                <i class="fas fa-prescription-bottle-alt"></i>
                                <span class="metric-value">4</span> actifs
                            </span>
                        </div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Mes indicateurs santé</h2>
                        <div class="card-icon stats-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="stat-row">
                            <div class="stat-label">
                                <div class="stat-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <span>Fréquence cardiaque</span>
                            </div>
                            <div class="stat-value">
                                72 bpm
                                <span class="stat-trend trend-stable">
                                    <i class="fas fa-minus"></i>
                                </span>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-label">
                                <div class="stat-icon pressure-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <span>Tension artérielle</span>
                            </div>
                            <div class="stat-value">
                                128/82
                                <span class="stat-trend trend-down">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-label">
                                <div class="stat-icon weight-icon">
                                    <i class="fas fa-weight"></i>
                                </div>
                                <span>Poids</span>
                            </div>
                            <div class="stat-value">
                                68.4 kg
                                <span class="stat-trend trend-stable">
                                    <i class="fas fa-minus"></i>
                                </span>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-label">
                                <div class="stat-icon chol-icon">
                                    <i class="fas fa-vial"></i>
                                </div>
                                <span>Cholestérol</span>
                            </div>
                            <div class="stat-value">
                                5.1 mmol/L
                                <span class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="patientStatistiques.php">
                            Voir toutes les statistiques
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="messages-section">
                <div class="messages-header">
                    <h2 class="messages-title">Messages récents</h2>
                    <a href="patientMessages.php" class="btn btn-outline">
                        <i class="fas fa-envelope"></i>
                        <span>Boîte de réception</span>
                    </a>
                </div>
                <div class="message-list">
                    <div class="message-item">
                        <div class="message-sender">
                            <div class="sender-avatar">TL</div>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <div class="sender-name">
                                    Dr. Thomas Laurent
                                    <span class="message-badge">Nouveau</span>
                                </div>
                                <div class="message-time">Aujourd'hui, 09:45</div>
                            </div>
                            <div class="message-preview">
                                Bonjour Mme Benoit, suite à notre dernier rendez-vous, je vous transmets les résultats de vos analyses sanguines. Le taux de cholestérol est légèrement élevé, nous en discuterons lors de notre prochain rendez-vous...
                            </div>
                            <div class="message-actions">
                                <a href="patientMessagesDetails.php?id=1" class="btn btn-sm btn-outline">
                                    Lire
                                </a>
                                <button class="btn btn-sm btn-outline">
                                    Répondre
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="message-item">
                        <div class="message-sender">
                            <div class="sender-avatar">SM</div>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <div class="sender-name">Dr. Sophie Martin</div>
                                <div class="message-time">Hier, 14:20</div>
                            </div>
                            <div class="message-preview">
                                Bonjour Marie, je vous confirme notre rendez-vous du 18 mai pour le suivi de votre thyroïde. N'oubliez pas d'apporter vos dernières analyses...
                            </div>
                            <div class="message-actions">
                                <a href="patientMessagesDetails.php?id=2" class="btn btn-sm btn-outline">
                                    Lire
                                </a>
                                <button class="btn btn-sm btn-outline">
                                    Répondre
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="messages-footer">
                    <a href="patientMessages.php" class="btn btn-outline">
                        Voir tous les messages
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="copyright">
                © 2025 MediStatView Services. Tous droits réservés.
            </div>
            <div class="footer-links">
                <a href="aPropos.php" class="footer-link">À propos</a>
                <a href="confidentialite.php" class="footer-link">Confidentialité</a>
                <a href="conditions.php" class="footer-link">Conditions d'utilisation</a>
                <a href="contact.php" class="footer-link">Contact</a>
                <a href="aide.php" class="footer-link">Aide</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('active');
        }
        
        // Fermer le menu déroulant lorsqu'on clique ailleurs sur la page
        window.onclick = function(event) {
            if (!event.target.matches('.user-btn') && !event.target.matches('.user-btn *')) {
                var dropdown = document.getElementById('userDropdown');
                if (dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>