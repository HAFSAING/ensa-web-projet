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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
            width: 100%;
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
            gap: 1rem;
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
        
        /* Main Content */
        main {
            flex: 1;
        }
        
        /* Rendez-vous Container */
        .rdv-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
        }
        
        /* Sidebar */
        .sidebar {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 90px;
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
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-color2) #f1f1f1;
        }
        
        .tabs::-webkit-scrollbar {
            height: 5px;
        }
        
        .tabs::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .tabs::-webkit-scrollbar-thumb {
            background: var(--accent-color2);
            border-radius: 6px;
        }
        
        .tab {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            font-weight: 500;
            color: #777;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
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
            height: 100%;
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
            display: flex;
            flex-direction: column;
            height: calc(100% - 60px);
        }
        
        .rdv-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .rdv-info {
            list-style: none;
            margin-bottom: 1rem;
            flex-grow: 1;
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
            margin-top: auto;
        }
        
        .rdv-action-btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.3s ease;
            cursor: pointer;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        /* Simplified Form */
        .simplified-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        /* Footer avec Google Maps et informations de contact */
        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 4rem 2rem 2rem;
            margin-top: 2rem;
        }
        
        .footer-content {
            max-width: 1400px;
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
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
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
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .rdv-cards {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }
        
        @media (max-width: 992px) {
            .rdv-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
                position: sticky;
                top: 80px;
            }
            
            .mobile-menu-toggle {
                display: block !important;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: var(--primary-color);
                cursor: pointer;
                margin-bottom: 1rem;
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
            
            .tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 0.5rem;
                gap: 0;
            }
            
            .simplified-form {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
            
            .footer-column.footer-map {
                grid-column: span 2;
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
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .footer-column.footer-map {
                grid-column: span 1;
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

    <main>
        <!-- Main Content with Sidebar and Calendar -->
        <div class="container rdv-container">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="openSidebar" style="display: none;">
                <i class="fas fa-bars"></i> Menu
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
                            <span class="sidebar-icon"><i class="fas fa-plus-circle"></i></span>
                            Nouveau Rendez-vous
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
                            Rappels
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link">
                            <span class="sidebar-icon"><i class="fas fa-cog"></i></span>
                            Paramètres
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link">
                            <span class="sidebar-icon"><i class="fas fa-question-circle"></i></span>
                            Aide & Support
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Sidebar Overlay -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <!-- Main Content -->
            <div class="main-content">
                <div class="content-header">
                    <h2 class="content-title">Mes Rendez-vous</h2>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Rendez-vous
                    </a>
                </div>
                
                <!-- Simple Tabs -->
                <div class="tabs">
                    <div class="tab active" data-tab="upcoming">À venir</div>
                    <div class="tab" data-tab="past">Passés</div>
                    <div class="tab" data-tab="canceled">Annulés</div>
                    <div class="tab" data-tab="quick">Prise rapide</div>
                </div>
                
                <!-- Tab Content - Upcoming -->
                <div class="tab-content active" id="upcoming">
                    <!-- Simple Search and Filter -->
                    <div class="rdv-filters">
                        <div class="rdv-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Rechercher un rendez-vous...">
                        </div>
                        <div class="rdv-filter-dropdown">
                            <button class="rdv-filter-btn">
                                <i class="fas fa-filter"></i> Filtrer
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
                            </div>
                        </div>
                    </div>
                    
                    <!-- Simplified Calendar View -->
                    <div class="calendar">
                        <div class="calendar-header">
                            <div class="calendar-nav">
                                <button class="calendar-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="calendar-month">Mai 2025</span>
                                <button class="calendar-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="calendar-grid">
                            <div class="calendar-day">Lun</div>
                            <div class="calendar-day">Mar</div>
                            <div class="calendar-day">Mer</div>
                            <div class="calendar-day">Jeu</div>
                            <div class="calendar-day">Ven</div>
                            <div class="calendar-day">Sam</div>
                            <div class="calendar-day">Dim</div>
                            
                            <!-- Dates de la première semaine -->
                            <div class="calendar-date disabled">29</div>
                            <div class="calendar-date disabled">30</div>
                            <div class="calendar-date">1</div>
                            <div class="calendar-date">2</div>
                            <div class="calendar-date">3</div>
                            <div class="calendar-date">4</div>
                            <div class="calendar-date">5</div>
                            
                            <!-- Dates de la deuxième semaine -->
                            <div class="calendar-date today">6</div>
                            <div class="calendar-date has-event">7</div>
                            <div class="calendar-date">8</div>
                            <div class="calendar-date">9</div>
                            <div class="calendar-date has-event">10</div>
                            <div class="calendar-date">11</div>
                            <div class="calendar-date">12</div>
                            
                            <!-- Dates de la troisième semaine -->
                            <div class="calendar-date">13</div>
                            <div class="calendar-date">14</div>
                            <div class="calendar-date active has-event">15</div>
                            <div class="calendar-date">16</div>
                            <div class="calendar-date">17</div>
                            <div class="calendar-date">18</div>
                            <div class="calendar-date">19</div>
                            
                            <!-- Dates de la quatrième semaine -->
                            <div class="calendar-date">20</div>
                            <div class="calendar-date has-event">21</div>
                            <div class="calendar-date">22</div>
                            <div class="calendar-date">23</div>
                            <div class="calendar-date">24</div>
                            <div class="calendar-date">25</div>
                            <div class="calendar-date">26</div>
                            
                            <!-- Dates de la cinquième semaine -->
                            <div class="calendar-date">27</div>
                            <div class="calendar-date">28</div>
                            <div class="calendar-date">29</div>
                            <div class="calendar-date">30</div>
                            <div class="calendar-date">31</div>
                            <div class="calendar-date disabled">1</div>
                            <div class="calendar-date disabled">2</div>
                        </div>
                    </div>
                    
                    <!-- Liste de rendez-vous simplifiée -->
                    <div class="rdv-list">
                        <h3>Rendez-vous du 15 Mai 2025</h3>
                        <div class="rdv-cards">
                            <!-- Carte de rendez-vous 1 -->
                            <div class="rdv-card">
                                <div class="rdv-card-header confirme">
                                    <span class="rdv-status">Confirmé</span>
                                    <h3>9:00 - 9:45</h3>
                                </div>
                                <div class="rdv-card-body">
                                    <h4 class="rdv-title">Dr. Martin Dubois</h4>
                                    <ul class="rdv-info">
                                        <li>
                                            <span class="rdv-info-icon"><i class="fas fa-stethoscope"></i></span>
                                            <span>Cardiologie</span>
                                        </li>
                                        <li>
                                            <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                            <span>Centre Médical Saint-Michel</span>
                                        </li>
                                        <li>
                                            <span class="rdv-info-icon"><i class="fas fa-info-circle"></i></span>
                                            <span>Contrôle annuel</span>
                                        </li>
                                    </ul>
                                    <div class="rdv-actions">
                                        <button class="rdv-action-btn edit">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="rdv-action-btn delete">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Carte de rendez-vous 2 -->
                            <div class="rdv-card">
                                <div class="rdv-card-header en-attente">
                                    <span class="rdv-status">En attente</span>
                                    <h3>14:30 - 15:15</h3>
                                </div>
                                <div class="rdv-card-body">
                                    <h4 class="rdv-title">Dr. Sophie Laurent</h4>
                                    <ul class="rdv-info">
                                        <li>
                                            <span class="rdv-info-icon"><i class="fas fa-stethoscope"></i></span>
                                            <span>Dermatologie</span>
                                        </li>
                                        <li>
                                            <span class="rdv-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                            <span>Clinique de la Peau</span>
                                        </li>
                                        <li>
                                            <span class="rdv-info-icon"><i class="fas fa-info-circle"></i></span>
                                            <span>Examen des grains de beauté</span>
                                        </li>
                                    </ul>
                                    <div class="rdv-actions">
                                        <button class="rdv-action-btn edit">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="rdv-action-btn delete">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content - Past -->
                <div class="tab-content" id="past">
                    <div class="rdv-list">
                        <p>Vos rendez-vous passés apparaîtront ici.</p>
                    </div>
                </div>
                
                <!-- Tab Content - Canceled -->
                <div class="tab-content" id="canceled">
                    <div class="rdv-list">
                        <p>Vos rendez-vous annulés apparaîtront ici.</p>
                    </div>
                </div>
                
                <!-- Tab Content - Quick Appointment -->
                <div class="tab-content" id="quick">
                    <h3>Prise de rendez-vous rapide</h3>
                    <p>Remplissez ce formulaire pour prendre rapidement un rendez-vous.</p>
                    
                    <div class="create-rdv-form">
                        <div class="simplified-form">
                            <div class="form-group">
                                <label for="speciality">Spécialité</label>
                                <select class="form-control" id="speciality">
                                    <option value="">Sélectionnez une spécialité</option>
                                    <option value="cardio">Cardiologie</option>
                                    <option value="derma">Dermatologie</option>
                                    <option value="general">Médecine Générale</option>
                                    <option value="ophtalmo">Ophtalmologie</option>
                                    <option value="dental">Dentaire</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="doctor">Médecin</label>
                                <select class="form-control" id="doctor">
                                    <option value="">D'abord sélectionner une spécialité</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="rdv-date">Date souhaitée</label>
                                <input type="date" class="form-control" id="rdv-date">
                            </div>
                            
                            <div class="form-group">
                                <label for="rdv-time">Heure souhaitée</label>
                                <select class="form-control" id="rdv-time">
                                    <option value="">Sélectionnez une heure</option>
                                    <option value="matin">Matin (8h-12h)</option>
                                    <option value="midi">Midi (12h-14h)</option>
                                    <option value="aprem">Après-midi (14h-18h)</option>
                                    <option value="soir">Soir (18h-20h)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="rdv-reason">Motif</label>
                                <textarea class="form-control" id="rdv-reason" rows="3" placeholder="Décrivez brièvement le motif de votre consultation..."></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button class="btn btn-outline">Annuler</button>
                            <button class="btn btn-primary">Rechercher disponibilités</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>À propos</h3>
                    <p>MediStatView est votre plateforme de gestion de santé complète. Nous vous aidons à gérer vos rendez-vous, trouver des professionnels de santé et accéder à des informations médicales fiables.</p>
                    <div class="social-links">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Liens utiles</h3>
                    <ul class="footer-links">
                        <li><a href="#">Trouver un médecin</a></li>
                        <li><a href="#">Prendre rendez-vous</a></li>
                        <li><a href="#">Magazine santé</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Nous contacter</a></li>
                    </ul>
                </div>
                
                <div class="footer-column footer-contact">
                    <h3>Contact</h3>
                    <p><span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span> 123 Avenue de la Santé, 75001 Paris</p>
                    <p><span class="contact-icon"><i class="fas fa-phone"></i></span> +33 1 23 45 67 89</p>
                    <p><span class="contact-icon"><i class="fas fa-envelope"></i></span> contact@medistatview.fr</p>
                    <p><span class="contact-icon"><i class="fas fa-clock"></i></span> Lun-Ven: 8h-20h | Sam: 9h-18h</p>
                </div>
                
                <div class="footer-column footer-map">
                    <h3>Nous trouver</h3>
                    <div style="background-color: rgba(255,255,255,0.1); height: 200px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--accent-color1);">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 MediStatView. Tous droits réservés.</p>
                <div class="legal-links">
                    <a href="#">Mentions légales</a>
                    <a href="#">Politique de confidentialité</a>
                    <a href="#">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        // Simple Tab Functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show the corresponding tab content
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Mobile Menu Toggle
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (openSidebar && closeSidebar && sidebar && sidebarOverlay) {
            openSidebar.addEventListener('click', () => {
                sidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
            });

            closeSidebar.addEventListener('click', () => {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });

            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
        }

        // Dynamic Doctor Selection based on Specialty
        const specialitySelect = document.getElementById('speciality');
        const doctorSelect = document.getElementById('doctor');

        if (specialitySelect && doctorSelect) {
            specialitySelect.addEventListener('change', () => {
                const specialty = specialitySelect.value;
                doctorSelect.innerHTML = ''; // Clear previous options
                
                // Default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Choisissez un médecin';
                doctorSelect.appendChild(defaultOption);
                
                // Add doctors based on specialty
                if (specialty === 'cardio') {
                    const doctors = [
                        { value: 'dubois', name: 'Dr. Martin Dubois' },
                        { value: 'petit', name: 'Dr. Élise Petit' },
                        { value: 'moreau', name: 'Dr. Philippe Moreau' }
                    ];
                    
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.value;
                        option.textContent = doctor.name;
                        doctorSelect.appendChild(option);
                    });
                } else if (specialty === 'derma') {
                    const doctors = [
                        { value: 'laurent', name: 'Dr. Sophie Laurent' },
                        { value: 'dupont', name: 'Dr. Alexandre Dupont' }
                    ];
                    
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.value;
                        option.textContent = doctor.name;
                        doctorSelect.appendChild(option);
                    });
                } else if (specialty === 'general') {
                    const doctors = [
                        { value: 'bernard', name: 'Dr. Marie Bernard' },
                        { value: 'martin', name: 'Dr. Jean Martin' },
                        { value: 'lefebvre', name: 'Dr. Claire Lefebvre' }
                    ];
                    
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.value;
                        option.textContent = doctor.name;
                        doctorSelect.appendChild(option);
                    });
                } else if (specialty === 'ophtalmo') {
                    const doctors = [
                        { value: 'richard', name: 'Dr. Thomas Richard' },
                        { value: 'leroy', name: 'Dr. Amélie Leroy' }
                    ];
                    
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.value;
                        option.textContent = doctor.name;
                        doctorSelect.appendChild(option);
                    });
                } else if (specialty === 'dental') {
                    const doctors = [
                        { value: 'durand', name: 'Dr. Nicolas Durand' },
                        { value: 'mercier', name: 'Dr. Lucie Mercier' },
                        { value: 'fontaine', name: 'Dr. Pascal Fontaine' }
                    ];
                    
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.value;
                        option.textContent = doctor.name;
                        doctorSelect.appendChild(option);
                    });
                }
            });
        }
    </script>
</body>
</html>