<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Rendez-vous - MediStatView</title>
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
        
        /* Main content */
        .main-content {
            flex: 1;
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
        
        .btn-danger {
            background-color: var(--accent-color3);
            color: var(--text-light);
        }
        
        .btn-danger:hover {
            background-color: #b30000;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        /* Filter section */
        .filter-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color2);
            box-shadow: 0 0 0 3px rgba(134, 179, 195, 0.3);
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .tab {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--accent-color1);
        }
        
        .tab-badge {
            background-color: #ddd;
            color: #666;
            border-radius: 20px;
            padding: 0.2rem 0.6rem;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }
        
        .tab.active .tab-badge {
            background-color: var(--accent-color1);
            color: var(--primary-color);
        }
        
        /* Calendar view */
        .calendar {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .calendar-header {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .calendar-title {
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .calendar-nav {
            display: flex;
            gap: 0.5rem;
        }
        
        .calendar-nav-btn {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .calendar-nav-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
        
        .calendar-weekdays {
            background-color: var(--light-bg);
        }
        
        .weekday {
            padding: 0.8rem;
            text-align: center;
            font-weight: 600;
            color: #666;
            border-bottom: 1px solid var(--border-color);
        }
        
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
        
        .calendar-day {
            min-height: 120px;
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            position: relative;
        }
        
        .day-number {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }
        
        .day-today {
            background-color: var(--accent-color1);
            color: var(--primary-color);
            border-radius: 50%;
        }
        
        .day-event {
            background-color: rgba(134, 179, 195, 0.2);
            border-left: 3px solid var(--accent-color2);
            padding: 0.4rem 0.5rem;
            margin-top: 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .day-event:hover {
            background-color: rgba(134, 179, 195, 0.3);
            transform: translateY(-2px);
        }
        
        .day-event-time {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .day-event-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* List view */
        .appointments-list {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .appointment-item {
            display: flex;
            align-items: center;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        
        .appointment-item:hover {
            background-color: var(--light-bg);
        }
        
        .appointment-item:last-child {
            border-bottom: none;
        }
        
        .appointment-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background-color: var(--light-bg);
            border-radius: 10px;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }
        
        .appointment-day {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .appointment-month {
            font-size: 0.9rem;
            text-transform: uppercase;
            color: #666;
        }
        
        .appointment-info {
            flex: 1;
        }
        
        .appointment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .appointment-time {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .appointment-time i {
            color: var(--accent-color2);
        }
        
        .appointment-doctor {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }
        
        .appointment-type {
            background-color: rgba(33, 107, 78, 0.1);
            color: var(--secondary-color);
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .appointment-details {
            color: #666;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .appointment-location, .appointment-note {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.9rem;
        }
        
        .appointment-location i, .appointment-note i {
            color: var(--accent-color2);
        }
        
        .appointment-actions {
            display: flex;
            gap: 0.5rem;
            margin-left: 1rem;
            flex-shrink: 0;
        }
        
        .appointment-status {
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        
        .status-confirmed {
            background-color: rgba(123, 186, 154, 0.2);
            color: var(--secondary-color);
        }
        
        .status-pending {
            background-color: rgba(243, 156, 18, 0.2);
            color: #c87500;
        }
        
        /* No data state */
        .no-data {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .no-data-icon {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        .no-data-title {
            font-size: 1.5rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .no-data-message {
            color: #666;
            margin-bottom: 1.5rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Modal */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .modal-backdrop.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(20px);
            transition: all 0.3s;
        }
        
        .modal-backdrop.active .modal {
            transform: translateY(0);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: #666;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .modal-close:hover {
            color: var(--accent-color3);
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
        
        /* New appointment form */
        .form-row {
            margin-bottom: 1.5rem;
        }
        
        .form-row:last-child {
            margin-bottom: 0;
        }
        
        .form-row-split {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .form-row-split .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }
        
        .page-item {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background-color: white;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .page-item:hover {
            background-color: var(--light-bg);
        }
        
        .page-item.active {
            background-color: var(--primary-color);
            color: var(--text-light);
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
        
        /* Badge */
        .badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: rgba(123, 186, 154, 0.2);
            color: var(--secondary-color);
        }
        
        .badge-warning {
            background-color: rgba(243, 156, 18, 0.2);
            color: #c87500;
        }
        
        .badge-danger {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }
        
        .badge-info {
            background-color: rgba(134, 179, 195, 0.2);
            color: var(--primary-color);
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
            
            .main-content {
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
            
            .appointment-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .appointment-date {
                margin-bottom: 1rem;
            }
            
            .appointment-actions {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
                justify-content: flex-end;
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
            
            .calendar-day {
                min-height: 80px;
            }
            
            .day-event {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
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
                            <a href="patientDashboard.php" class="nav-link">
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
                            <a href="patientRendezVous.php" class="nav-link active">
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
                        <a href="patientProfil.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Mon profil</span>
                        </a>
                        <a href="patientParametres.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="main-content container">
        <div class="page-header">
            <h1 class="page-title">Mes Rendez-vous</h1>
            <div class="action-buttons">
                <button class="btn btn-primary" id="newAppointmentBtn">
                    <i class="fas fa-plus"></i>
                    <span>Nouveau rendez-vous</span>
                </button>
                <button class="btn btn-outline">
                    <i class="fas fa-sync-alt"></i>
                    <span>Actualiser</span>
                </button>
            </div>
        </div>

        <!-- Section de filtres -->
        <div class="filter-section">
            <h2 class="filter-title">Filtrer les rendez-vous</h2>
            <form class="filter-form">
                <div class="form-group">
                    <label class="form-label">Médecin</label>
                    <select class="form-control">
                        <option value="">Tous les médecins</option>
                        <option value="1">Dr. Thomas Leroy</option>
                        <option value="2">Dr. Sophie Martin</option>
                        <option value="3">Dr. Philippe Dubois</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Spécialité</label>
                    <select class="form-control">
                        <option value="">Toutes les spécialités</option>
                        <option value="1">Médecine générale</option>
                        <option value="2">Cardiologie</option>
                        <option value="3">Dermatologie</option>
                        <option value="4">Pédiatrie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Période</label>
                    <select class="form-control">
                        <option value="all">Tous</option>
                        <option value="upcoming">À venir</option>
                        <option value="past">Passés</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control">
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i>
                        <span>Filtrer</span>
                    </button>
                    <button type="reset" class="btn btn-outline btn-sm">
                        <i class="fas fa-times"></i>
                        <span>Réinitialiser</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Onglets de vue -->
        <div class="tabs">
            <div class="tab active" data-view="list">
                <i class="fas fa-list"></i>
                <span>Liste</span>
                <span class="tab-badge">5</span>
            </div>
            <div class="tab" data-view="calendar">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendrier</span>
            </div>
        </div>

        <!-- Vue liste -->
        <div class="tab-content" id="listView">
            <div class="appointments-list">
                <!-- Rendez-vous 1 -->
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="appointment-day">15</div>
                        <div class="appointment-month">MAI</div>
                    </div>
                    <div class="appointment-info">
                        <span class="appointment-status status-confirmed">Confirmé</span>
                        <div class="appointment-meta">
                            <div class="appointment-time">
                                <i class="far fa-clock"></i>
                                <span>14:30 - 15:00</span>
                            </div>
                            <span class="appointment-type">Consultation</span>
                        </div>
                        <div class="appointment-doctor">Dr. Thomas Leroy - Médecine générale</div>
                        <div class="appointment-details">
                            <div class="appointment-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Centre Médical Saint-Michel, Bureau 303</span>
                            </div>
                            <div class="appointment-note">
                                <i class="fas fa-sticky-note"></i>
                                <span>Suivi traitement hypertension</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                            <span>Modifier</span>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i>
                            <span>Annuler</span>
                        </button>
                    </div>
                </div>
                
                <!-- Rendez-vous 2 -->
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="appointment-day">20</div>
                        <div class="appointment-month">MAI</div>
                    </div>
                    <div class="appointment-info">
                        <span class="appointment-status status-pending">En attente</span>
                        <div class="appointment-meta">
                            <div class="appointment-time">
                                <i class="far fa-clock"></i>
                                <span>10:00 - 11:00</span>
                            </div>
                            <span class="appointment-type">Examen</span>
                        </div>
                        <div class="appointment-doctor">Dr. Sophie Martin - Cardiologie</div>
                        <div class="appointment-details">
                            <div class="appointment-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Hôpital Sainte-Marie, Service Cardiologie</span>
                            </div>
                            <div class="appointment-note">
                                <i class="fas fa-sticky-note"></i>
                                <span>Échographie cardiaque</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                            <span>Modifier</span>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i>
                            <span>Annuler</span>
                        </button>
                    </div>
                </div>
                
                <!-- Rendez-vous 3 -->
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="appointment-day">28</div>
                        <div class="appointment-month">MAI</div>
                    </div>
                    <div class="appointment-info">
                        <span class="appointment-status status-confirmed">Confirmé</span>
                        <div class="appointment-meta">
                            <div class="appointment-time">
                                <i class="far fa-clock"></i>
                                <span>09:15 - 09:45</span>
                            </div>
                            <span class="appointment-type">Consultation</span>
                        </div>
                        <div class="appointment-doctor">Dr. Philippe Dubois - Dermatologie</div>
                        <div class="appointment-details">
                            <div class="appointment-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Cabinet Médical Montaigne, 2ème étage</span>
                            </div>
                            <div class="appointment-note">
                                <i class="fas fa-sticky-note"></i>
                                <span>Suivi après traitement</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                            <span>Modifier</span>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i>
                            <span>Annuler</span>
                        </button>
                    </div>
                </div>
                
                <!-- Rendez-vous 4 - Passé -->
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="appointment-day">05</div>
                        <div class="appointment-month">MAI</div>
                    </div>
                    <div class="appointment-info">
                        <span class="appointment-status badge-info">Terminé</span>
                        <div class="appointment-meta">
                            <div class="appointment-time">
                                <i class="far fa-clock"></i>
                                <span>11:30 - 12:00</span>
                            </div>
                            <span class="appointment-type">Consultation</span>
                        </div>
                        <div class="appointment-doctor">Dr. Thomas Leroy - Médecine générale</div>
                        <div class="appointment-details">
                            <div class="appointment-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Centre Médical Saint-Michel, Bureau 303</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-file-medical-alt"></i>
                            <span>Rapport</span>
                        </button>
                    </div>
                </div>
                
                <!-- Rendez-vous 5 - Passé -->
                <div class="appointment-item">
                    <div class="appointment-date">
                        <div class="appointment-day">25</div>
                        <div class="appointment-month">AVR</div>
                    </div>
                    <div class="appointment-info">
                        <span class="appointment-status badge-info">Terminé</span>
                        <div class="appointment-meta">
                            <div class="appointment-time">
                                <i class="far fa-clock"></i>
                                <span>15:45 - 16:30</span>
                            </div>
                            <span class="appointment-type">Examen</span>
                        </div>
                        <div class="appointment-doctor">Dr. Isabelle Klein - Radiologie</div>
                        <div class="appointment-details">
                            <div class="appointment-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Centre d'Imagerie Médicale Pasteur</span>
                            </div>
                            <div class="appointment-note">
                                <i class="fas fa-sticky-note"></i>
                                <span>Radiographie de la cheville</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-file-medical-alt"></i>
                            <span>Rapport</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="pagination">
                <div class="page-item active">1</div>
                <div class="page-item">2</div>
                <div class="page-item">3</div>
                <div class="page-item"><i class="fas fa-chevron-right"></i></div>
            </div>
        </div>
        
        <!-- Vue calendrier (masquée par défaut) -->
        <div class="tab-content" id="calendarView" style="display: none;">
            <div class="calendar">
                <div class="calendar-header">
                    <div class="calendar-title">Mai 2025</div>
                    <div class="calendar-nav">
                        <button class="calendar-nav-btn"><i class="fas fa-chevron-left"></i></button>
                        <button class="calendar-nav-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="calendar-weekdays">
                    <div class="weekday">Lun</div>
                    <div class="weekday">Mar</div>
                    <div class="weekday">Mer</div>
                    <div class="weekday">Jeu</div>
                    <div class="weekday">Ven</div>
                    <div class="weekday">Sam</div>
                    <div class="weekday">Dim</div>
                </div>
                <div class="calendar-days">
                    <!-- Première semaine vide ou partielle -->
                    <div class="calendar-day"><span class="day-number"></span></div>
                    <div class="calendar-day"><span class="day-number"></span></div>
                    <div class="calendar-day"><span class="day-number">1</span></div>
                    <div class="calendar-day"><span class="day-number">2</span></div>
                    <div class="calendar-day"><span class="day-number">3</span></div>
                    <div class="calendar-day"><span class="day-number">4</span></div>
                    <div class="calendar-day"><span class="day-number">5</span>
                        <div class="day-event">
                            <div class="day-event-time">11:30</div>
                            <div class="day-event-title">Dr. Leroy - Consultation</div>
                        </div>
                    </div>
                    
                    <!-- Deuxième semaine -->
                    <div class="calendar-day"><span class="day-number">6</span></div>
                    <div class="calendar-day"><span class="day-number">7</span></div>
                    <div class="calendar-day"><span class="day-number">8</span></div>
                    <div class="calendar-day"><span class="day-number">9</span>
                        <div class="day-today">9</div>
                    </div>
                    <div class="calendar-day"><span class="day-number">10</span></div>
                    <div class="calendar-day"><span class="day-number">11</span></div>
                    <div class="calendar-day"><span class="day-number">12</span></div>
                    
                    <!-- Troisième semaine -->
                    <div class="calendar-day"><span class="day-number">13</span></div>
                    <div class="calendar-day"><span class="day-number">14</span></div>
                    <div class="calendar-day"><span class="day-number">15</span>
                        <div class="day-event">
                            <div class="day-event-time">14:30</div>
                            <div class="day-event-title">Dr. Leroy - Consultation</div>
                        </div>
                    </div>
                    <div class="calendar-day"><span class="day-number">16</span></div>
                    <div class="calendar-day"><span class="day-number">17</span></div>
                    <div class="calendar-day"><span class="day-number">18</span></div>
                    <div class="calendar-day"><span class="day-number">19</span></div>
                    
                    <!-- Quatrième semaine -->
                    <div class="calendar-day"><span class="day-number">20</span>
                        <div class="day-event">
                            <div class="day-event-time">10:00</div>
                            <div class="day-event-title">Dr. Martin - Examen</div>
                        </div>
                    </div>
                    <div class="calendar-day"><span class="day-number">21</span></div>
                    <div class="calendar-day"><span class="day-number">22</span></div>
                    <div class="calendar-day"><span class="day-number">23</span></div>
                    <div class="calendar-day"><span class="day-number">24</span></div>
                    <div class="calendar-day"><span class="day-number">25</span></div>
                    <div class="calendar-day"><span class="day-number">26</span></div>
                    
                    <!-- Cinquième semaine -->
                    <div class="calendar-day"><span class="day-number">27</span></div>
                    <div class="calendar-day"><span class="day-number">28</span>
                        <div class="day-event">
                            <div class="day-event-time">09:15</div>
                            <div class="day-event-title">Dr. Dubois - Consultation</div>
                        </div>
                    </div>
                    <div class="calendar-day"><span class="day-number">29</span></div>
                    <div class="calendar-day"><span class="day-number">30</span></div>
                    <div class="calendar-day"><span class="day-number">31</span></div>
                    <div class="calendar-day"><span class="day-number"></span></div>
                    <div class="calendar-day"><span class="day-number"></span></div>
                </div>
            </div>
        </div>
        
        <!-- État sans données (masqué par défaut) -->
        <div class="no-data" style="display: none;">
            <div class="no-data-icon">
                <i class="far fa-calendar-times"></i>
            </div>
            <h2 class="no-data-title">Aucun rendez-vous trouvé</h2>
            <p class="no-data-message">Vous n'avez pas de rendez-vous correspondant à vos critères de recherche. Veuillez ajuster vos filtres ou créer un nouveau rendez-vous.</p>
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Nouveau rendez-vous</span>
            </button>
        </div>
    </div>

    <!-- Modal pour nouveau rendez-vous -->
    <div class="modal-backdrop" id="appointmentModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Nouveau rendez-vous</h2>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Type de rendez-vous *</label>
                            <select class="form-control" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="consultation">Consultation</option>
                                <option value="suivi">Suivi</option>
                                <option value="examen">Examen</option>
                                <option value="therapie">Thérapie</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Spécialité *</label>
                            <select class="form-control" id="speciality" required>
                                <option value="">Sélectionnez une spécialité</option>
                                <option value="1">Médecine générale</option>
                                <option value="2">Cardiologie</option>
                                <option value="3">Dermatologie</option>
                                <option value="4">Pédiatrie</option>
                                <option value="5">Radiologie</option>
                                <option value="6">Psychiatrie</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Médecin *</label>
                            <select class="form-control" id="doctor" required>
                                <option value="">Sélectionnez un médecin</option>
                                <option value="1">Dr. Thomas Leroy</option>
                                <option value="2">Dr. Sophie Martin</option>
                                <option value="3">Dr. Philippe Dubois</option>
                                <option value="4">Dr. Isabelle Klein</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-split">
                        <div class="form-group">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Heure *</label>
                            <select class="form-control" required>
                                <option value="">Sélectionnez une heure</option>
                                <option value="09:00">09:00</option>
                                <option value="09:30">09:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="14:00">14:00</option>
                                <option value="14:30">14:30</option>
                                <option value="15:00">15:00</option>
                                <option value="15:30">15:30</option>
                                <option value="16:00">16:00</option>
                                <option value="16:30">16:30</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Motif du rendez-vous</label>
                            <textarea class="form-control" placeholder="Décrivez brièvement la raison de votre rendez-vous..."></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Informations supplémentaires</label>
                            <textarea class="form-control" placeholder="Informations importantes à communiquer (allergies, médicaments, symptômes...)"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="cancelModal">Annuler</button>
                <button class="btn btn-primary" type="submit" form="appointmentForm">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="copyright">
                © 2025 MediStatView. Tous droits réservés.
            </div>
            <div class="footer-links">
                <a href="#" class="footer-link">Confidentialité</a>
                <a href="#" class="footer-link">Conditions d'utilisation</a>
                <a href="#" class="footer-link">Contact</a>
                <a href="#" class="footer-link">Aide</a>
            </div>
        </div>
    </footer>

    <!-- Scripts JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        // Fonction pour basculer le menu déroulant utilisateur
        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('active');
        }
        
        // Fermer le menu déroulant si l'utilisateur clique en dehors
        window.onclick = function(event) {
            if (!event.target.matches('.user-btn') && !event.target.closest('.user-btn')) {
                var dropdowns = document.getElementsByClassName('dropdown-menu');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('active')) {
                        openDropdown.classList.remove('active');
                    }
                }
            }
        }
        
        // Gestion des onglets
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Supprimer la classe active de tous les onglets
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet cliqué
                this.classList.add('active');
                
                // Masquer toutes les vues de contenu
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.style.display = 'none';
                });
                
                // Afficher la vue correspondante
                const view = this.getAttribute('data-view');
                document.getElementById(view + 'View').style.display = 'block';
            });
        });
        
        // Gestion du modal
        const modal = document.getElementById('appointmentModal');
        const newAppointmentBtn = document.getElementById('newAppointmentBtn');
        const closeModal = document.getElementById('closeModal');
        const cancelModal = document.getElementById('cancelModal');
        
        newAppointmentBtn.addEventListener('click', function() {
            modal.classList.add('active');
        });
        
        closeModal.addEventListener('click', function() {
            modal.classList.remove('active');
        });
        
        cancelModal.addEventListener('click', function() {
            modal.classList.remove('active');
        });
        
        // Fermer le modal si l'utilisateur clique en dehors
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('active');
            }
        });
        
        // Logique de filtrage du formulaire de rendez-vous
        const specialitySelect = document.getElementById('speciality');
        const doctorSelect = document.getElementById('doctor');
        
        specialitySelect.addEventListener('change', function() {
            const selectedSpeciality = this.value;
            
            // Réinitialiser les options du médecin
            doctorSelect.innerHTML = '<option value="">Sélectionnez un médecin</option>';
            
            // Si aucune spécialité n'est sélectionnée, ne rien faire de plus
            if (!selectedSpeciality) return;
            
            // Simuler une recherche de médecins par spécialité
            // Dans une application réelle, cela serait une requête AJAX
            const doctors = {
                '1': [
                    {id: 1, name: 'Dr. Thomas Leroy'},
                    {id: 5, name: 'Dr. Michel Bernard'}
                ],
                '2': [
                    {id: 2, name: 'Dr. Sophie Martin'},
                    {id: 6, name: 'Dr. Antoine Durand'}
                ],
                '3': [
                    {id: 3, name: 'Dr. Philippe Dubois'}
                ],
                '4': [
                    {id: 7, name: 'Dr. Claire Mercier'}
                ],
                '5': [
                    {id: 4, name: 'Dr. Isabelle Klein'}
                ],
                '6': [
                    {id: 8, name: 'Dr. François Lambert'}
                ]
            };
            
            // Ajouter les médecins correspondants
            if (doctors[selectedSpeciality]) {
                doctors[selectedSpeciality].forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.name;
                    doctorSelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>