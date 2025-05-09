<?php
// Inclure le fichier de connexion à la base de données
require_once __DIR__ . '/../config/database.php';
$conn = getDatabaseConnection();

// Récupération de l'ID du médecin
$medecin_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($medecin_id <= 0) {
    header('Location: Magazine.php');
    exit;
}

// Récupération des informations du médecin
$stmt_doctor = $conn->prepare("
    SELECT m.*, s.nom as specialite_nom
    FROM medecins m
    JOIN specialites s ON m.specialite_id = s.id
    WHERE m.id = :medecin_id
");
$stmt_doctor->bindParam(':medecin_id', $medecin_id);
$stmt_doctor->execute();
$medecin = $stmt_doctor->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    header('Location: Magazine.php');
    exit;
}

// Filtrage par catégorie si spécifié
$categorie_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$whereClause = $categorie_id > 0 ? "AND a.categorie_id = :categorie_id" : "";

// Pagination
$articles_par_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articles_par_page;

// Récupération des articles du médecin (avec pagination et filtrage)
$stmt_articles = $conn->prepare("
    SELECT a.*, c.nom as categorie_nom, COUNT(ca.id) as nb_commentaires
    FROM articles a
    JOIN categories_articles c ON a.categorie_id = c.id
    LEFT JOIN commentaires_articles ca ON a.id = ca.article_id AND ca.approuve = 1
    WHERE a.medecin_id = :medecin_id AND a.statut = 'publie'
    $whereClause
    GROUP BY a.id
    ORDER BY a.date_publication DESC
    LIMIT :offset, :limit
");
$stmt_articles->bindParam(':medecin_id', $medecin_id);
$stmt_articles->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_articles->bindParam(':limit', $articles_par_page, PDO::PARAM_INT);

if ($categorie_id > 0) {
    $stmt_articles->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
}

$stmt_articles->execute();
$articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total d'articles pour la pagination
$sql_count = "
    SELECT COUNT(*) as total
    FROM articles a
    WHERE a.medecin_id = :medecin_id AND a.statut = 'publie'
    $whereClause
";

$stmt_count = $conn->prepare($sql_count);
$stmt_count->bindParam(':medecin_id', $medecin_id);

if ($categorie_id > 0) {
    $stmt_count->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
}

$stmt_count->execute();
$total_articles = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_articles / $articles_par_page);

// Récupération des statistiques du médecin
$stmt_stats = $conn->prepare("
    SELECT 
        COUNT(a.id) as total_articles,
        SUM(a.nb_vues) as total_vues,
        COUNT(DISTINCT a.categorie_id) as nb_categories
    FROM articles a
    WHERE a.medecin_id = :medecin_id AND a.statut = 'publie'
");
$stmt_stats->bindParam(':medecin_id', $medecin_id);
$stmt_stats->execute();
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// Récupération des catégories des articles du médecin
$stmt_categories = $conn->prepare("
    SELECT DISTINCT c.id, c.nom, COUNT(a.id) as nb_articles
    FROM categories_articles c
    JOIN articles a ON c.id = a.categorie_id
    WHERE a.medecin_id = :medecin_id AND a.statut = 'publie'
    GROUP BY c.id
    ORDER BY nb_articles DESC
");
$stmt_categories->bindParam(':medecin_id', $medecin_id);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Récupération des autres médecins populaires
$stmt_doctors = $conn->prepare("
    SELECT m.id, m.nom, m.prenom, s.nom as specialite, COUNT(a.id) as nb_articles
    FROM medecins m
    JOIN specialites s ON m.specialite_id = s.id
    JOIN articles a ON m.id = a.medecin_id
    WHERE a.statut = 'publie' AND m.id != :medecin_id
    GROUP BY m.id
    ORDER BY nb_articles DESC
    LIMIT 5
");
$stmt_doctors->bindParam(':medecin_id', $medecin_id);
$stmt_doctors->execute();
$popular_doctors = $stmt_doctors->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour tronquer du texte
function truncate_text($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . '...';
}

// Fonction pour formater la date
function format_date($date) {
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}
?>

<!-- Le reste du code HTML reste inchangé, sauf pour les liens de catégorie -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles du Dr. <?= htmlspecialchars($medecin['prenom']) ?> <?= htmlspecialchars($medecin['nom']) ?> - MediStatView</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Header styles from existing CSS */
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

        /* Doctor Profile Banner */
        .doctor-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .doctor-banner-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .doctor-banner-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid var(--accent-color1);
            background-color: var(--accent-color2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .doctor-banner-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doctor-banner-info {
            flex: 1;
        }

        .doctor-banner-name {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .doctor-banner-specialty {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        .doctor-banner-bio {
            margin-bottom: 1.5rem;
            max-width: 800px;
            line-height: 1.8;
        }

        .doctor-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }

        .doctor-stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            min-width: 120px;
        }

        .doctor-stat-number {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--accent-color1);
        }

        .doctor-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Magazine Layout */
        .magazine-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto 4rem;
            padding: 0 1rem;
        }

        .content-area {
            width: 100%;
        }

        .sidebar {
            width: 100%;
        }

        .section-title {
            position: relative;
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--accent-color1);
        }

        .categories-list {
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
        }

        .category-link {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: white;
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 30px;
            font-size: 0.9rem;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .category-link:hover, .category-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .category-count {
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 0.1rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .article-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .article-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .article-category {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: var(--accent-color1);
            color: var(--text-dark);
            padding: 0.3rem 0.8rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .article-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .article-title {
            margin-bottom: 0.8rem;
            font-size: 1.2rem;
            color: var(--primary-color);
            text-decoration: none;
        }

        .article-title:hover {
            color: var(--secondary-color);
        }

        .article-excerpt {
            margin-bottom: 1rem;
            color: #555;
            flex-grow: 1;
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #777;
            margin-top: auto;
        }

        .article-date {
            display: flex;
            align-items: center;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }

        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .pagination a:hover, .pagination span.current {
            background-color: var(--secondary-color);
            color: white;
        }

        /* Sidebar */
        .sidebar-widget {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .widget-title {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .popular-doctors {
            list-style: none;
        }

        .doctor-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .doctor-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .doctor-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: color 0.3s;
        }

        .doctor-link:hover {
            color: var(--secondary-color);
        }

        .doctor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
        }

        .doctor-details {
            flex: 1;
        }

        .doctor-name {
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .doctor-specialty {
            font-size: 0.85rem;
            color: #777;
        }

        .doctor-stats-small {
            font-size: 0.8rem;
            color: #999;
            display: flex;
            align-items: center;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #777;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .magazine-layout {
                grid-template-columns: 1fr;
            }
            
            .doctor-banner-content {
                flex-direction: column;
                text-align: center;
            }
            
            .doctor-stats {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .articles-grid {
                grid-template-columns: 1fr;
            }
            
            .doctor-banner-name {
                font-size: 1.8rem;
            }
        }

        /* Footer styles from existing CSS */
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
        }

        .footer-links a:hover {
            color: var(--accent-color1);
        }

        .footer-contact p {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }

        .contact-icon {
            margin-right: 0.8rem;
            color: var(--accent-color1);
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
        }

        .social-icon:hover {
            background-color: var(--accent-color1);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
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
                        <li>
                            <a href="index.php" class="nav-link">
                                <i class="fas fa-home"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docFilterMedcin.php" class="nav-link">
                                <i class="fas fa-user-md"></i>
                                <span>Médecin</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="userPharmacie.php" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Pharmacie</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="medicaments.php" class="nav-link">
                                <i class="fas fa-capsules"></i>
                                <span>Médicaments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Questions.php" class="nav-link">
                                <i class="fas fa-question-circle"></i>
                                <span>Questions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Magazine.php" class="nav-link active">
                                <i class="fas fa-book-medical"></i>
                                <span>Magazine</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Doctor Profile Banner -->
    <section class="doctor-banner">
        <div class="doctor-banner-content">
            <div class="doctor-banner-image">
                <?php if (!empty($medecin['image_principale'])): ?>
                    <img src="<?= htmlspecialchars($medecin['image_principale']) ?>" alt="Dr. <?= htmlspecialchars($medecin['prenom']) ?> <?= htmlspecialchars($medecin['nom']) ?>">
                <?php else: ?>
                    <?= substr(htmlspecialchars($medecin['prenom']), 0, 1) . substr(htmlspecialchars($medecin['nom']), 0, 1) ?>
                <?php endif; ?>
            </div>
            <div class="doctor-banner-info">
                <h1 class="doctor-banner-name">Dr. <?= htmlspecialchars($medecin['prenom']) ?> <?= htmlspecialchars($medecin['nom']) ?></h1>
                <div class="doctor-banner-specialty"><?= htmlspecialchars($medecin['specialite_nom']) ?></div>
                
                <?php if (!empty($medecin['bio'])): ?>
                <div class="doctor-banner-bio">
                    <?= htmlspecialchars($medecin['bio']) ?>
                </div>
                <?php endif; ?>
                
                <div class="doctor-stats">
                    <div class="doctor-stat-item">
                        <div class="doctor-stat-number"><?= $stats['total_articles'] ?></div>
                        <div class="doctor-stat-label">Articles</div>
                    </div>
                    <div class="doctor-stat-item">
                        <div class="doctor-stat-number"><?= $stats['total_vues'] ?></div>
                        <div class="doctor-stat-label">Vues</div>
                    </div>
                    <div class="doctor-stat-item">
                        <div class="doctor-stat-number"><?= $stats['nb_categories'] ?></div>
                        <div class="doctor-stat-label">Catégories</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Magazine Content -->
    <div class="magazine-layout">
        <div class="content-area">
            <!-- Catégories -->
            <?php if (count($categories) > 0): ?>
                <div class="categories-list">
                    <a href="doctor-articles.php?id=<?= $medecin_id ?>" class="category-link <?= $categorie_id == 0 ? 'active' : '' ?>">Tous</a>
                    <?php foreach ($categories as $categorie): ?>
                        <a href="doctor-articles.php?id=<?= $medecin_id ?>&categorie=<?= $categorie['id'] ?>" class="category-link <?= $categorie_id == $categorie['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($categorie['nom']) ?>
                            <span class="category-count"><?= $categorie['nb_articles'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Articles du médecin -->
            <section class="all-articles">
                <h2 class="section-title">Articles du Dr. <?= htmlspecialchars($medecin['prenom']) ?> <?= htmlspecialchars($medecin['nom']) ?></h2>
                
                <?php if (count($articles) > 0): ?>
                    <div class="articles-grid">
                        <?php foreach ($articles as $article): ?>
                            <article class="article-card">
                                <div class="article-image" style="background-image: url('<?= !empty($article['image_principale']) ? htmlspecialchars($article['image_principale']) : 'assets/images/default-article.jpg' ?>')">
                                    <span class="article-category"><?= htmlspecialchars($article['categorie_nom']) ?></span>
                                </div>
                                <div class="article-content">
                                    <a href="article.php?slug=<?= htmlspecialchars($article['slug']) ?>" class="article-title">
                                        <h3><?= htmlspecialchars($article['titre']) ?></h3>
                                    </a>
                                    <div class="article-excerpt">
                                        <?= truncate_text(htmlspecialchars($article['resume'])) ?>
                                    </div>
                                    <div class="article-meta">
                                        <div class="article-date">
                                            <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                                            <?= format_date($article['date_publication']) ?>
                                        </div>
                                        <div class="article-stats">
                                        <i class="far fa-eye" style="margin-right: 5px;"></i>
                                            <?= $article['nb_vues'] ?>
                                        </div>
                                        <div class="article-comments">
                                            <i class="far fa-comment" style="margin-right: 5px;"></i>
                                            <?= $article['nb_commentaires'] ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="doctor-articles.php?id=<?= $medecin_id ?>&page=<?= $page - 1 ?><?= $categorie_id ? '&categorie=' . $categorie_id : '' ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="current"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="doctor-articles.php?id=<?= $medecin_id ?>&page=<?= $i ?><?= $categorie_id ? '&categorie=' . $categorie_id : '' ?>"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="doctor-articles.php?id=<?= $medecin_id ?>&page=<?= $page + 1 ?><?= $categorie_id ? '&categorie=' . $categorie_id : '' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h3>Aucun article disponible</h3>
                        <p>Le Dr. <?= htmlspecialchars($medecin['prenom']) ?> <?= htmlspecialchars($medecin['nom']) ?> n'a pas encore publié d'articles.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
        
        <div class="sidebar">
            <!-- Widget des catégories -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Catégories</h3>
                <?php if (count($categories) > 0): ?>
                    <div class="categories-list">
                        <?php foreach ($categories as $categorie): ?>
                            <a href="Magazine.php?categorie=<?= $categorie['id'] ?>" class="category-link">
                                <?= htmlspecialchars($categorie['nom']) ?>
                                <span class="category-count"><?= $categorie['nb_articles'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucune catégorie disponible.</p>
                <?php endif; ?>
            </div>
            
            <!-- Widget des médecins populaires -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Médecins populaires</h3>
                <?php if (count($popular_doctors) > 0): ?>
                    <ul class="popular-doctors">
                        <?php foreach ($popular_doctors as $doctor): ?>
                            <li class="doctor-item">
                                <a href="doctor-articles.php?id=<?= $doctor['id'] ?>" class="doctor-link">
                                    <div class="doctor-avatar">
                                        <?= substr(htmlspecialchars($doctor['prenom']), 0, 1) . substr(htmlspecialchars($doctor['nom']), 0, 1) ?>
                                    </div>
                                    <div class="doctor-details">
                                        <div class="doctor-name">Dr. <?= htmlspecialchars($doctor['prenom']) ?> <?= htmlspecialchars($doctor['nom']) ?></div>
                                        <div class="doctor-specialty"><?= htmlspecialchars($doctor['specialite']) ?></div>
                                        <div class="doctor-stats-small">
                                            <i class="fas fa-newspaper" style="margin-right: 5px;"></i>
                                            <?= $doctor['nb_articles'] ?> articles
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun médecin populaire disponible.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>À propos de MediStatView</h3>
                <p>MediStatView est une plateforme qui met en relation les patients avec des professionnels de santé qualifiés, offrant des services médicaux innovants et des informations fiables.</p>
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
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="docFilterMedcin.php">Médecins</a></li>
                    <li><a href="userPharmacie.php">Pharmacies</a></li>
                    <li><a href="medicaments.php">Médicaments</a></li>
                    <li><a href="Questions.php">Questions</a></li>
                    <li><a href="Magazine.php">Magazine</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Services</h3>
                <ul class="footer-links">
                    <li><a href="#">Consultation en ligne</a></li>
                    <li><a href="#">Prise de rendez-vous</a></li>
                    <li><a href="#">Conseil pharmaceutique</a></li>
                    <li><a href="#">Information médicale</a></li>
                    <li><a href="#">Téléconsultation</a></li>
                </ul>
            </div>
            <div class="footer-column footer-contact">
                <h3>Contact</h3>
                <p><i class="fas fa-map-marker-alt contact-icon"></i> 123 Rue de la Santé, 75000 Paris</p>
                <p><i class="fas fa-phone-alt contact-icon"></i> +33 1 23 45 67 89</p>
                <p><i class="fas fa-envelope contact-icon"></i> contact@medistatview.com</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 MediStatView. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>