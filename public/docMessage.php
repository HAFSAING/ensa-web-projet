<?php
// Initialisation de la session
session_start();

// Configuration de la connexion à la base de données
$servername = "localhost";
$username = "root"; // À remplacer par votre nom d'utilisateur MySQL
$password = ""; // À remplacer par votre mot de passe MySQL
$dbname = "medistatview";

try {
    // Création de la connexion à la base de données avec PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configuration des attributs PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Récupération des informations du médecin
    $medecin_id = $_SESSION['medecin_id'];
    $stmt = $conn->prepare("SELECT id, civilite, nom, prenom, specialite_id FROM medecins WHERE id = :medecin_id");
    $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
    $stmt->execute();
    $medecin = $stmt->fetch();
    
    // Récupération des notifications non lues
    $stmt = $conn->prepare("SELECT COUNT(*) as nb_notifications FROM notifications 
                           WHERE utilisateur_id = :medecin_id AND type_utilisateur = 'medecin' AND lue = 0");
    $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetch();
    $nb_notifications = $notifications['nb_notifications'];
    
    // Récupération de la liste des conversations
    $stmt = $conn->prepare("
        SELECT DISTINCT 
            p.id as patient_id, 
            p.nom as patient_nom, 
            p.prenom as patient_prenom,
            (SELECT message FROM notifications 
             WHERE (utilisateur_id = :medecin_id AND type_utilisateur = 'medecin') 
                OR (utilisateur_id = p.id AND type_utilisateur = 'patient')
             ORDER BY created_at DESC LIMIT 1) as dernier_message,
            (SELECT created_at FROM notifications 
             WHERE (utilisateur_id = :medecin_id AND type_utilisateur = 'medecin') 
                OR (utilisateur_id = p.id AND type_utilisateur = 'patient')
             ORDER BY created_at DESC LIMIT 1) as date_dernier_message,
            (SELECT COUNT(*) FROM notifications 
             WHERE utilisateur_id = p.id AND type_utilisateur = 'patient' 
                AND lue = 0) as nb_non_lus
        FROM patients p
        INNER JOIN notifications n ON 
            (n.utilisateur_id = p.id AND n.type_utilisateur = 'patient') OR
            (n.utilisateur_id = :medecin_id AND n.type_utilisateur = 'medecin')
        GROUP BY p.id
        ORDER BY date_dernier_message DESC
    ");
    $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
    $stmt->execute();
    $conversations = $stmt->fetchAll();
    
    // Sélection d'une conversation spécifique si un ID est fourni
    $conversation_active = null;
    $messages = [];
    
    if (isset($_GET['patient_id']) && is_numeric($_GET['patient_id'])) {
        $patient_id = $_GET['patient_id'];
        
        // Récupération des informations du patient
        $stmt = $conn->prepare("SELECT id, nom, prenom FROM patients WHERE id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        $conversation_active = $stmt->fetch();
        
        if ($conversation_active) {
            // Récupération des messages entre le médecin et le patient
            $stmt = $conn->prepare("
                SELECT 
                    n.id,
                    n.utilisateur_id,
                    n.type_utilisateur,
                    n.message,
                    n.created_at,
                    CASE 
                        WHEN n.type_utilisateur = 'medecin' THEN m.nom
                        WHEN n.type_utilisateur = 'patient' THEN p.nom
                    END as nom,
                    CASE 
                        WHEN n.type_utilisateur = 'medecin' THEN m.prenom
                        WHEN n.type_utilisateur = 'patient' THEN p.prenom
                    END as prenom
                FROM notifications n
                LEFT JOIN medecins m ON m.id = n.utilisateur_id AND n.type_utilisateur = 'medecin'
                LEFT JOIN patients p ON p.id = n.utilisateur_id AND n.type_utilisateur = 'patient'
                WHERE 
                    (n.utilisateur_id = :medecin_id AND n.type_utilisateur = 'medecin' AND n.lien = :patient_id) OR
                    (n.utilisateur_id = :patient_id AND n.type_utilisateur = 'patient' AND n.lien = :medecin_id)
                ORDER BY n.created_at ASC
            ");
            $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
            $stmt->execute();
            $messages = $stmt->fetchAll();
            
            // Marquer les messages du patient comme lus
            $stmt = $conn->prepare("
                UPDATE notifications 
                SET lue = 1 
                WHERE utilisateur_id = :patient_id AND type_utilisateur = 'patient' AND lien = :medecin_id AND lue = 0
            ");
            $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    
    // Traitement de l'envoi d'un nouveau message
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_message']) && isset($_POST['patient_id']) && isset($_POST['message'])) {
        $patient_id = $_POST['patient_id'];
        $message_text = trim($_POST['message']);
        
        if (!empty($message_text)) {
            // Vérification que le patient existe
            $stmt = $conn->prepare("SELECT id FROM patients WHERE id = :patient_id");
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Insertion du message dans la table des notifications
                $stmt = $conn->prepare("
                    INSERT INTO notifications (utilisateur_id, type_utilisateur, titre, message, lien, lue)
                    VALUES (:medecin_id, 'medecin', 'Nouveau message', :message, :patient_id, 0)
                ");
                $stmt->bindParam(':medecin_id', $medecin_id, PDO::PARAM_INT);
                $stmt->bindParam(':message', $message_text, PDO::PARAM_STR);
                $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
                $stmt->execute();
                
                // Redirection pour éviter le problème de rechargement de formulaire
                header("Location: docMessage.php?patient_id=" . $patient_id);
                exit();
            }
        }
    }
    
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>

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

        /* Messagerie */
        .messaging-container {
            display: flex;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            height: calc(100vh - 180px);
        }

        .conversations-list {
            width: 300px;
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
        }

        .conversations-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-bg);
        }

        .conversations-search {
            position: relative;
        }

        .conversations-search input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 30px;
            background-color: white;
            font-size: 0.9rem;
        }

        .conversations-search i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .conversation-item:hover {
            background-color: rgba(134, 179, 195, 0.1);
        }

        .conversation-item.active {
            background-color: rgba(134, 179, 195, 0.2);
            border-left: 3px solid var(--primary-color);
        }

        .conversation-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 1rem;
            color: white;
        }

        .conversation-info {
            flex-grow: 1;
            overflow: hidden;
        }

        .conversation-name {
            font-weight: 600;
            margin-bottom: 0.2rem;
            display: flex;
            justify-content: space-between;
        }

        .conversation-preview {
            font-size: 0.85rem;
            color: #777;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 0.75rem;
            color: #aaa;
        }

        .conversation-badge {
            background-color: var(--accent-color3);
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            background-color: var(--light-bg);
        }

        .chat-title {
            font-weight: 600;
            flex-grow: 1;
        }

        .chat-actions a {
            color: var(--primary-color);
            margin-left: 1rem;
            font-size: 1.2rem;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 70%;
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            position: relative;
        }

        .message-time {
            font-size: 0.75rem;
            color: #aaa;
            margin-top: 0.3rem;
        }

        .message-received {
            align-self: flex-start;
            background-color: var(--light-bg);
        }

        .message-sent {
            align-self: flex-end;
            background-color: rgba(33, 107, 78, 0.2);
        }

        .chat-input {
            border-top: 1px solid var(--border-color);
            padding: 1rem;
            display: flex;
            align-items: center;
        }

        .chat-input form {
            display: flex;
            width: 100%;
        }

        .chat-input input {
            flex-grow: 1;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 30px;
            margin-right: 0.8rem;
            font-size: 0.95rem;
        }

        .chat-input button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chat-input button:hover {
            background-color: var(--primary-color);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #777;
            text-align: center;
            padding: 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--accent-color2);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .empty-state p {
            max-width: 500px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .conversations-list {
                width: 250px;
            }
        }

        @media (max-width: 768px) {
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

            .conversations-list {
                display: none;
            }

            .messaging-container.show-conversations .conversations-list {
                display: block;
                width: 100%;
            }

            .messaging-container.show-conversations .chat-container {
                display: none;
            }

            .chat-header {
                padding: 0.8rem;
            }

            .back-button {
                display: block !important;
                margin-right: 0.8rem;
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
                        <li class="nav-item">
                            <a href="docMessage.php" class="nav-link active">
                                <i class="fas fa-envelope"></i>
                                <span>Messagerie</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="profile-dropdown">
                    <button class="profile-button">
                        <div class="profile-avatar">
                            <?php echo substr($medecin['prenom'], 0, 1) . substr($medecin['nom'], 0, 1); ?>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name"><?php echo $medecin['civilite'] . ' ' . $medecin['prenom'] . ' ' . $medecin['nom']; ?></div>
                            <?php
                            // Récupération du nom de la spécialité du médecin
                            $stmt = $conn->prepare("SELECT nom FROM specialites WHERE id = :specialite_id");
                            $stmt->bindParam(':specialite_id', $medecin['specialite_id'], PDO::PARAM_INT);
                            $stmt->execute();
                            $specialite = $stmt->fetch();
                            ?>
                            <div class="profile-title"><?php echo $specialite['nom']; ?></div>
                        </div>
                        <i class="fas fa-chevron-down" style="margin-left: 10px; font-size: 0.8rem;"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="docProfile.php"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="docParametres.php"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="docNotifications.php"><i class="fas fa-bell"></i> Notifications 
                            <?php if($nb_notifications > 0): ?>
                                <span class="conversation-badge"><?php echo $nb_notifications; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="aide.php"><i class="fas fa-question-circle"></i> Aide & Support</a>
                        <a href="docLogOut.php" style="color: #d32f2f;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
                    <a href="docPatients.php">
                        <i class="fas fa-user-injured"></i>
                        <span>Liste des patients</span>
                    </a>
                </li>
                <li>
                    <a href="docRendezVous.php">
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
                    <a href="docNotes.php">
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
                    <a href="analyses.php">
                        <i class="fas fa-flask"></i>
                        <span>Résultats d'analyses</span>
                    </a>
                </li>
                <li>
                    <a href="docStatistique.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistiques</span>
                    </a>
                </li>
                <li>
                    <a href="docMessage.php" class="active">
                        <i class="fas fa-envelope"></i>
                        <span>Messagerie</span>
                    </a>
                </li>
                <li>
                    <a href="docParametres.php">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <div class="messaging-container<?php if (!$conversation_active) echo ' show-conversations'; ?>">
                <!-- Liste des conversations -->
                <div class="conversations-list">
                    <div class="conversations-header">
                        <h2>Messages</h2>
                        <div class="conversations-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Rechercher un patient..." id="search-conversation">
                        </div>
                    </div>
                    
                    <?php if (count($conversations) > 0): ?>
                        <?php foreach ($conversations as $conv): ?>
                            <a href="docMessage.php?patient_id=<?php echo $conv['patient_id']; ?>" class="conversation-item<?php if ($conversation_active && $conversation_active['id'] == $conv['patient_id']) echo ' active'; ?>">
                                <div class="conversation-avatar">
                                    <?php echo substr($conv['patient_prenom'], 0, 1) . substr($conv['patient_nom'], 0, 1); ?>
                                </div>
                                <div class="conversation-info">
                                    <div class="conversation-name">
                                        <?php echo $conv['patient_prenom'] . ' ' . $conv['patient_nom']; ?>
                                        <?php if ($conv['nb_non_lus'] > 0): ?>
                                            <span class="conversation-badge"><?php echo $conv['nb_non_lus']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="conversation-preview">
                                        <?php echo !empty($conv['dernier_message']) ? htmlspecialchars(substr($conv['dernier_message'], 0, 50)) . (strlen($conv['dernier_message']) > 50 ? '...' : '') : 'Aucun message'; ?>
                                    </div>
                                    <div class="conversation-time">
                                        <?php 
                                        if (!empty($conv['date_dernier_message'])) {
                                            $date = new DateTime($conv['date_dernier_message']);
                                            $now = new DateTime();
                                            $diff = $date->diff($now);
                                            
                                            if ($diff->days == 0) {
                                                echo $date->format('H:i');
                                            } elseif ($diff->days == 1) {
                                                echo 'Hier';
                                            } elseif ($diff->days < 7) {
                                                echo $date->format('l');
                                            } else {
                                                echo $date->format('d/m/Y');
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h3>Aucune conversation</h3>
                            <p>Vous n'avez pas encore de conversations avec vos patients.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Zone de conversation -->
                <div class="chat-container">
                    <?php if ($conversation_active): ?>
                        <div class="chat-header">
                            <a href="docMessage.php" class="back-button" style="display: none; color: var(--primary-color); margin-right: 10px;">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div class="conversation-avatar">
                                <?php echo substr($conversation_active['prenom'], 0, 1) . substr($conversation_active['nom'], 0, 1); ?>
                            </div>
                            <div class="chat-title">
                                <?php echo $conversation_active['prenom'] . ' ' . $conversation_active['nom']; ?>
                            </div>
                            <div class="chat-actions">
                                <a href="patient-details.php?id=<?php echo $conversation_active['id']; ?>" title="Voir le dossier du patient">
                                    <i class="fas fa-folder-open"></i>
                                </a>
                                <a href="consultation.php?patient_id=<?php echo $conversation_active['id']; ?>" title="Nouvelle consultation">
                                    <i class="fas fa-stethoscope"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="chat-messages">
                            <?php if (count($messages) > 0): ?>
                                <?php foreach ($messages as $msg): ?>
                                    <div class="message <?php echo $msg['type_utilisateur'] === 'medecin' ? 'message-sent' : 'message-received'; ?>">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                        <div class="message-time">
                                            <?php 
                                            $date = new DateTime($msg['created_at']);
                                            echo $date->format('H:i · d/m/Y'); 
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-comment-dots"></i>
                                    <h3>Démarrez la conversation</h3>
                                    <p>Envoyez un message à votre patient pour commencer la conversation.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="chat-input">
                            <form method="POST" id="messageForm">
                                <input type="hidden" name="patient_id" value="<?php echo $conversation_active['id']; ?>">
                                <input type="text" name="message" placeholder="Tapez votre message..." required autofocus>
                                <button type="submit" name="envoyer_message">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h3>Sélectionnez une conversation</h3>
                            <p>Choisissez un patient dans la liste pour afficher vos messages.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script pour la recherche des conversations
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-conversation');
            const conversationItems = document.querySelectorAll('.conversation-item');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    conversationItems.forEach(item => {
                        const name = item.querySelector('.conversation-name').textContent.toLowerCase();
                        if (name.includes(searchTerm)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
            
            // Scroll automatique vers le bas de la conversation
            const chatMessages = document.querySelector('.chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Gestion de l'affichage responsive
            const backButton = document.querySelector('.back-button');
            if (backButton) {
                backButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector('.messaging-container').classList.add('show-conversations');
                });
            }
            
            // Gestion du formulaire de message
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', function() {
                    const messageInput = this.querySelector('input[name="message"]');
                    if (messageInput.value.trim() === '') {
                        event.preventDefault();
                    }
                });
            }
        });

        // Vérifier s'il y a de nouveaux messages toutes les 30 secondes
        setInterval(function() {
            <?php if ($conversation_active): ?>
                fetch('check-messages.php?patient_id=<?php echo $conversation_active['id']; ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.hasNewMessages) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            <?php endif; ?>
        }, 30000);
    </script>
</body>
</html>