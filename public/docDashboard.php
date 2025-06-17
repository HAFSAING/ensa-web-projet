<?php
session_start();

// Vérification si le médecin est connecté
if (!isset($_SESSION['medecin_id'])) {
    header("Location: docConnecter.php");
    exit();
}

// Inclusion du fichier de connexion à la base de données
require_once __DIR__ . '/../config/database.php';

// Obtenir la connexion PDO
try {
    $pdo = getDatabaseConnection();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer les informations du médecin connecté avec la spécialité
$medecin_id = $_SESSION['medecin_id'];
$stmt_medecin = $pdo->prepare("
    SELECT m.civilite, m.nom, m.prenom, s.nom AS specialite 
    FROM medecins m 
    LEFT JOIN specialites s ON m.specialite_id = s.id 
    WHERE m.id = ?
");
$stmt_medecin->execute([$medecin_id]);
$medecin = $stmt_medecin->fetch(PDO::FETCH_ASSOC);

// Initialiser les variables pour éviter les erreurs undefined
$total_patients = 0;
$total_rdv_month = 0;
$total_consultations = 0;
$total_prescriptions = 0;

// Récupérer les statistiques du médecin
$stmt_stats = $pdo->prepare("
    SELECT 
        (SELECT COUNT(DISTINCT c.patient_id) FROM consultations c WHERE c.medecin_id = ?) AS total_patients,
        (SELECT COUNT(*) FROM rendez_vous WHERE medecin_id = ? AND date_heure >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND statut = 'confirme') AS total_rdv_month,
        (SELECT COUNT(*) FROM consultations WHERE medecin_id = ? AND date_consultation >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND statut = 'terminee') AS total_consultations,
        (SELECT COUNT(*) FROM prescriptions p 
         JOIN consultations c ON p.consultation_id = c.id 
         WHERE c.medecin_id = ? AND c.date_consultation >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) AS total_prescriptions
");
$stmt_stats->execute([$medecin_id, $medecin_id, $medecin_id, $medecin_id]);
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

if ($stats) {
    $total_patients = $stats['total_patients'] ?? 0;
    $total_rdv_month = $stats['total_rdv_month'] ?? 0;
    $total_consultations = $stats['total_consultations'] ?? 0;
    $total_prescriptions = $stats['total_prescriptions'] ?? 0;
}

// Récupérer les prochains rendez-vous du médecin
$stmt_rendezvous = $pdo->prepare("
    SELECT r.id, r.date_heure, p.nom, p.prenom, r.statut, r.motif
    FROM rendez_vous r
    JOIN patients p ON r.patient_id = p.id
    WHERE r.medecin_id = ? AND r.date_heure >= CURDATE()
    ORDER BY r.date_heure ASC
    LIMIT 4
");
$stmt_rendezvous->execute([$medecin_id]);
$rendezvous = $stmt_rendezvous->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les patients récents consultés par le médecin
$stmt_patients = $pdo->prepare("
    SELECT p.id, p.nom, p.prenom, p.date_naissance, c.date_consultation
    FROM patients p
    JOIN consultations c ON p.id = c.patient_id
    WHERE c.medecin_id = ? AND c.date_consultation >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
    ORDER BY c.date_consultation DESC
    LIMIT 5
");
$stmt_patients->execute([$medecin_id]);
$patients = $stmt_patients->fetchAll(PDO::FETCH_ASSOC);

// Calcul de l'âge approximatif pour chaque patient
function calculateAge($birthDate) {
    if ($birthDate) {
        $birthDate = new DateTime($birthDate);
        $today = new DateTime('today');
        $age = $today->diff($birthDate)->y;
        return $age . ' ans';
    }
    return 'N/A';
}

// Données simulées pour l'activité mensuelle
$activite_mensuelle = [
    ['mois' => 'Jan', 'consultations' => 20, 'nouveaux_patients' => 5, 'prescriptions' => 15],
    ['mois' => 'Fév', 'consultations' => 25, 'nouveaux_patients' => 8, 'prescriptions' => 18],
    ['mois' => 'Mar', 'consultations' => 30, 'nouveaux_patients' => 10, 'prescriptions' => 22],
    ['mois' => 'Avr', 'consultations' => 28, 'nouveaux_patients' => 7, 'prescriptions' => 20],
    ['mois' => 'Mai', 'consultations' => 35, 'nouveaux_patients' => 12, 'prescriptions' => 25],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Médecin - MediStatView</title>
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

        main {
            flex: 1;
            padding: 2rem;
        }

        .tab {
            padding: 0.8rem 1.5rem;
            background-color: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab:hover {
            color: var(--primary-color);
            border-bottom: 3px solid var(--accent-color1);
        }

        .tab.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }

        .overview-section {
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            background-color: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background-color: var(--accent-color1);
            color: white;
            border-color: var(--accent-color1);
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .overview-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .overview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(123, 186, 154, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color1);
            font-size: 1.5rem;
        }

        .patients-icon {
            background-color: rgba(134, 179, 195, 0.2);
            color: var(--accent-color2);
        }

        .appointments-icon {
            background-color: rgba(33, 107, 78, 0.1);
            color: var(--secondary-color);
        }

        .consultations-icon {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }

        .card-content h3 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }

        .card-content p {
            font-size: 1rem;
            color: #666;
        }

        .activity-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }

        .consultations-legend {
            background-color: var(--primary-color);
        }

        .patients-legend {
            background-color: var(--accent-color1);
        }

        .prescriptions-legend {
            background-color: var(--accent-color2);
        }

        .appointments-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .appointments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .appointments-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .appointment-row {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .appointment-row:last-child {
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

        .appointment-time {
            font-size: 0.9rem;
            color: #666;
            margin-right: 1rem;
        }

        .appointment-info {
            flex: 1;
        }

        .appointment-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }

        .appointment-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .status-upcoming {
            background-color: rgba(123, 186, 154, 0.2);
            color: var(--accent-color1);
        }

        .status-cancelled {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }

        .appointment-details {
            color: #666;
            font-size: 0.9rem;
        }

        .appointment-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #164a5b;
        }

        .btn-secondary {
            background-color: var(--light-bg);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }

        .patients-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        .patients-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patients-table th,
        .patients-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .patients-table th {
            background-color: var(--light-bg);
            color: var(--primary-color);
            font-weight: 600;
        }

        .patient-row {
            transition: background-color 0.3s ease;
        }

        .patient-row:hover {
            background-color: var(--light-bg);
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
            margin-right: 0.5rem;
        }

        .patient-name {
            font-weight: 500;
        }

        .patient-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(123, 186, 154, 0.2);
            color: var(--accent-color1);
        }

        .patient-actions {
            display: flex;
            gap: 0.5rem;
        }

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

            main {
                padding: 1rem;
            }

            .overview-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .nav-item {
                flex-basis: 33.333%;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .patients-table thead {
                display: none;
            }

            .patients-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
            }

            .patients-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 1rem;
                border-bottom: none;
            }

            .patients-table td::before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
            }

            .patients-table td:last-child {
                border-bottom: none;
            }
        }

        @media (max-width: 576px) {
            .nav-item {
                flex-basis: 50%;
            }

            .nav-link {
                font-size: 0.8rem;
            }

            .overview-card {
                flex-direction: column;
                text-align: center;
            }

            .appointment-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .appointment-date {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }

            .appointment-time {
                margin-right: 0;
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
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <svg width="180" height="50" viewBox="0 0 180 50">
                        <rect x="10" y="15" width="20" height="20" fill="#76b5c5" />
                        <polygon points="30,15 40,25 30,35" fill="#a7c5d1" />
                        <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                        <text x="50" y="40" fill="#a7c5d1" font-size="12">SERVICES</text>
                    </svg>
                </div>
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item"><a href="docDashboard.php" class="nav-link active"><i class="fas fa-home"></i> Tableau de bord</a></li>
                        <li class="nav-item"><a href="docPatient.php" class="nav-link"><i class="fas fa-users"></i> Patients</a></li>
                        <li class="nav-item"><a href="docRendezVous.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Rendez-vous</a></li>
                        <li class="nav-item"><a href="docDossier.php" class="nav-link"><i class="fas fa-folder-open"></i> Dossiers</a></li>
                        <li class="nav-item"><a href="docPrescriptions.php" class="nav-link"><i class="fas fa-prescription"></i> Prescriptions</a></li>
                    </ul>
                </nav>
                
                <div class="profile-dropdown">
                    <button class="profile-button">
                        <div class="profile-avatar">
                            <?= substr($medecin['prenom'] ?? '', 0, 1) . substr($medecin['nom'] ?? '', 0, 1) ?>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name"><?= htmlspecialchars($medecin['civilite'] ?? '') . ' ' . htmlspecialchars($medecin['prenom'] ?? '') . ' ' . htmlspecialchars($medecin['nom'] ?? '') ?></div>
                            <div class="profile-title"><?= htmlspecialchars($medecin['specialite'] ?? 'Spécialité non définie') ?></div>
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

    <main class="container">
        <div class="tabs">
            <button class="tab active">Vue d'ensemble</button>
            <button class="tab">Liste des patients</button>
            <button class="tab">Rendez-vous</button>
            <button class="tab">Dossiers médicaux</button>
            <button class="tab">Notes cliniques</button>
            <button class="tab">Prescriptions</button>
            <button class="tab">Résultats d'analyses</button>
            <button class="tab">Statistiques</button>
            <button class="tab">Messagerie</button>
            <button class="tab">Paramètres</button>
        </div>

        <div class="overview-section">
            <div class="section-header">
                <h2 class="section-title">Vue d'ensemble</h2>
                <div class="filter-buttons">
                    <button class="filter-btn active">Aujourd'hui</button>
                    <button class="filter-btn">Cette semaine</button>
                    <button class="filter-btn">Ce mois</button>
                    <button class="filter-btn">Cette année</button>
                </div>
            </div>
            <div class="overview-grid">
                <div class="overview-card">
                    <div class="card-icon patients-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($total_patients) ?></h3>
                        <p>Patients Totaux</p>
                    </div>
                </div>
                <div class="overview-card">
                    <div class="card-icon appointments-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($total_rdv_month) ?></h3>
                        <p>Rendez-vous ce mois</p>
                    </div>
                </div>
                <div class="overview-card">
                    <div class="card-icon consultations-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($total_consultations) ?></h3>
                        <p>Consultations</p>
                    </div>
                </div>
                <div class="overview-card">
                    <div class="card-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($total_prescriptions) ?></h3>
                        <p>Prescriptions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="activity-section">
            <div class="section-header">
                <h2 class="section-title">Activité mensuelle</h2>
                <div class="filter-buttons">
                    <button class="filter-btn active">Cette année</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="activityChart"></canvas>
            </div>
            <div class="legend">
                <div class="legend-item">
                    <span class="legend-color consultations-legend"></span>
                    Consultations
                </div>
                <div class="legend-item">
                    <span class="legend-color patients-legend"></span>
                    Nouveaux patients
                </div>
                <div class="legend-item">
                    <span class="legend-color prescriptions-legend"></span>
                    Prescriptions
                </div>
            </div>
        </div>

        <div class="appointments-section">
            <div class="appointments-header">
                <h2 class="section-title">Prochains rendez-vous</h2>
                <a href="docRendezVous.php" class="btn btn-primary">Voir tous</a>
            </div>
            <div class="appointments-list">
                <?php foreach ($rendezvous as $rdv): ?>
                    <div class="appointment-row">
                        <div class="appointment-date">
                            <span class="appointment-day"><?= date('d', strtotime($rdv['date_heure'])) ?></span>
                            <span class="appointment-month"><?= date('M', strtotime($rdv['date_heure'])) ?></span>
                        </div>
                        <div class="appointment-time"><?= date('H:i', strtotime($rdv['date_heure'])) ?></div>
                        <div class="appointment-info">
                            <div class="appointment-name">
                                <?= htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']) ?>
                                <span class="appointment-status status-<?= $rdv['statut'] === 'annule' ? 'cancelled' : 'upcoming' ?>">
                                    <?= $rdv['statut'] === 'annule' ? 'Annulé' : 'À venir' ?>
                                </span>
                            </div>
                            <div class="appointment-details"><?= htmlspecialchars($rdv['motif']) ?></div>
                        </div>
                        <div class="appointment-actions">
                            <button class="btn btn-primary">Voir</button>
                            <button class="btn btn-secondary">Modifier</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="patients-section">
            <div class="section-header">
                <h2 class="section-title">Patients récents</h2>
                <a href="docPatient.php" class="btn btn-primary">Voir tous</a>
            </div>
            <table class="patients-table">
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
                    <?php foreach ($patients as $patient): 
                        $age = calculateAge($patient['date_naissance']);
                    ?>
                        <tr class="patient-row">
                            <td data-label="Patient">
                                <div style="display: flex; align-items: center;">
                                    <div class="patient-avatar">
                                        <?= substr($patient['prenom'] ?? '', 0, 1) . substr($patient['nom'] ?? '', 0, 1) ?>
                                    </div>
                                    <span class="patient-name"><?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?></span>
                                </div>
                            </td>
                            <td data-label="Âge"><?= htmlspecialchars($age) ?></td>
                            <td data-label="État">
                                <span class="patient-status status-active">
                                    <i class="fas fa-heartbeat"></i>
                                </span>
                            </td>
                            <td data-label="Dernière visite"><?= date('d M, Y', strtotime($patient['date_consultation'])) ?></td>
                            <td data-label="Actions" class="patient-actions">
                                <button class="btn btn-primary">Profil</button>
                                <button class="btn btn-secondary">Dossier</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php echo "'" . implode("','", array_column($activite_mensuelle, 'mois')) . "'"; ?>],
                datasets: [
                    {
                        label: 'Consultations',
                        data: [<?php echo implode(',', array_column($activite_mensuelle, 'consultations')); ?>],
                        borderColor: 'var(--primary-color)',
                        backgroundColor: 'rgba(29, 86, 107, 0.2)',
                        fill: true,
                    },
                    {
                        label: 'Nouveaux patients',
                        data: [<?php echo implode(',', array_column($activite_mensuelle, 'nouveaux_patients')); ?>],
                        borderColor: 'var(--accent-color1)',
                        backgroundColor: 'rgba(123, 186, 154, 0.2)',
                        fill: true,
                    },
                    {
                        label: 'Prescriptions',
                        data: [<?php echo implode(',', array_column($activite_mensuelle, 'prescriptions')); ?>],
                        borderColor: 'var(--accent-color2)',
                        backgroundColor: 'rgba(134, 179, 195, 0.2)',
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        document.querySelector('.user-btn').addEventListener('click', (event) => {
            event.stopPropagation();
            const dropdown = document.querySelector('.dropdown-menu');
            dropdown.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.querySelector('.dropdown-menu');
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>
</body>
</html>