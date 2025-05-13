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

// Récupérer les informations du médecin connecté
$medecin_id = $_SESSION['medecin_id'];
$stmt_medecin = $pdo->prepare("
    SELECT m.civilite, m.nom, m.prenom, s.nom AS specialite 
    FROM medecins m 
    LEFT JOIN specialites s ON m.specialite_id = s.id 
    WHERE m.id = ?
");
$stmt_medecin->execute([$medecin_id]);
$medecin = $stmt_medecin->fetch(PDO::FETCH_ASSOC);

// Gestion des filtres
$patient_id_filter = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;
$type_document_filter = isset($_GET['type_document']) ? $_GET['type_document'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_conditions = ["dm.medecin_id = ?"];
$params = [$medecin_id];

if ($patient_id_filter > 0) {
    $where_conditions[] = "dm.patient_id = ?";
    $params[] = $patient_id_filter;
}

if (!empty($type_document_filter)) {
    $where_conditions[] = "dm.type_document = ?";
    $params[] = $type_document_filter;
}

if (!empty($search_query)) {
    $where_conditions[] = "(p.nom LIKE ? OR p.prenom LIKE ? OR dm.titre LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$where_clause = count($where_conditions) > 0 ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Récupérer les documents médicaux
$stmt_documents = $pdo->prepare("
    SELECT dm.id, dm.patient_id, dm.type_document, dm.titre, dm.chemin_fichier, dm.date_document, dm.notes,
           p.nom AS patient_nom, p.prenom AS patient_prenom
    FROM documents_medicaux dm
    JOIN patients p ON dm.patient_id = p.id
    $where_clause
    ORDER BY dm.date_document DESC
");
$stmt_documents->execute($params);
$documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des patients pour le filtre
$stmt_patients = $pdo->prepare("
    SELECT DISTINCT p.id, p.nom, p.prenom
    FROM patients p
    JOIN documents_medicaux dm ON p.id = dm.patient_id
    WHERE dm.medecin_id = ?
    ORDER BY p.nom, p.prenom
");
$stmt_patients->execute([$medecin_id]);
$patients = $stmt_patients->fetchAll(PDO::FETCH_ASSOC);

// Gestion de l'envoi par email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email']) && isset($_POST['document_id'])) {
    $document_id = (int)$_POST['document_id'];
    $recipient_email = trim($_POST['email']);

    // Vérifier le document
    $stmt_doc = $pdo->prepare("
        SELECT chemin_fichier, titre
        FROM documents_medicaux
        WHERE id = ? AND medecin_id = ?
    ");
    $stmt_doc->execute([$document_id, $medecin_id]);
    $document = $stmt_doc->fetch(PDO::FETCH_ASSOC);

    if ($document && filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        // Simuler l'envoi d'email (remplacer par une vraie implémentation avec PHPMailer si nécessaire)
        $subject = "Envoi de document médical: " . $document['titre'];
        $message = "Bonjour,\n\nVeuillez trouver ci-joint le document médical: " . $document['titre'] . ".\n\nCordialement,\nDr. " . $medecin['prenom'] . " " . $medecin['nom'];
        $headers = "From: " . $medecin['email'];

        // Pour un envoi réel, utiliser PHPMailer avec pièce jointe
        // Ici, simulation avec mail() pour l'exemple
        if (mail($recipient_email, $subject, $message, $headers)) {
            $success_message = "Document envoyé avec succès à $recipient_email.";
        } else {
            $error_message = "Échec de l'envoi du document. Veuillez réessayer.";
        }
    } else {
        $error_message = "Adresse email invalide ou document non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossiers Médicaux - MediStatView</title>
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

        main {
            flex: 1;
            padding: 2rem;
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

        .filter-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-form select,
        .filter-form input {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .filter-form button {
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-form button:hover {
            background-color: #164a5b;
        }

        .documents-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .documents-table th,
        .documents-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .documents-table th {
            background-color: var(--light-bg);
            color: var(--primary-color);
            font-weight: 600;
        }

        .document-row {
            transition: background-color 0.3s ease;
        }

        .document-row:hover {
            background-color: var(--light-bg);
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

        .email-form {
            display: none;
            margin-top: 0.5rem;
        }

        .email-form.active {
            display: block;
        }

        .email-form input {
            padding: 0.5rem;
            width: 100%;
            margin-bottom: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }

        .message {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 6px;
        }

        .message.success {
            background-color: rgba(123, 186, 154, 0.2);
            color: var(--accent-color1);
        }

        .message.error {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .documents-table thead {
                display: none;
            }

            .documents-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
            }

            .documents-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 1rem;
                border-bottom: none;
            }

            .documents-table td::before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--primary-color);
            }

            .documents-table td:last-child {
                border-bottom: none;
            }
        }

        @media (max-width: 576px) {
            main {
                padding: 1rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
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
                        <li class="nav-item"><a href="docDashboard.php" class="nav-link"><i class="fas fa-home"></i> Tableau de bord</a></li>
                        <li class="nav-item"><a href="docPatient.php" class="nav-link"><i class="fas fa-users"></i> Patients</a></li>
                        <li class="nav-item"><a href="docRendezVous.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Rendez-vous</a></li>
                        <li class="nav-item"><a href="docDossier.php" class="nav-link active"><i class="fas fa-folder-open"></i> Dossiers</a></li>
                        <li class="nav-item"><a href="docPrescriptions.php" class="nav-link"><i class="fas fa-prescription"></i> Prescriptions</a></li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <button class="user-btn">
                        <div class="user-avatar">
                            <?= substr($medecin['prenom'] ?? '', 0, 1) . substr($medecin['nom'] ?? '', 0, 1) ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($medecin['civilite'] ?? '') . ' ' . htmlspecialchars($medecin['prenom'] ?? '') ?></span>
                            <span class="user-role"><?= htmlspecialchars($medecin['specialite'] ?? 'Spécialité non définie') ?></span>
                        </div>
                    </button>
                    <div class="dropdown-menu">
                        <a href="docProfile.php" class="dropdown-item"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="docParametres.php" class="dropdown-item"><i class="fas fa-cog"></i> Paramètres</a>
                        <a href="docNotifications.php" class="dropdown-item"><i class="fas fa-bell"></i> Notifications</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item"><i class="fas fa-question-circle"></i> Aide & Support</a>
                        <a href="docDeconnection.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="documents-section">
            <div class="section-header">
                <h2 class="section-title">Dossiers Médicaux</h2>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="message success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="message error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form class="filter-form" method="GET">
                <select name="patient_id">
                    <option value="0">Tous les patients</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient['id'] ?>" <?= $patient_id_filter == $patient['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="type_document">
                    <option value="">Tous les types</option>
                    <option value="ordonnance" <?= $type_document_filter == 'ordonnance' ? 'selected' : '' ?>>Ordonnance</option>
                    <option value="analyse" <?= $type_document_filter == 'analyse' ? 'selected' : '' ?>>Analyse</option>
                    <option value="radiologie" <?= $type_document_filter == 'radiologie' ? 'selected' : '' ?>>Radiologie</option>
                    <option value="autre" <?= $type_document_filter == 'autre' ? 'selected' : '' ?>>Autre</option>
                </select>
                <input type="text" name="search" placeholder="Rechercher par patient ou titre" value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit">Filtrer</button>
            </form>

            <table class="documents-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Aucun document trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $document): ?>
                            <tr class="document-row">
                                <td data-label="Patient"><?= htmlspecialchars($document['patient_prenom'] . ' ' . $document['patient_nom']) ?></td>
                                <td data-label="Type"><?= htmlspecialchars(ucfirst($document['type_document'])) ?></td>
                                <td data-label="Titre"><?= htmlspecialchars($document['titre']) ?></td>
                                <td data-label="Date"><?= date('d M, Y', strtotime($document['date_document'])) ?></td>
                                <td data-label="Actions">
                                    <a href="<?= htmlspecialchars($document['chemin_fichier']) ?>" download class="btn btn-primary"><i class="fas fa-download"></i> Télécharger</a>
                                    <button class="btn btn-secondary email-toggle" data-id="<?= $document['id'] ?>"><i class="fas fa-envelope"></i> Envoyer</button>
                                    <form class="email-form" id="email-form-<?= $document['id'] ?>" method="POST">
                                        <input type="hidden" name="document_id" value="<?= $document['id'] ?>">
                                        <input type="email" name="email" placeholder="Adresse email du destinataire" required>
                                        <button type="submit" name="send_email" class="btn btn-primary">Envoyer par email</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        document.querySelectorAll('.email-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const docId = button.getAttribute('data-id');
                const form = document.getElementById(`email-form-${docId}`);
                form.classList.toggle('active');
            });
        });
    </script>
</body>
</html>