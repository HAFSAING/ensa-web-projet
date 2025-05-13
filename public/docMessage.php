<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie - MediStatView</title>
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

        /* Layout pour la messagerie */
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

        /* Section principale de messagerie */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
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

        /* Interface de messagerie */
        .messaging-container {
            display: flex;
            height: 70vh;
        }

        /* Liste des conversations */
        .conversations-list {
            width: 300px;
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            flex-shrink: 0;
        }

        .search-box {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(29, 86, 107, 0.2);
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .conversation-item:hover {
            background-color: var(--light-bg);
        }

        .conversation-item.active {
            background-color: rgba(29, 86, 107, 0.1);
            border-left: 3px solid var(--primary-color);
        }

        .conversation-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            position: relative;
        }

        .online-indicator {
            width: 12px;
            height: 12px;
            background-color: #4caf50;
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
            border: 2px solid white;
        }

        .conversation-info {
            flex-grow: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.3rem;
        }

        .conversation-name {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .conversation-time {
            font-size: 0.8rem;
            color: #777;
        }

        .conversation-preview {
            font-size: 0.85rem;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
        }

        .message-status {
            display: inline-flex;
            margin-right: 0.3rem;
        }

        .message-count {
            width: 20px;
            height: 20px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        /* Zone de chat */
        .chat-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-with {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .chat-name {
            font-weight: 600;
        }

        .chat-status {
            font-size: 0.8rem;
            color: #4caf50;
        }

        .chat-actions {
            display: flex;
            gap: 0.7rem;
        }

        .chat-action {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            background-color: var(--light-bg);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chat-action:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .messages-container {
            flex-grow: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: #f5f7fa;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            max-width: 70%;
            padding: 1rem;
            border-radius: 12px;
            position: relative;
            line-height: 1.5;
        }

        .message-received {
            align-self: flex-start;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-bottom-left-radius: 2px;
        }

        .message-sent {
            align-self: flex-end;
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .message-time {
            font-size: 0.75rem;
            margin-top: 0.5rem;
            color: #999;
            align-self: flex-end;
        }

        .message-sent .message-time {
            color: rgba(255, 255, 255, 0.7);
        }

        .message-input-container {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .message-attachment {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-bg);
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-attachment:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .message-input {
            flex-grow: 1;
            padding: 0.8rem 1.2rem;
            border-radius: 25px;
            border: 1px solid var(--border-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .message-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(29, 86, 107, 0.2);
        }

        .message-send {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-send:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .date-separator {
            align-self: center;
            padding: 0.3rem 1rem;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 15px;
            font-size: 0.8rem;
            color: #777;
            margin: 1rem 0;
        }

        /* Filtres */
        .filter-tabs {
            display: flex;
            padding: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .filter-tab {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            color: #777;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .filter-tab.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .filter-tab:hover {
            color: var(--primary-color);
        }

        /* Section vide, pas encore de message sélectionné */
        .empty-state {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            background-color: #f5f7fa;
            color: #777;
        }

        .empty-icon {
            font-size: 5rem;
            color: var(--accent-color2);
            opacity: 0.5;
        }

        .empty-text {
            font-size: 1.2rem;
            max-width: 300px;
            text-align: center;
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
            .messaging-container {
                flex-direction: column;
                height: auto;
            }

            .conversations-list {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                max-height: 300px;
            }

            .message {
                max-width: 85%;
            }
        }

        @media (max-width: 576px) {
            .header-content {
                justify-content: center;
            }

            .chat-header {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }

            .chat-actions {
                align-self: flex-end;
            }

            .message-input-container {
                padding: 0.5rem;
            }

            .message-input {
                padding: 0.6rem 1rem;
            }

            .message-attachment, .message-send {
                width: 38px;
                height: 38px;
            }
        }
         /* Footer */
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
    <!-- Ajouter Font Awesome pour les icônes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
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
                            <a href="#" class="nav-link">
                                <i class="fas fa-home"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-injured"></i>
                                <span>Patients</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Rendez-vous</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-file-medical"></i>
                                <span>Dossiers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Prescriptions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
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
                        <a href="#"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="#"><i class="fas fa-bell"></i> Notifications</a>
                        <div class="dropdown-divider"></div>
                        <a href="#"><i class="fas fa-question-circle"></i> Aide & Support</a>
                        <a href="#" style="color: #d32f2f;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
                    <a href="#">
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
                    <a href="#" class="active">
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
            <!-- Card de messagerie -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Messages</h2>
                    <button class="action-button" style="width: auto; padding: 0.5rem 1rem; background-color: var(--primary-color); color: white; border: none; border-radius: 6px; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <i class="fas fa-plus"></i>
                        <span>Nouveau message</span>
                    </button>
                </div>
                
                <div class="messaging-container">
                    <!-- Liste des conversations -->
                    <div class="conversations-list">
                        <div class="search-box">
                            <input type="text" class="search-input" placeholder="Rechercher une conversation...">
                        </div>
                        
                        <div class="filter-tabs">
                            <div class="filter-tab active">Tous</div>
                            <div class="filter-tab">Patients</div>
                            <div class="filter-tab">Collègues</div>
                        </div>
                        
                        <div class="conversation-item active">
                            <div class="conversation-avatar">
                                SM
                                <div class="online-indicator"></div>
                            </div>
                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <div class="conversation-name">Sophie Martin</div>
                                    <div class="conversation-time">10:30</div>
                                </div>
                                <div class="conversation-preview">
                                    <div class="message-status">
                                        <i class="fas fa-check-double" style="color: #4caf50; font-size: 0.8rem; margin-right: 0.3rem;"></i>
                                    </div>
                                    Je vous confirme mon rendez-vous de demain
                                </div>
                            </div>
                        </div>
                        
                        <div class="conversation-item">
                            <div class="conversation-avatar">TD</div>
                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <div class="conversation-name">Thomas Dubois</div>
                                    <div class="conversation-time">Hier</div>
                                </div>
                                <div class="conversation-preview">
                                    Merci pour les résultats d'analyses, je...
                                    <div class="message-count">2</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="conversation-item">
                            <div class="conversation-avatar">
                                DR. L
                                <div class="online-indicator"></div>
                            </div>
                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <div class="conversation-name">Dr. Leblanc</div>
                                    <div class="conversation-time">Hier</div>
                                </div>
                                <div class="conversation-preview">
                                    <div class="message-status">
                                        <i class="fas fa-check" style="color: #777; font-size: 0.8rem; margin-right: 0.3rem;"></i>
                                    </div>
                                    Avis sur dossier patient #2468
                                </div>
                            </div>
                        </div>
                        
                        <div class="conversation-item">
                            <div class="conversation-avatar">EL</div>
                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <div class="conversation-name">Émilie Leroy</div>
                                    <div class="conversation-time">04/05</div>
                                </div>