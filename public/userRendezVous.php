<?php
session_start();

// Inclure la connexion à la base de données
require_once __DIR__ . '/../config/database.php';

// Obtenir la connexion PDO
$pdo = getDatabaseConnection();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['patient_id'])) {
    header("Location: userConnecter.php");
    exit();
}

// Récupérer les données du patient connecté
$patient_id = $_SESSION['patient_id'];
$stmt = $pdo->prepare("SELECT nom, prenom FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header("Location: userConnecter.php");
    exit();
}

// Gestion des filtres
$medecin = $_GET['medecin'] ?? '';
$specialite = $_GET['specialite'] ?? '';
$periode = $_GET['periode'] ?? '';
$date = $_GET['date'] ?? '';

$whereClauses = ["r.patient_id = ?"];
$params = [$patient_id];

if ($medecin) {
    $whereClauses[] = "CONCAT(m.prenom, ' ', m.nom) = ?";
    $params[] = $medecin;
}
if ($specialite) {
    $whereClauses[] = "s.nom = ?";
    $params[] = $specialite;
}
if ($periode === 'a_venir') {
    $whereClauses[] = "r.date_heure >= CURDATE()";
} elseif ($periode === 'passes') {
    $whereClauses[] = "r.date_heure < CURDATE()";
}
if ($date) {
    $whereClauses[] = "DATE(r.date_heure) = ?";
    $params[] = $date;
}

$query = "
    SELECT r.id, r.date_heure, m.nom AS medecin_nom, m.prenom AS medecin_prenom, s.nom AS specialite, r.motif, r.statut, r.notes
    FROM rendez_vous r
    JOIN medecins m ON r.medecin_id = m.id
    JOIN specialites s ON m.specialite_id = s.id
    WHERE " . implode(" AND ", $whereClauses) . "
    ORDER BY r.date_heure ASC
";
$stmt_rendezvous = $pdo->prepare($query);
$stmt_rendezvous->execute($params);
$rendezvous = $stmt_rendezvous->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les spécialités pour le formulaire
$stmt_specialites = $pdo->query("SELECT nom FROM specialites ORDER BY nom ASC");
$specialites = $stmt_specialites->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les médecins pour le formulaire
$stmt_medecins = $pdo->query("
    SELECT CONCAT(m.prenom, ' ', m.nom) AS nom_complet
    FROM medecins m
    WHERE m.statut = 'actif'
    ORDER BY nom_complet ASC
");
$medecins = $stmt_medecins->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStatView</title>
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
                        <li class="nav-item"><a href="userDashboard.php" class="nav-link"><i class="fas fa-home"></i> Tableau de bord</a></li>
                        <li class="nav-item"><a href="userDossier.php" class="nav-link"><i class="fas fa-folder-open"></i> Mon dossier</a></li>
                        <li class="nav-item"><a href="userRendezVous.php" class="nav-link active"><i class="fas fa-calendar-alt"></i> Rendez-vous</a></li>
                        <li class="nav-item"><a href="userMessage.php" class="nav-link"><i class="fas fa-envelope"></i> Messages</a></li>
                        <li class="nav-item"><a href="userStatistique.php" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                    </ul>
                </nav>
                <div class="profile-dropdown">
                    <button class="profile-button">
                        <div class="profile-avatar">
                            <?= substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1) ?>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name"><?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?></div>
                            <div class="profile-title">Patient</div>
                        </div>
                        <i class="fas fa-chevron-down" style="margin-left: 10px; font-size: 0.8rem;"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="#" class="dropdown-item"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Paramètres</a>
                        <div class="dropdown-divider"></div>
                        <a href="Deconnection.php" class="dropdown-item" style="color: #d32f2f;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h2 class="page-title">Mes Rendez-vous</h2>
            <div class="action-buttons">
                <a href="#newAppointmentModal" class="btn btn-primary">Nouveau rendez-vous</a>
                <a href="?refresh=true" class="btn btn-outline">Actualiser</a>
            </div>
        </div>

        <div class="filter-section">
            <h3 class="filter-title">Filtrer les rendez-vous</h3>
            <form method="get" class="filter-form">
                <div class="form-group">
                    <label for="medecin" class="form-label">Médecin</label>
                    <select id="medecin" name="medecin" class="form-control">
                        <option value="">Tous les médecins</option>
                        <?php foreach ($medecins as $medecin_option): ?>
                            <option value="<?= htmlspecialchars($medecin_option) ?>" <?= $medecin === $medecin_option ? 'selected' : '' ?>>
                                Dr. <?= htmlspecialchars($medecin_option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="specialite" class="form-label">Spécialité</label>
                    <select id="specialite" name="specialite" class="form-control">
                        <option value="">Toutes les spécialités</option>
                        <?php foreach ($specialites as $specialite_option): ?>
                            <option value="<?= htmlspecialchars($specialite_option) ?>" <?= $specialite === $specialite_option ? 'selected' : '' ?>>
                                <?= htmlspecialchars($specialite_option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="periode" class="form-label">Période</label>
                    <select id="periode" name="periode" class="form-control">
                        <option value="">Tous</option>
                        <option value="a_venir" <?= $periode === 'a_venir' ? 'selected' : '' ?>>À venir</option>
                        <option value="passes" <?= $periode === 'passes' ? 'selected' : '' ?>>Passés</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= $date ?>">
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="userRendezVous.php" class="btn btn-outline">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="view-toggle">
            <button class="view-btn active" data-view="list">Liste</button>
            <button class="view-btn" data-view="calendar">Calendrier</button>
        </div>

        <div class="appointments-list" id="listView">
            <?php if ($rendezvous): ?>
                <?php foreach ($rendezvous as $rdv): ?>
                    <div class="appointment-item">
                        <div class="appointment-date">
                            <span class="appointment-day"><?= date('d', strtotime($rdv['date_heure'])) ?></span>
                            <span class="appointment-month"><?= date('M', strtotime($rdv['date_heure'])) ?></span>
                        </div>
                        <div class="appointment-info">
                            <div class="appointment-meta">
                                <span class="appointment-time"><i class="far fa-clock"></i> <?= date('H:i', strtotime($rdv['date_heure'])) ?></span>
                                <span class="appointment-type"><?= $rdv['statut'] === 'annule' ? 'Annulé' : ($rdv['statut'] === 'termine' ? 'Terminé' : 'À venir') ?></span>
                            </div>
                            <div class="appointment-doctor"><?= htmlspecialchars($rdv['medecin_prenom'] . ' ' . $rdv['medecin_nom'] . ' - ' . $rdv['specialite']) ?></div>
                            <div class="appointment-details">
                                <div class="appointment-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($rdv['notes'] ?? 'Lieu non spécifié') ?></div>
                                <div class="appointment-note"><i class="fas fa-sticky-note"></i> <?= htmlspecialchars($rdv['motif']) ?></div>
                            </div>
                        </div>
                        <div class="appointment-actions">
                            <?php if ($rdv['statut'] !== 'termine' && $rdv['statut'] !== 'annule'): ?>
                                <button class="btn btn-sm btn-primary">Modifier</button>
                                <button class="btn btn-sm btn-danger">Annuler</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-primary">Rapport</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-calendar-exclamation no-data-icon"></i>
                    <h3 class="no-data-title">Aucun rendez-vous trouvé</h3>
                    <p class="no-data-message">Vous n'avez pas de rendez-vous correspondant à vos critères de recherche. Veuillez ajuster vos filtres ou créer un nouveau rendez-vous.</p>
                    <a href="#newAppointmentModal" class="btn btn-primary">Nouveau rendez-vous</a>
                </div>
            <?php endif; ?>
            <div class="pagination">
                <button class="page-item">1</button>
                <button class="page-item">2</button>
                <button class="page-item">3</button>
            </div>
        </div>

        <div class="calendar" id="calendarView" style="display: none;">
            <div class="calendar-header">
                <h2 class="calendar-title">Mai 2025</h2>
                <div class="calendar-nav">
                    <button class="calendar-nav-btn"><i class="fas fa-chevron-left"></i></button>
                    <button class="calendar-nav-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-grid">
                <div class="calendar-weekdays">
                    <span class="weekday">Lun</span>
                    <span class="weekday">Mar</span>
                    <span class="weekday">Mer</span>
                    <span class="weekday">Jeu</span>
                    <span class="weekday">Ven</span>
                    <span class="weekday">Sam</span>
                    <span class="weekday">Dim</span>
                </div>
                <div class="calendar-days">
                    <?php
                    $days = ['', '', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'];
                    $currentDate = new DateTime('2025-05-01');
                    foreach ($days as $day) {
                        $class = $day == date('d') && $currentDate->format('Y-m') == '2025-05' ? 'day-today' : '';
                        echo "<div class='calendar-day $class'>";
                        if ($day) {
                            echo "<span class='day-number'>$day</span>";
                            foreach ($rendezvous as $rdv) {
                                $rdvDay = date('d', strtotime($rdv['date_heure']));
                                if ($rdvDay == $day) {
                                    echo "<div class='day-event'>";
                                    echo "<span class='day-event-time'>" . date('H:i', strtotime($rdv['date_heure'])) . "</span><br>";
                                    echo "<span class='day-event-title'>" . htmlspecialchars($rdv['medecin_prenom'] . ' - ' . $rdv['motif']) . "</span>";
                                    echo "</div>";
                                }
                            }
                        }
                        echo "</div>";
                        $currentDate->modify('+1 day');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="modalBackdrop">
        <div class="modal" id="newAppointmentModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Nouveau rendez-vous</h2>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="newAppointmentForm" method="post" action="save_appointment.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="appointmentType">Type de rendez-vous *</label>
                                <select id="appointmentType" name="appointmentType" class="form-control" required>
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
                                <label for="specialty">Spécialité *</label>
                                <select id="specialty" name="specialty" class="form-control" required>
                                    <option value="">Sélectionnez une spécialité</option>
                                    <?php foreach ($specialites as $specialite_option): ?>
                                        <option value="<?= htmlspecialchars($specialite_option) ?>">
                                            <?= htmlspecialchars($specialite_option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="doctor">Médecin *</label>
                                <select id="doctor" name="doctor" class="form-control" required>
                                    <option value="">Sélectionnez un médecin</option>
                                    <?php foreach ($medecins as $medecin_option): ?>
                                        <option value="Dr. <?= htmlspecialchars($medecin_option) ?>">
                                            Dr. <?= htmlspecialchars($medecin_option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row form-row-split">
                            <div class="form-group">
                                <label for="date">Date *</label>
                                <input type="date" id="date" name="date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="time">Heure *</label>
                                <select id="time" name="time" class="form-control" required>
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
                                <label for="reason">Motif du rendez-vous</label>
                                <textarea id="reason" name="reason" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="notes">Informations supplémentaires</label>
                                <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAppointment">Annuler</button>
                    <button type="submit" form="newAppointmentForm" class="btn btn-primary">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <span>© 2025 MediStatView. Tous droits réservés.</span>
            <div class="footer-links">
                <a href="#" class="footer-link">Confidentialité</a>
                <a href="#" class="footer-link">Conditions d'utilisation</a>
                <a href="#" class="footer-link">Contact</a>
                <a href="#" class="footer-link">Aide</a>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        // Gestion de l'affichage des vues (Liste/Calendrier)
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('listView').style.display = btn.getAttribute('data-view') === 'list' ? 'block' : 'none';
                document.getElementById('calendarView').style.display = btn.getAttribute('data-view') === 'calendar' ? 'block' : 'none';
            });
        });

        // Gestion du modal
        const modalBackdrop = document.getElementById('modalBackdrop');
        const modal = document.getElementById('newAppointmentModal');
        const closeBtn = document.querySelector('.modal-close');
        const cancelBtn = document.getElementById('cancelAppointment');

        document.querySelectorAll('.btn-primary[href="#newAppointmentModal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                modalBackdrop.classList.add('active');
            });
        });

        closeBtn.addEventListener('click', () => modalBackdrop.classList.remove('active'));
        cancelBtn.addEventListener('click', () => modalBackdrop.classList.remove('active'));
        modalBackdrop.addEventListener('click', (e) => {
            if (e.target === modalBackdrop) modalBackdrop.classList.remove('active');
        });

        // Soumission du formulaire
        document.getElementById('newAppointmentForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('save_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Rendez-vous créé avec succès !');
                    modalBackdrop.classList.remove('active');
                    location.reload();
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la création du rendez-vous.');
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userBtn = document.querySelector('.user-btn');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        userBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le comportement par défaut du bouton
            dropdownMenu.classList.toggle('active'); // Bascule la classe active
        });

        // Ferme le menu déroulant si l'utilisateur clique en dehors
        document.addEventListener('click', function(event) {
            if (!userBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove('active');
            }
        });
    });
    </script>
</body>
</html>