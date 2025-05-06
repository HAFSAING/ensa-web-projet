<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStatView - Gestion des Rendez-vous</title>
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
        
        /* Page Title */
        .page-title {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 2rem 1rem;
            text-align: center;
            position: relative;
        }
        
        .page-title h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
        
        .page-title p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }
        
        /* Rendez-vous Container */
        .rdv-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }
        
        /* Sidebar */
        .sidebar {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            height: fit-content;
        }
        
        .sidebar h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color1);
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.8rem;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            border-radius: 6px;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(134, 179, 195, 0.1);
            color: var(--primary-color);
        }
        
        .sidebar-link.active {
            background-color: rgba(123, 186, 154, 0.15);
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .sidebar-icon {
            margin-right: 0.8rem;
            color: var(--accent-color2);
        }
        
        /* Main Content */
        .main-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .content-title {
            color: var(--primary-color);
            font-size: 1.6rem;
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 1.5rem;
        }
        
        .tab {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            font-weight: 500;
            color: #777;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab:hover {
            color: var(--secondary-color);
        }
        
        .tab.active {
            color: var(--secondary-color);
            border-bottom-color: var(--accent-color1);
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Calendar */
        .calendar {
            margin-bottom: 2rem;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .calendar-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--primary-color);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .calendar-btn:hover {
            background-color: rgba(134, 179, 195, 0.2);
        }
        
        .calendar-month {
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .calendar-day {
            text-align: center;
            padding: 0.5rem;
            font-weight: 500;
            color: #777;
        }
        
        .calendar-date {
            aspect-ratio: 1 / 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .calendar-date:hover {
            background-color: rgba(134, 179, 195, 0.1);
        }
        
        .calendar-date.active {
            background-color: var(--accent-color1);
            color: white;
        }
        
        .calendar-date.has-event::after {
            content: '';
            position: absolute;
            bottom: 6px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--accent-color3);
        }
        
        .calendar-date.active.has-event::after {
            background-color: white;
        }
        
        .calendar-date.today {
            border: 2px solid var(--accent-color2);
            font-weight: bold;
        }
        
        .calendar-date.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        
        /* Rendez-vous List */
        .rdv-list {
            margin-top: 2rem;
        }
        
        .rdv-filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .rdv-search {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        
        .rdv-search input {
            width: 100%;
            padding: 0.7rem 1rem 0.7rem 2.5rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .rdv-search i {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .rdv-filter-dropdown {
            position: relative;
        }
        
        .rdv-filter-btn {
            padding: 0.7rem 1rem;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .rdv-filter-dropdown-content {
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 200px;
            border-radius: 6px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            z-index: 10;
            padding: 0.8rem;
            margin-top: 0.5rem;
            display: none;
        }
        
        .rdv-filter-dropdown:hover .rdv-filter-dropdown-content {
            display: block;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .filter-option:hover {
            background-color: rgba(134, 179, 195, 0.1);
        }
        
        .filter-option input {
            margin-right: 0.8rem;
        }
        
        /* Rendez-vous Cards */
        .rdv-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .rdv-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #eee;
            transition: all 0.3s ease;
        }
        
        .rdv-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        
        .rdv-card-header {
            padding: 1rem;
            background-color: var(--accent-color2);
            color: white;
        }
        
        .rdv-card-header.confirme {
            background-color: var(--accent-color1);
        }
        
        .rdv-card-header.en-attente {
            background-color: #f5b041;
        }
        
        .rdv-card-header.annule {
            background-color: var(--accent-color3);
        }
        
        .rdv-status {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            background-color: rgba(255,255,255,0.2);
            margin-bottom: 0.5rem;
        }
        
        .rdv-card-body {
            padding: 1rem;
        }
        
        .rdv-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .rdv-info {
            list-style: none;
            margin-bottom: 1rem;
        }
        
        .rdv-info li {
            display: flex;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            color: #555;
        }
        
        .rdv-info-icon {
            width: 20px;
            margin-right: 0.8rem;
            color: var(--accent-color2);
        }
        
        .rdv-actions {
            border-top: 1px solid #eee;
            padding-top: 1rem;
            display: flex;
            justify-content: space-between;
        }
        
        .rdv-action-btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.3s ease;
        }
        
        .rdv-action-btn.edit {
            color: #3498db;
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .rdv-action-btn.delete {
            color: #e74c3c;
            background-color: rgba(231, 76, 60, 0.1);
        }
        
        .rdv-action-btn:hover {
            transform: translateY(-2px);
        }
        
        /* Create Rendez-vous Form */
        .create-rdv-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color2);
            box-shadow: 0 0 0 3px rgba(134, 179, 195, 0.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .rdv-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .mobile-menu-toggle {
                display: block;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: var(--primary-color);
                cursor: pointer;
            }
            
            .sidebar.active {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 280px;
                z-index: 1000;
                animation: slideIn 0.3s forwards;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(-100%);
                }
                to {
                    transform: translateX(0);
                }
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            .sidebar-close {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: var(--primary-color);
                cursor: pointer;
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .rdv-cards {
                grid-template-columns: 1fr;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .page-title h1 {
                font-size: 1.8rem;
            }
            
            .calendar-grid {
                gap: 2px;
            }
            
            .rdv-filters {
                flex-direction: column;
            }
            
            .rdv-filter-dropdown-content {
                left: 0;
                right: auto;
            }
        }
        
        /* Footer avec Google Maps et informations de contact */
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
                            <a href="#" class="nav-link active">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Rendez-vous</span>
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

    <!-- Page Title -->
    <section class="page-title">
        <div class="container">
            <h1>Gestion de vos Rendez-vous</h1>
            <p>Planifiez, gérez et suivez facilement tous vos rendez-vous médicaux en un seul endroit.</p>
        </div>
    </section>

    <!-- Main Content with Sidebar and Calendar -->
    <div class="rdv-container">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="openSidebar" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <button class="sidebar-close" id="closeSidebar" style="display: none;">
                <i class="fas fa-times"></i>
            </button>
            <h3>Menu</h3>
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="sidebar-link active">
                        <span class="sidebar-icon"><i class="fas fa-calendar-alt"></i></span>
                        Mes Rendez-vous
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <span class="sidebar-icon"><i class="fas fa-user-md"></i></span>
                        Mes Médecins
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <span class="sidebar-icon"><i class="fas fa-file-medical"></i></span>
                        Mes Documents
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <span class="sidebar-icon"><i class="fas fa-history"></i></span>
                        Historique
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <span class="sidebar-icon"><i class="fas fa-bell"></i></span>
                        Notifications
                        <span style="margin-left: auto; background-color: var(--accent-color3); color: white; font-size: 0.8rem; padding: 0.2rem 0.5rem; border-radius: 10px;">3</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link">
                        <span class="sidebar-icon"><i class="fas fa-cog"></i></span>
                        Paramètres
                    </a>
                </li>
            </ul>
            
            <h3 style="margin-top: 2rem;">Statistiques</h3>
            <div style="background-color: rgba(134, 179, 195, 0.1); border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                <p><strong>Ce mois-ci :</strong></p>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                    <span>Rendez-vous</span>
                    <span>8</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                    <span>Confirmés</span>
                    <span>5</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                    <span>En attente</span>
                    <span>2</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                    <span>Annulés</span>
                    <span>1</span>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h2 class="content-title">Mes Rendez-vous</h2>
                <a href="#" class="btn btn-primary" id="newRdvBtn">
                    <i class="fas fa-plus"></i>
                    Nouveau rendez-vous
                </a>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="tabs">
                <div class="tab active" data-tab="calendar">Calendrier</div>
                <div class="tab" data-tab="list">Liste</div>
                <div class="tab" data-tab="create">Créer</div>
            </div>
            
            <!-- Calendar Tab Content -->
            <div class="tab-content active" id="calendar-tab">
                <div class="calendar">
                    <div class="calendar-header">
                        <div class="calendar-nav">
                            <button class="calendar-btn" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="calendar-month">Mai 2025</span>
                            <button class="calendar-btn" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline">
                                <i class="fas fa-sync-alt"></i>
                                Aujourd'hui
                            </button>
                        </div>
                    </div>
                    <div class="calendar-weekdays calendar-grid">
                        <div class="calendar-day">Lun</div>
                        <div class="calendar-day">Mar</div>
                        <div class="calendar-day">Mer</div>
                        <div class="calendar-day">Jeu</div>
                        <div class="calendar-day">Ven</div>
                        <div class="calendar-day">Sam</div>
                        <div class="calendar-day">Dim</div>
                    </div>
                    <div class="calendar-dates calendar-grid">
                        <div class="calendar-date disabled">28</div>
                        <div class="calendar-date disabled">29</div>
                        <div class="calendar-date disabled">30</div>
                        <div class="calendar-date">1</div>
                        <div class="calendar-date">2</div>
                        <div class="calendar-date has-event">3</div>
                        <div class="calendar-date">4</div>
                        <div class="calendar-date">5</div>
                        <div class="calendar-date today active">6</div>
                        <div class="calendar-date has-event">7</div>
                        <div class="calendar-date">8</div>
                        <div class="calendar-date">9</div>
                        <div class="calendar-date">10</div>
                        <div class="calendar-date">11</div>
                        <div class="calendar-date">12</div>
                        <div class="calendar-date">13</div>
                        <div class="calendar-date has-event">14</div>
                        <div class="calendar-date">15</div>
                        <div class="calendar-date">16</div>
                        <div class="calendar-date">17</div>
                        <div class="calendar-date">18</div>
                        <div class="calendar-date">19</div>
                        <div class="calendar-date">20</div>
                        <div class="calendar-date has-event">21</div>
                        <div class="calendar-date">22</div>
                        <div class="calendar-date">23</div>
                        <div class="calendar-date">24</div>
                        <div class="calendar-date">25</div>
                        <div class="calendar-date">26</div>
                        <div class="calendar-date">27</div>
                        <div class="calendar-date">28</div>
                        <div class="calendar-date">29</div>
                        <div class="calendar-date">30</div>
                        <div class="calendar-date">31</div>
                        <div class="calendar-date disabled">1</div>
                    </div>
                </div>
                
                <h3>Rendez-vous du 6 mai 2025</h3>
                <div class="rdv-cards">
                    <div class="rdv-card">
                        <div class="rdv-card-header confirme">
                            <div class="rdv-status">Confirmé</div>
                            <div>10:30 - 11:00</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Consultation générale</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-user-md"></i></span>
                                    Dr. Martin Dupont
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-stethoscope"></i></span>
                                    Médecin généraliste
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    Cabinet médical St-Michel
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-file-medical-alt"></i></span>
                                    Contrôle annuel
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-pen"></i>
                                    Modifier
                                </a>
                                <a href="#" class="rdv-action-btn delete">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rdv-card">
                        <div class="rdv-card-header en-attente">
                            <div class="rdv-status">En attente</div>
                            <div>15:00 - 15:30</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Analyse de sang</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-vial"></i></span>
                                    Laboratoire Central
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    15 rue des Lilas
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-file-medical-alt"></i></span>
                                    Bilan sanguin complet
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-pen"></i>
                                    Modifier
                                </a>
                                <a href="#" class="rdv-action-btn delete">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- List Tab Content -->
            <div class="tab-content" id="list-tab">
                <div class="rdv-filters">
                    <div class="rdv-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher un rendez-vous...">
                    </div>
                    <div class="rdv-filter-dropdown">
                        <button class="rdv-filter-btn">
                            <i class="fas fa-filter"></i>
                            Filtrer
                        </button>
                        <div class="rdv-filter-dropdown-content">
                            <div class="filter-option">
                                <input type="checkbox" id="filter-all" checked>
                                <label for="filter-all">Tous</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filter-confirmed">
                                <label for="filter-confirmed">Confirmés</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filter-pending">
                                <label for="filter-pending">En attente</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filter-canceled">
                                <label for="filter-canceled">Annulés</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="rdv-cards">
                    <div class="rdv-card">
                        <div class="rdv-card-header confirme">
                            <div class="rdv-status">Confirmé</div>
                            <div>6 mai 2025 - 10:30</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Consultation générale</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-user-md"></i></span>
                                    Dr. Martin Dupont
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-stethoscope"></i></span>
                                    Médecin généraliste
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    Cabinet médical St-Michel
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-pen"></i>
                                    Modifier
                                </a>
                                <a href="#" class="rdv-action-btn delete">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rdv-card">
                        <div class="rdv-card-header en-attente">
                            <div class="rdv-status">En attente</div>
                            <div>6 mai 2025 - 15:00</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Analyse de sang</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-vial"></i></span>
                                    Laboratoire Central
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    15 rue des Lilas
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-pen"></i>
                                    Modifier
                                </a>
                                <a href="#" class="rdv-action-btn delete">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rdv-card">
                        <div class="rdv-card-header confirme">
                            <div class="rdv-status">Confirmé</div>
                            <div>7 mai 2025 - 14:15</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Suivi dentaire</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-tooth"></i></span>
                                    Dr. Sophie Laurent
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    Centre dentaire Montparnasse
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-pen"></i>
                                    Modifier
                                </a>
                                <a href="#" class="rdv-action-btn delete">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rdv-card">
                        <div class="rdv-card-header confirme">
                            <div class="rdv-status">Confirmé</div>
                            <div>14 mai 2025 - 09:00</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Consultation spécialiste</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-user-md"></i></span>
                                    Dr. Philippe Moreau
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-stethoscope"></i></span>
                                    Cardiologue
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    Hôpital Saint-Louis
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-pen"></i>
                                    Modifier
                                </a>
                                <a href="#" class="rdv-action-btn delete">
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rdv-card">
                        <div class="rdv-card-header annule">
                            <div class="rdv-status">Annulé</div>
                            <div>21 mai 2025 - 11:30</div>
                        </div>
                        <div class="rdv-card-body">
                            <h4 class="rdv-title">Contrôle ophtalmologie</h4>
                            <ul class="rdv-info">
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-eye"></i></span>
                                    Dr. Élise Petit
                                </li>
                                <li>
                                    <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    Cabinet Vision Plus
                                </li>
                            </ul>
                            <div class="rdv-actions">
                                <a href="#" class="rdv-action-btn edit">
                                    <i class="fas fa-redo"></i>
                                    Reprogrammer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Create Tab Content -->
            <div class="tab-content" id="create-tab">
                <div class="create-rdv-form">
                    <h3>Nouveau rendez-vous</h3>
                    <form action="#" method="post">
                        <div class="form-group">
                            <label for="rdv-type">Type de rendez-vous</label>
                            <select class="form-control" id="rdv-type">
                                <option value="consultation">Consultation</option>
                                <option value="analyse">Analyse</option>
                                <option value="suivi">Suivi</option>
                                <option value="controle">Contrôle</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="rdv-doctor">Médecin ou professionnel</label>
                            <input type="text" class="form-control" id="rdv-doctor" placeholder="Nom du praticien">
                        </div>
                        
                        <div class="form-group">
                            <label for="rdv-speciality">Spécialité</label>
                            <input type="text" class="form-control" id="rdv-speciality" placeholder="Spécialité du praticien">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rdv-date">Date</label>
                                <input type="date" class="form-control" id="rdv-date">
                            </div>
                            
                            <div class="form-group">
                                <label for="rdv-time">Heure</label>
                                <input type="time" class="form-control" id="rdv-time">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="rdv-location">Lieu</label>
                            <input type="text" class="form-control" id="rdv-location" placeholder="Adresse du rendez-vous">
                        </div>
                        
                        <div class="form-group">
                            <label for="rdv-notes">Notes</label>
                            <textarea class="form-control" id="rdv-notes" rows="3" placeholder="Précisions importantes concernant ce rendez-vous"></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline">Annuler</button>
                            <button type="submit" class="btn btn-primary">Créer le rendez-vous</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>À propos de MediStatView</h3>
                <p>MediStatView est une plateforme qui simplifie la gestion de vos rendez-vous médicaux et vous permet de suivre votre santé de manière efficace.</p>
                <p>Notre mission est de rendre les soins médicaux plus accessibles et organisés pour tous.</p>
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3>Liens rapides</h3>
                <ul class="footer-links">
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">Trouver un médecin</a></li>
                    <li><a href="#">Pharmacies</a></li>
                    <li><a href="#">Centre d'aide</a></li>
                    <li><a href="#">Blog santé</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-column footer-contact">
                <h3>Contact</h3>
                <p><span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span> 123 Avenue des Soins, 75000 Paris</p>
                <p><span class="contact-icon"><i class="fas fa-phone-alt"></i></span> +33 1 23 45 67 89</p>
                <p><span class="contact-icon"><i class="fas fa-envelope"></i></span> contact@medistatview.fr</p>
                <p><span class="contact-icon"><i class="fas fa-clock"></i></span> Lun-Ven: 9h-18h</p>
            </div>
            
            <div class="footer-column footer-map">
                <h3>Nous trouver</h3>
                <div style="background-color: #ddd; width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <span style="color: #666;">Carte Google Maps</span>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2025 MediStatView - Tous droits réservés</p>
            <div class="legal-links">
                <a href="#">Conditions d'utilisation</a> | 
                <a href="#">Politique de confidentialité</a> | 
                <a href="#">Mentions légales</a>
            </div>
        </div>
    </footer>

    <!-- Script pour les fonctionnalités interactives -->
    <script>
        // Navigation par onglets
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                
                // Désactiver tous les onglets
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Activer l'onglet cliqué
                tab.classList.add('active');
                document.getElementById(`${target}-tab`).classList.add('active');
            });
        });
        
        // Menu mobile
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        // Vérifier si l'écran est de petite taille
        const checkScreenSize = () => {
            if (window.innerWidth <= 992) {
                openSidebarBtn.style.display = 'block';
                closeSidebarBtn.style.display = 'block';
                sidebar.classList.remove('active');
            } else {
                openSidebarBtn.style.display = 'none';
                closeSidebarBtn.style.display = 'none';
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        };
        
        // Vérifier la taille de l'écran au chargement et au redimensionnement
        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);
        
        // Ouvrir le menu latéral
        openSidebarBtn.addEventListener('click', () => {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
        });
        
        // Fermer le menu latéral
        closeSidebarBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
        
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
        
        // Dates du calendrier cliquables
        const calendarDates = document.querySelectorAll('.calendar-date:not(.disabled)');
        
        calendarDates.forEach(date => {
            date.addEventListener('click', () => {
                calendarDates.forEach(d => d.classList.remove('active'));
                date.classList.add('active');
            });
        });
    </script>
    
    <!-- Font Awesome pour les icônes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>