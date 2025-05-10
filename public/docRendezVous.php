<?php
session_start();


// Inclusion du fichier de connexion à la base de données
require_once __DIR__ . '/../config/database.php';


// Connexion à la base de données
try {
    $pdo = getDatabaseConnection();
    
    // Requête pour récupérer les rendez-vous du médecin
    $query = "SELECT r.*, p.nom AS patient_nom, p.prenom AS patient_prenom 
              FROM rendez_vous r
              JOIN patients p ON r.patient_id = p.id
              WHERE r.medecin_id = :medecin_id
              ORDER BY r.date_heure DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
    $stmt->execute();
    $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Erreur lors de la récupération des rendez-vous: " . $e->getMessage());
}
// Pour débogage - afficher les rendez-vous récupérés
echo "<pre>";
print_r($rendezvous);
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous - MediStatView</title>
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

        /* Filtres */
        .filters-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .filter-select, .filter-input {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: white;
        }

        .filter-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: flex-end;
        }

        .filter-button:hover {
            background-color: #164455;
        }

        /* Tableau des rendez-vous */
        .appointment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointment-table th,
        .appointment-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .appointment-table th {
            color: var(--primary-color);
            font-weight: 600;
            background-color: rgba(29, 86, 107, 0.05);
        }

        .appointment-avatar {
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

        .appointment-patient-cell {
            display: flex;
            align-items: center;
        }

        .appointment-patient-name {
            font-weight: 500;
        }

        .appointment-date-cell {
            white-space: nowrap;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-confirmed {
            background-color: #e0f7fa;
            color: #0288d1;
        }

        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .status-pending {
            background-color: #fff8e1;
            color: #ff8f00;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .action-buttons {
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

        /* Bouton ajouter rendez-vous */
        .add-appointment-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: auto;
        }

        .add-appointment-btn:hover {
            background-color: #1a5a42;
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
            .appointment-table th:nth-child(4),
            .appointment-table td:nth-child(4),
            .appointment-table th:nth-child(5),
            .appointment-table td:nth-child(5) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .appointment-table th:nth-child(3),
            .appointment-table td:nth-child(3) {
                display: none;
            }
            
            .appointment-patient-cell {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .appointment-avatar {
                margin-right: 0;
                margin-bottom: 0.5rem;
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
                            <a href="docDashboard.php" class="nav-link">
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
                            <a href="docRendezVous.php" class="nav-link active">
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
                        <a href="Deconnection.php" style="color: #d32f2f;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
                    <a href="docDashboard.php">
                        <i class="fas fa-th-large"></i>
                        <span>Vue d'ensemble</span>
                    </a>
                </li>
                <li>
                    <a href="docPatient.php">
                        <i class="fas fa-user-injured"></i>
                        <span>Liste des patients</span>
                    </a>
                </li>
                <li>
                    <a href="docRendezVous.php" class="active">
                        <i class="fas fa-calendar-check"></i>
                        <span>Rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="docDossier.php">
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
                    <a href="docPrescriptions.php">
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
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Gestion des rendez-vous</h2>
                    <button class="add-appointment-btn">
                        <i class="fas fa-plus"></i>
                        <span>Nouveau rendez-vous</span>
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="filters-container">
                        <div class="filter-group">
                            <label for="status-filter" class="filter-label">Statut</label>
                            <select id="status-filter" class="filter-select">
                                <option value="all">Tous les statuts</option>
                                <option value="confirme">Confirmé</option>
                                <option value="en_attente">En attente</option>
                                <option value="annule">Annulé</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="date-filter" class="filter-label">Date</label>
                            <select id="date-filter" class="filter-select">
                                <option value="all">Toutes les dates</option>
                                <option value="today">Aujourd'hui</option>
                                <option value="week">Cette semaine</option>
                                <option value="month">Ce mois</option>
                                <option value="future">Futurs</option>
                                <option value="past">Passés</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="search-filter" class="filter-label">Recherche</label>
                            <input type="text" id="search-filter" class="filter-input" placeholder="Nom patient...">
                        </div>
                        <button class="filter-button">
                            <i class="fas fa-filter"></i>
                            <span>Filtrer</span>
                        </button>
                    </div>

                    <!-- Tableau des rendez-vous -->
                    <table class="appointment-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date et heure</th>
                                <th>Motif</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rendezvous as $rdv): 
                                // Formatage de la date et heure
                                $date_heure = new DateTime($rdv['date_heure']);
                                $date_formatted = $date_heure->format('d/m/Y');
                                $heure_formatted = $date_heure->format('H:i');
                                
                                // Initiales du patient
                                $initiales = substr($rdv['patient_prenom'], 0, 1) . substr($rdv['patient_nom'], 0, 1);
                                
                                // Classe CSS selon le statut
                                $status_class = '';
                                switch ($rdv['statut']) {
                                    case 'confirme':
                                        $status_class = 'status-confirmed';
                                        break;
                                    case 'annule':
                                        $status_class = 'status-cancelled';
                                        break;
                                    case 'en_attente':
                                        $status_class = 'status-pending';
                                        break;
                                    case 'termine':
                                        $status_class = 'status-completed';
                                        break;
                                }
                            ?>
                            <tr>
                                <td class="appointment-patient-cell">
                                    <div class="appointment-avatar"><?= strtoupper($initiales) ?></div>
                                    <div>
                                        <div class="appointment-patient-name"><?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?></div>
                                    </div>
                                </td>
                                <td class="appointment-date-cell">
                                    <div><?= $date_formatted ?></div>
                                    <div><?= $heure_formatted ?></div>
                                </td>
                                <td><?= htmlspecialchars($rdv['motif']) ?></td>
                                <td>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= ucfirst(str_replace('_', ' ', $rdv['statut'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-button" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-button" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($rdv['statut'] == 'en_attente' || $rdv['statut'] == 'confirme'): ?>
                                        <button class="action-button" title="Annuler">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script pour la gestion des filtres (à implémenter)
        document.addEventListener('DOMContentLoaded', function() {
            const filterButton = document.querySelector('.filter-button');
            const statusFilter = document.getElementById('status-filter');
            const dateFilter = document.getElementById('date-filter');
            const searchFilter = document.getElementById('search-filter');
            
            filterButton.addEventListener('click', function() {
                // Ici, vous pourriez ajouter la logique de filtrage
                // Par exemple, faire une requête AJAX ou filtrer côté client
                console.log('Filtrer par:', {
                    status: statusFilter.value,
                    date: dateFilter.value,
                    search: searchFilter.value
                });
            });
            
            // Bouton pour ajouter un nouveau rendez-vous
            const addButton = document.querySelector('.add-appointment-btn');
            addButton.addEventListener('click', function() {
                // Redirection vers la page de création de rendez-vous
                window.location.href = 'docNouveauRendezVous.php';
            });
        });
    </script>
</body>
</html>