<?php
// Démarrage de la session
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
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header("Location: userConnecter.php");
    exit();
}

// Paramètres de pagination
$messages_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $messages_per_page;

// Filtres et recherche
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Construire la requête des notifications
$sql = "
    SELECT n.id, n.titre, n.message, n.lien, n.lue, n.created_at, 
           m.prenom AS medecin_prenom, m.nom AS medecin_nom
    FROM notifications n
    LEFT JOIN medecins m ON n.medecin_id = m.id
    WHERE n.utilisateur_id = :patient_id AND n.type_utilisateur = 'patient'
";
$params = [':patient_id' => $patient_id];

// Appliquer les filtres
if ($filter === 'unread') {
    $sql .= " AND n.lue = FALSE";
}
if ($search) {
    $sql .= " AND (n.titre LIKE :search OR n.message LIKE :search)";
    $params[':search'] = "%$search%";
}

// Appliquer le tri
if ($sort === 'oldest') {
    $sql .= " ORDER BY n.created_at ASC";
} else {
    $sql .= " ORDER BY n.created_at DESC";
}

// Pagination
$sql_count = "SELECT COUNT(*) FROM notifications n WHERE n.utilisateur_id = :patient_id AND n.type_utilisateur = 'patient'";
if ($filter === 'unread') {
    $sql_count .= " AND n.lue = FALSE";
}
if ($search) {
    $sql_count .= " AND (n.titre LIKE :search OR n.message LIKE :search)";
}
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_messages = $stmt_count->fetchColumn();
$total_pages = ceil($total_messages / $messages_per_page);

$sql .= " LIMIT :limit OFFSET :offset";
$stmt_notifications = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt_notifications->bindValue($key, $value);
}
$stmt_notifications->bindValue(':limit', $messages_per_page, PDO::PARAM_INT);
$stmt_notifications->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_notifications->execute();
$notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);

// Marquer un message comme lu
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notification_id = $_GET['mark_read'];
    $stmt = $pdo->prepare("UPDATE notifications SET lue = TRUE WHERE id = ? AND utilisateur_id = ? AND type_utilisateur = 'patient'");
    $stmt->execute([$notification_id, $patient_id]);
    header("Location: userMessage.php?page=$page&filter=$filter&sort=$sort" . ($search ? "&search=" . urlencode($search) : ""));
    exit();
}

// Marquer tous les messages comme lus
if (isset($_GET['mark_all_read'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET lue = TRUE WHERE utilisateur_id = ? AND type_utilisateur = 'patient' AND lue = FALSE");
    $stmt->execute([$patient_id]);
    header("Location: userMessage.php?page=$page&filter=$filter&sort=$sort" . ($search ? "&search=" . urlencode($search) : ""));
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - MediStatView</title>
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

        .user-avatar_answer {
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

        .messages-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin: 2rem 0;
        }

        .messages-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .messages-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .messages-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: var(--light-bg);
            border-radius: 6px;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
        }

        .search-bar input {
            border: none;
            background: none;
            outline: none;
            padding: 0.5rem;
            width: 200px;
        }

        .search-bar i {
            color: var(--primary-color);
        }

        .filter-select {
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: var(--light-bg);
            color: var(--text-dark);
        }

        .message-list {
            padding: 0 1.5rem;
        }

        .message-item {
            display: flex;
            padding: 1.2rem 0;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
        }

        .message-item:hover {
            background-color: var(--light-bg);
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

        .message-actions {
            display: flex;
            gap: 0.5rem;
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

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: var(--light-bg);
        }

        .pagination .active {
            background-color: var(--accent-color1);
            color: var(--primary-color);
            border-color: var(--accent-color1);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 12px;
            max-width: 600px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        .compose-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .compose-form label {
            font-weight: 500;
            color: var(--primary-color);
        }

        .compose-form input,
        .compose-form textarea {
            padding: 0.7rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            width: 100%;
        }

        .compose-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        .messages-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }

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
        }

        @media (max-width: 768px) {
            .nav-list {
                flex-wrap: wrap;
                gap: 0.2rem;
            }

            .nav-item {
                flex-basis: 33.333%;
            }

            .messages-controls {
                flex-direction: column;
                width: 100%;
            }

            .search-bar {
                width: 100%;
            }

            .search-bar input {
                width: 100%;
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

            .message-preview {
                -webkit-line-clamp: 1;
            }

            .pagination a {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
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
                        <li class="nav-item"><a href="userRendezVous.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Rendez-vous</a></li>
                        <li class="nav-item"><a href="userMessage.php" class="nav-link active"><i class="fas fa-envelope"></i> Messages</a></li>
                        <li class="nav-item"><a href="userStatistique.php" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <button class="user-btn">
                        <div class="user-avatar">
                            <?= substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1) ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?></span>
                            <span class="user-role">Patient</span>
                        </div>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Paramètres</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard">
        <div class="container">
            <div class="messages-section">
                <div class="messages-header">
                    <h2 class="messages-title">Mes Messages</h2>
                    <div class="messages-controls">
                        <form action="userMessage.php" method="get" class="search-bar">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Rechercher un message..." value="<?= htmlspecialchars($search) ?>">
                            <input type="hidden" name="page" value="1">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                        </form>
                        <select class="filter-select" onchange="location = this.value;">
                            <option value="?page=1&filter=all&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>" <?= $filter === 'all' ? 'selected' : '' ?>>Tous</option>
                            <option value="?page=1&filter=unread&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>" <?= $filter === 'unread' ? 'selected' : '' ?>>Non lus</option>
                        </select>
                        <select class="filter-select" onchange="location = this.value;">
                            <option value="?page=1&filter=<?= $filter ?>&sort=newest<?= $search ? '&search=' . urlencode($search) : '' ?>" <?= $sort === 'newest' ? 'selected' : '' ?>>Plus récents</option>
                            <option value="?page=1&filter=<?= $filter ?>&sort=oldest<?= $search ? '&search=' . urlencode($search) : '' ?>" <?= $sort === 'oldest' ? 'selected' : '' ?>>Plus anciens</option>
                        </select>
                        <a href="?mark_all_read=1&page=<?= $page ?>&filter=<?= $filter ?>&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-outline">Tout marquer comme lu</a>
                        <button class="btn btn-primary" onclick="openComposeModal()">Nouveau message</button>
                    </div>
                </div>
                <div class="message-list">
                    <?php if (empty($notifications)): ?>
                        <p>Aucun message à afficher.</p>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <div class="message-item" onclick="openMessageModal(<?= $n['id'] ?>, '<?= htmlspecialchars(addslashes($n['titre'])) ?>', '<?= htmlspecialchars(addslashes($n['medecin_prenom'] . ' ' . $n['medecin_nom'])) ?>', '<?= htmlspecialchars(addslashes($n['message'])) ?>', '<?= date('d M Y, H:i', strtotime($n['created_at'])) ?>', '<?= $n['lien'] ? htmlspecialchars($n['lien']) : '' ?>')">
                                <div class="message-sender">
                                    <div class="sender-avatar"><?= substr($n['medecin_prenom'] ?? $n['titre'], 0, 1) . substr($n['medecin_nom'] ?? $n['titre'], 0, 1) ?></div>
                                </div>
                                <div class="message-content">
                                    <div class="message-header">
                                        <div>
                                            <span class="sender-name"><?= htmlspecialchars($n['medecin_prenom'] && $n['medecin_nom'] ? $n['medecin_prenom'] . ' ' . $n['medecin_nom'] : $n['titre']) ?></span>
                                            <?php if (!$n['lue']): ?>
                                                <span class="message-badge">Nouveau</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="message-time"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?></span>
                                    </div>
                                    <div class="message-preview"><?= htmlspecialchars($n['message']) ?></div>
                                    <div class="message-actions">
                                        <?php if ($n['lien']): ?>
                                            <a href="<?= htmlspecialchars($n['lien']) ?>" class="btn btn-sm btn-outline">Voir détails</a>
                                        <?php endif; ?>
                                        <?php if (!$n['lue']): ?>
                                            <a href="?mark_read=<?= $n['id'] ?>&page=<?= $page ?>&filter=<?= $filter ?>&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-outline">Marquer comme lu</a>
                                        <?php endif; ?>
                                        <a href="#" class="btn btn-sm btn-outline" onclick="event.stopPropagation(); openComposeModal()">Répondre</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="messages-footer">
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&filter=<?= $filter ?>&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>">&laquo; Précédent</a>
                            <?php endif; ?>
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&filter=<?= $filter ?>&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?>&filter=<?= $filter ?>&sort=<?= $sort ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Suivant &raquo;</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher le message complet -->
    <div class="modal" id="messageModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeMessageModal()">&times;</span>
            <h2 id="modal-title"></h2>
            <p><strong>De:</strong> <span id="modal-sender"></span></p>
            <p><strong>Date:</strong> <span id="modal-date"></span></p>
            <p id="modal-content"></p>
            <div id="modal-actions"></div>
        </div>
    </div>

    <!-- Modal pour composer un message -->
    <div class="modal" id="composeModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeComposeModal()">&times;</span>
            <h2>Nouveau Message</h2>
            <form class="compose-form" action="#" method="post">
                <label for="recipient">Destinataire</label>
                <select id="recipient" name="recipient">
                    <!-- À remplir dynamiquement via une requête pour les médecins -->
                    <option value="">Sélectionner un médecin</option>
                    <?php
                    $stmt_medecins = $pdo->query("SELECT id, prenom, nom FROM medecins WHERE statut = 'actif'");
                    while ($medecin = $stmt_medecins->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$medecin['id']}'>" . htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) . "</option>";
                    }
                    ?>
                </select>
                <label for="subject">Sujet</label>
                <input type="text" id="subject" name="subject" required>
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <span class="copyright">© 2025 MediStatView Services. Tous droits réservés.</span>
            <div class="footer-links">
                <a href="#" class="footer-link">À propos</a>
                <a href="#" class="footer-link">Confidentialité</a>
                <a href="usertermes&privacy.php" class="footer-link">Conditions d'utilisation</a>
                <a href="#" class="footer-link">Contact</a>
                <a href="#" class="footer-link">Aide</a>
            </div>
        </div>
    </footer>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        function openMessageModal(id, title, sender, content, date, link) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-sender').textContent = sender || 'Système';
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-content').textContent = content;
            const actionsDiv = document.getElementById('modal-actions');
            actionsDiv.innerHTML = '';
            if (link) {
                actionsDiv.innerHTML += `<a href="${link}" class="btn btn-outline">Voir détails</a>`;
            }
            document.getElementById('messageModal').style.display = 'block';
        }

        function closeMessageModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        function openComposeModal() {
            document.getElementById('composeModal').style.display = 'block';
        }

        function closeComposeModal() {
            document.getElementById('composeModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                closeMessageModal();
                closeComposeModal();
            }
        }
    </script>
</body>
</html>