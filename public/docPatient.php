<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();

try {

    $medecin_id = $_SESSION['medecin_id'];
    $stmt_medecin = $pdo->prepare("
        SELECT m.civilite, m.nom, m.prenom, s.nom AS specialite 
        FROM medecins m 
        LEFT JOIN specialites s ON m.specialite_id = s.id 
        WHERE m.id = ?
    ");
    $stmt_medecin->execute([$medecin_id]);
    $medecin = $stmt_medecin->fetch(PDO::FETCH_ASSOC);

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
    die("Erreur lors de la récupération des données: " . $e->getMessage());
}


$patientsParPage = 10;
$pageCourante = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$debut = ($pageCourante - 1) * $patientsParPage;

$recherche = isset($_GET['recherche']) ? $_GET['recherche'] : '';
$filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'tous';

$sql = "SELECT p.*, v.nom as ville_nom 
        FROM patients p 
        LEFT JOIN villes v ON p.ville_id = v.id 
        WHERE 1=1";
$params = [];

if (!empty($recherche)) {
    $sql .= " AND (p.nom LIKE :recherche OR p.prenom LIKE :recherche OR p.cin LIKE :recherche OR p.email LIKE :recherche)";
    $params[':recherche'] = "%$recherche%";
}

if ($filtre === 'actifs') {
    $sql .= " AND p.statut = 'actif'";
} elseif ($filtre === 'en_attente') {
    $sql .= " AND p.statut = 'en_attente'";
} elseif ($filtre === 'suspendus') {
    $sql .= " AND p.statut = 'suspendu'";
}

$sqlCount = str_replace("SELECT p.*, v.nom as ville_nom", "SELECT COUNT(*)", $sql);
$stmtCount = $pdo->prepare($sqlCount);
foreach ($params as $key => $val) {
    $stmtCount->bindValue($key, $val);
}
$stmtCount->execute();
$totalPatients = $stmtCount->fetchColumn();
$totalPages = ceil($totalPatients / $patientsParPage);

$sql .= " ORDER BY p.nom ASC LIMIT :debut, :nb_par_page";
$params[':debut'] = $debut;
$params[':nb_par_page'] = $patientsParPage;

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    // Gestion spéciale pour les entiers dans la limite LIMIT
    if ($key == ':debut' || $key == ':nb_par_page') {
        $stmt->bindValue($key, $val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val);
    }
}
$stmt->execute();
$patients = $stmt->fetchAll();

function getConsultationsPatient($pdo, $patientId) {
    $sql = "SELECT COUNT(*) FROM consultations WHERE patient_id = :patient_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getDerniereConsultation($pdo, $patientId) {
    $sql = "SELECT date_consultation FROM consultations 
            WHERE patient_id = :patient_id AND statut = 'terminee'
            ORDER BY date_consultation DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['date_consultation'] : null;
}

function calculerAge($dateNaissance) {
    $aujourdHui = new DateTime();
    $dateNaissanceObj = new DateTime($dateNaissance);
    $difference = $aujourdHui->diff($dateNaissanceObj);
    return $difference->y;
}

function getHealthIndicator($patientId) {
    $options = ['health-excellent', 'health-good', 'health-fair', 'health-poor'];
    $rand = $patientId % 4;
    return $options[$rand];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Patients - MediStatView</title>
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

        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.7rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--text-light);
        }

        .btn-primary:hover {
            background-color: #174658;
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--text-light);
        }

        .btn-secondary:hover {
            background-color: #185539;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
        }

        .btn-outline:hover {
            background-color: var(--light-bg);
        }

        .btn i {
            font-size: 1.1rem;
        }

        /* Filtres et recherche */
        .filters-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.2rem;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-label {
            font-weight: 500;
            color: var(--primary-color);
        }

        .filter-select {
            padding: 0.6rem 1rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: var(--light-bg);
            font-size: 0.9rem;
            color: var(--text-dark);
            transition: all 0.2s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--accent-color2);
            box-shadow: 0 0 0 2px rgba(134, 179, 195, 0.3);
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--light-bg);
            border-radius: 6px;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            max-width: 300px;
            width: 100%;
        }

        .search-box input {
            border: none;
            background: none;
            flex-grow: 1;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .search-box input:focus {
            outline: none;
        }

        .search-box i {
            color: #777;
        }

        /* Tableau des patients */
        .patients-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .patients-table th,
        .patients-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .patients-table th {
            background-color: rgba(29, 86, 107, 0.05);
            color: var(--primary-color);
            font-weight: 600;
            position: relative;
        }

        .patients-table th:after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background-color: var(--border-color);
        }

        .patients-table th:last-child:after {
            display: none;
        }

        .patients-table tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .patients-table tr:last-child td {
            border-bottom: none;
        }

        .patient-name-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
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
        }

        .patient-info {
            display: flex;
            flex-direction: column;
        }

        .patient-name {
            font-weight: 500;
            color: var(--primary-color);
        }

        .patient-email {
            font-size: 0.8rem;
            color: #777;
        }

        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-pending {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .badge-suspended {
            background-color: #ffebee;
            color: #c62828;
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

        .patient-actions {
            display: flex;
            gap: 0.8rem;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            background-color: var(--light-bg);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-icon:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-item {
            list-style: none;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background-color: white;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .page-item.disabled .page-link {
            color: #ccc;
            pointer-events: none;
        }

        @media (max-width: 992px) {
            .filters-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-group {
                width: 100%;
                justify-content: space-between;
            }

            .search-box {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .patients-table th:nth-child(3),
            .patients-table td:nth-child(3),
            .patients-table th:nth-child(4),
            .patients-table td:nth-child(4) {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .patients-table th:nth-child(5),
            .patients-table td:nth-child(5) {
                display: none;
            }
        }

        /* Styles pour modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 600px;
            position: relative;
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
            transition: all 0.2s ease;
        }

        .close-modal:hover {
            color: var(--accent-color3);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color2);
            box-shadow: 0 0 0 2px rgba(134, 179, 195, 0.3);
        }

        .form-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .form-row .form-group {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 250px;
        }

        .modal-footer {
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .alert-info {
            background-color: #e1f5fe;
            color: #0277bd;
            border-left: 4px solid #0277bd;
        }

        .alert-warning {
            background-color: #fff3e0;
            color: #ef6c00;
            border-left: 4px solid #ef6c00;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: #777;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .empty-text {
            margin-bottom: 1.5rem;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</head>
<body>
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
                            <a href="docPatient.php" class="nav-link active">
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

    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">Gestion des Patients</h1>
            <div class="action-buttons">
                <button class="btn btn-outline" onclick="exportPatients()">
                    <i class="fas fa-download"></i> Exporter
                </button>
                <button class="btn btn-primary" onclick="showAddPatientModal()">
                    <i class="fas fa-plus"></i> Ajouter un patient
                </button>
            </div>
        </div>


        
        <div class="filters-container">
            <div class="filter-group">
                <span class="filter-label">Filtrer par:</span>
                <form action="" method="GET" id="filter-form">
                    <select name="filtre" class="filter-select" onchange="this.form.submit()">
                        <option value="tous" <?php echo $filtre === 'tous' ? 'selected' : ''; ?>>Tous les patients</option>
                        <option value="actifs" <?php echo $filtre === 'actifs' ? 'selected' : ''; ?>>Patients actifs</option>
                        <option value="en_attente" <?php echo $filtre === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="suspendus" <?php echo $filtre === 'suspendus' ? 'selected' : ''; ?>>Suspendus</option>
                    </select>
                    <?php if (!empty($recherche)): ?>
                        <input type="hidden" name="recherche" value="<?php echo htmlspecialchars($recherche); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['page'])): ?>
                        <input type="hidden" name="page" value="<?php echo (int)$_GET['page']; ?>">
                    <?php endif; ?>
                </form>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="" method="GET">
                    <input type="text" name="recherche" placeholder="Rechercher un patient..." value="<?php echo htmlspecialchars($recherche); ?>">
                    <?php if ($filtre !== 'tous'): ?>
                        <input type="hidden" name="filtre" value="<?php echo htmlspecialchars($filtre); ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>

    <!-- Tableau des patients -->
    <?php if (count($patients) > 0): ?>
    <div class="table-responsive">
        <table class="patients-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Âge</th>
                    <th>Ville</th>
                    <th>Consultations</th>
                    <th>Dernière visite</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <?php 
                    $age = calculerAge($patient['date_naissance']);
                    $nbConsultations = getConsultationsPatient($pdo, $patient['id']);
                    $derniereConsultation = getDerniereConsultation($pdo, $patient['id']);
                    $healthIndicator = getHealthIndicator($patient['id']);
                    
                    // Déterminer la classe de badge en fonction du statut
                    $badgeClass = '';
                    switch ($patient['statut']) {
                        case 'actif':
                            $badgeClass = 'badge-active';
                            break;
                        case 'en_attente':
                            $badgeClass = 'badge-pending';
                            break;
                        case 'suspendu':
                            $badgeClass = 'badge-suspended';
                            break;
                    }
                    
                    // Obtenir les initiales pour l'avatar
                    $initiales = strtoupper(substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1));
                ?>
                <tr>
                    <td>
                        <div class="patient-name-cell">
                            <div class="patient-avatar"><?php echo $initiales; ?></div>
                            <div class="patient-info">
                                <div class="patient-name"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></div>
                                <div class="patient-email"><?php echo htmlspecialchars($patient['email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo $age; ?> ans</td>
                    <td><?php echo htmlspecialchars($patient['ville_nom'] ?? 'Non spécifiée'); ?></td>
                    <td><?php echo $nbConsultations; ?></td>
                    <td>
                        <?php if ($derniereConsultation): ?>
                            <?php 
                                $dateObj = new DateTime($derniereConsultation);
                                echo $dateObj->format('d/m/Y');
                            ?>
                        <?php else: ?>
                            Jamais
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php 
                                switch ($patient['statut']) {
                                    case 'actif':
                                        echo 'Actif';
                                        break;
                                    case 'en_attente':
                                        echo 'En attente';
                                        break;
                                    case 'suspendu':
                                        echo 'Suspendu';
                                        break;
                                }
                            ?>
                        </span>
                    </td>
                    <td>
                        <div class="patient-actions">
                            <a href="docPatientDetail.php?id=<?php echo $patient['id']; ?>" class="action-icon" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="docConsultation.php?patient_id=<?php echo $patient['id']; ?>" class="action-icon" title="Nouvelle consultation">
                                <i class="fas fa-stethoscope"></i>
                            </a>
                            <a href="#" class="action-icon" onclick="showEditPatientModal(<?php echo $patient['id']; ?>)" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <ul class="pagination">
        <?php if ($pageCourante > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $pageCourante - 1; ?><?php echo !empty($recherche) ? '&recherche=' . urlencode($recherche) : ''; ?><?php echo $filtre !== 'tous' ? '&filtre=' . urlencode($filtre) : ''; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="fas fa-chevron-left"></i></span>
            </li>
        <?php endif; ?>

        <?php
        // Afficher les liens de pagination
        $startPage = max(1, $pageCourante - 2);
        $endPage = min($totalPages, $pageCourante + 2);

        if ($startPage > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=1' . (!empty($recherche) ? '&recherche=' . urlencode($recherche) : '') . ($filtre !== 'tous' ? '&filtre=' . urlencode($filtre) : '') . '">1</a></li>';
            if ($startPage > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $pageCourante) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . (!empty($recherche) ? '&recherche=' . urlencode($recherche) : '') . ($filtre !== 'tous' ? '&filtre=' . urlencode($filtre) : '') . '">' . $i . '</a></li>';
            }
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . (!empty($recherche) ? '&recherche=' . urlencode($recherche) : '') . ($filtre !== 'tous' ? '&filtre=' . urlencode($filtre) : '') . '">' . $totalPages . '</a></li>';
        }
        ?>

        <?php if ($pageCourante < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $pageCourante + 1; ?><?php echo !empty($recherche) ? '&recherche=' . urlencode($recherche) : ''; ?><?php echo $filtre !== 'tous' ? '&filtre=' . urlencode($filtre) : ''; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="fas fa-chevron-right"></i></span>
            </li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>

    <?php else: ?>
    <!-- État vide - aucun patient trouvé -->
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-user-injured"></i>
        </div>
        <h3 class="empty-title">Aucun patient trouvé</h3>
        <p class="empty-text">
            <?php if (!empty($recherche)): ?>
                Aucun résultat ne correspond à votre recherche "<?php echo htmlspecialchars($recherche); ?>".
            <?php elseif ($filtre !== 'tous'): ?>
                Aucun patient <?php echo $filtre === 'actifs' ? 'actif' : ($filtre === 'en_attente' ? 'en attente' : 'suspendu'); ?> trouvé.
            <?php else: ?>
                Vous n'avez pas encore de patients enregistrés.
            <?php endif; ?>
        </p>
        <button class="btn btn-primary" onclick="showAddPatientModal()">
            <i class="fas fa-plus"></i> Ajouter un patient
        </button>
    </div>
    <?php endif; ?>

    <div id="addPatientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter un nouveau patient</h2>
                <button class="close-modal" onclick="closeModal('addPatientModal')">&times;</button>
            </div>
            <form id="addPatientForm" action="actions/addPatient.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cin" class="form-label">CIN *</label>
                        <input type="text" id="cin" name="cin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="date_naissance" class="form-label">Date de naissance *</label>
                        <input type="date" id="date_naissance" name="date_naissance" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="sexe" class="form-label">Sexe *</label>
                        <select id="sexe" name="sexe" class="form-control" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone *</label>
                        <input type="tel" id="telephone" name="telephone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mutuelle" class="form-label">Mutuelle</label>
                        <select id="mutuelle" name="mutuelle" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="cnops">CNOPS</option>
                            <option value="cnss">CNSS</option>
                            <option value="ramed">RAMED</option>
                            <option value="amo">AMO</option>
                            <option value="autre">Autre</option>
                            <option value="aucune">Aucune</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" id="adresse" name="adresse" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ville_id" class="form-label">Ville</label>
                        <select id="ville_id" name="ville_id" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <?php
                            // Récupérer la liste des villes
                            $sqlVilles = "SELECT id, nom FROM villes ORDER BY nom ASC";
                            $stmtVilles = $pdo->query($sqlVilles);
                            $villes = $stmtVilles->fetchAll();
                            
                            foreach ($villes as $ville) {
                                echo '<option value="' . $ville['id'] . '">' . htmlspecialchars($ville['nom']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="statut" class="form-label">Statut *</label>
                    <select id="statut" name="statut" class="form-control" required>
                        <option value="actif">Actif</option>
                        <option value="en_attente">En attente</option>
                        <option value="suspendu">Suspendu</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addPatientModal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modifier un patient -->
    <div id="editPatientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Modifier le patient</h2>
                <button class="close-modal" onclick="closeModal('editPatientModal')">&times;</button>
            </div>
            <form id="editPatientForm" action="actions/updatePatient.php" method="POST">
                <input type="hidden" id="edit_patient_id" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_nom" class="form-label">Nom *</label>
                        <input type="text" id="edit_nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_prenom" class="form-label">Prénom *</label>
                        <input type="text" id="edit_prenom" name="prenom" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_cin" class="form-label">CIN *</label>
                        <input type="text" id="edit_cin" name="cin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_date_naissance" class="form-label">Date de naissance *</label>
                        <input type="date" id="edit_date_naissance" name="date_naissance" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_sexe" class="form-label">Sexe *</label>
                        <select id="edit_sexe" name="sexe" class="form-control" required>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_email" class="form-label">Email *</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_telephone" class="form-label">Téléphone *</label>
                        <input type="tel" id="edit_telephone" name="telephone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_mutuelle" class="form-label">Mutuelle</label>
                        <select id="edit_mutuelle" name="mutuelle" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="cnops">CNOPS</option>
                            <option value="cnss">CNSS</option>
                            <option value="ramed">RAMED</option>
                            <option value="amo">AMO</option>
                            <option value="autre">Autre</option>
                            <option value="aucune">Aucune</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_adresse" class="form-label">Adresse</label>
                        <input type="text" id="edit_adresse" name="adresse" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit_ville_id" class="form-label">Ville</label>
                        <select id="edit_ville_id" name="ville_id" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <?php
                            foreach ($villes as $ville) {
                                echo '<option value="' . $ville['id'] . '">' . htmlspecialchars($ville['nom']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_statut" class="form-label">Statut *</label>
                    <select id="edit_statut" name="statut" class="form-control" required>
                        <option value="actif">Actif</option>
                        <option value="en_attente">En attente</option>
                        <option value="suspendu">Suspendu</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('editPatientModal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

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


    <script>
        // Fonctions pour les modales
        function showAddPatientModal() {
            document.getElementById('addPatientModal').style.display = 'block';
        }

        function showEditPatientModal(patientId) {
            // Ici, vous récupérez les données du patient via AJAX
            fetch('actions/getPatient.php?id=' + patientId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const patient = data.patient;
                        document.getElementById('edit_patient_id').value = patient.id;
                        document.getElementById('edit_nom').value = patient.nom;
                        document.getElementById('edit_prenom').value = patient.prenom;
                        document.getElementById('edit_cin').value = patient.cin;
                        document.getElementById('edit_date_naissance').value = patient.date_naissance;
                        document.getElementById('edit_sexe').value = patient.sexe;
                        document.getElementById('edit_email').value = patient.email;
                        document.getElementById('edit_telephone').value = patient.telephone;
                        document.getElementById('edit_mutuelle').value = patient.mutuelle || '';
                        document.getElementById('edit_adresse').value = patient.adresse || '';
                        document.getElementById('edit_ville_id').value = patient.ville_id || '';
                        document.getElementById('edit_statut').value = patient.statut;
                        
                        document.getElementById('editPatientModal').style.display = 'block';
                    } else {
                        alert('Erreur lors de la récupération des données du patient');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la récupération des données');
                });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Fermer la modale si l'utilisateur clique en dehors de celle-ci
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }

        // Fonction pour exporter les patients (à implémenter)
        function exportPatients() {
            // Rediriger vers un script d'exportation
            window.location.href = 'actions/exportPatients.php?filtre=<?php echo urlencode($filtre); ?>&recherche=<?php echo urlencode($recherche); ?>';
        }

        // Pour la recherche instantanée (optionnel)
        document.querySelector('.search-box input').addEventListener('keyup', function(e) {
            // Si l'utilisateur appuie sur Entrée (code 13), soumettre le formulaire
            if (e.keyCode === 13) {
                this.form.submit();
            }
        });
    </script>

</div>
</body>
</html>