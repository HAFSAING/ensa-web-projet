<?php
require_once __DIR__ . '/../config/database.php';
$conn = getDatabaseConnection();

// Récupération des catégories
$stmt_categories = $conn->prepare("SELECT * FROM categories_articles ORDER BY nom ASC");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Récupération des articles mis en avant
$stmt_spotlight = $conn->prepare("
    SELECT a.*, c.nom as categorie_nom, m.nom as medecin_nom, m.prenom as medecin_prenom, 
           s.nom as specialite_nom
    FROM articles a
    JOIN categories_articles c ON a.categorie_id = c.id
    JOIN medecins m ON a.medecin_id = m.id
    JOIN specialites s ON m.specialite_id = s.id
    WHERE a.est_mis_en_avant = 1 AND a.statut = 'publie'
    ORDER BY a.date_publication DESC
    LIMIT 3
");
$stmt_spotlight->execute();
$articles_spotlight = $stmt_spotlight->fetchAll(PDO::FETCH_ASSOC);

// Filtrage par catégorie si spécifié
$categorie_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$whereClause = $categorie_id > 0 ? "AND a.categorie_id = :categorie_id" : "";

// Recherche si spécifiée
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchClause = !empty($search) ? "AND (a.titre LIKE :search OR a.resume LIKE :search OR a.contenu LIKE :search)" : "";

// Pagination
$articles_par_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articles_par_page;

// Récupération des articles (avec pagination, filtrage et recherche)
$sql_articles = "
    SELECT a.*, c.nom as categorie_nom, m.nom as medecin_nom, m.prenom as medecin_prenom, 
           s.nom as specialite_nom, COUNT(ca.id) as nb_commentaires
    FROM articles a
    JOIN categories_articles c ON a.categorie_id = c.id
    JOIN medecins m ON a.medecin_id = m.id
    JOIN specialites s ON m.specialite_id = s.id
    LEFT JOIN commentaires_articles ca ON a.id = ca.article_id AND ca.approuve = 1
    WHERE a.statut = 'publie' 
    $whereClause
    $searchClause
    GROUP BY a.id
    ORDER BY a.date_publication DESC
    LIMIT :offset, :limit
";

$stmt_articles = $conn->prepare($sql_articles);
$stmt_articles->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_articles->bindParam(':limit', $articles_par_page, PDO::PARAM_INT);

if ($categorie_id > 0) {
    $stmt_articles->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
}

if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt_articles->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$stmt_articles->execute();
$articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total d'articles pour la pagination
$sql_count = "
    SELECT COUNT(DISTINCT a.id) as total
    FROM articles a
    WHERE a.statut = 'publie'
    $whereClause
    $searchClause
";

$stmt_count = $conn->prepare($sql_count);

if ($categorie_id > 0) {
    $stmt_count->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
}

if (!empty($search)) {
    $stmt_count->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$stmt_count->execute();
$total_articles = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_articles / $articles_par_page);

// Récupération des médecins populaires (avec le plus d'articles)
$stmt_doctors = $conn->prepare("
    SELECT m.id, m.nom, m.prenom, s.nom as specialite, COUNT(a.id) as nb_articles
    FROM medecins m
    JOIN specialites s ON m.specialite_id = s.id
    JOIN articles a ON m.id = a.medecin_id
    WHERE a.statut = 'publie'
    GROUP BY m.id
    ORDER BY nb_articles DESC
    LIMIT 5
");
$stmt_doctors->execute();
$popular_doctors = $stmt_doctors->fetchAll(PDO::FETCH_ASSOC);

// Récupération des articles récents
$stmt_recent = $conn->prepare("
    SELECT a.id, a.titre, a.slug, a.date_publication
    FROM articles a
    WHERE a.statut = 'publie'
    ORDER BY a.date_publication DESC
    LIMIT 5
");
$stmt_recent->execute();
$recent_articles = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine Santé - MediStatView</title>
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

        /* Magazine specific styles */
        .magazine-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 3rem 0;
            text-align: center;
            margin-bottom: 2rem;
        }

        .magazine-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .magazine-header p {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .search-box {
            max-width: 600px;
            margin: 1.5rem auto 0;
            display: flex;
            overflow: hidden;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .search-box input {
            flex: 1;
            padding: 0.8rem 1.5rem;
            border: none;
            font-size: 1rem;
        }

        .search-box button {
            background-color: var(--accent-color1);
            color: var(--text-dark);
            border: none;
            padding: 0 1.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .search-box button:hover {
            background-color: #6aa889;
        }

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

        .spotlight-section {
            margin-bottom: 3rem;
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

        .spotlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .spotlight-article {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .spotlight-article:hover {
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

        .doctor-info {
            display: flex;
            align-items: center;
        }

        .doctor-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--accent-color2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .article-date {
            display: flex;
            align-items: center;
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

        .all-articles {
            margin-bottom: 3rem;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
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

        .doctor-link .doctor-avatar {
            width: 40px;
            height: 40px;
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

        .doctor-stats {
            font-size: 0.8rem;
            color: #999;
            display: flex;
            align-items: center;
        }

        .recent-articles {
            list-style: none;
        }

        .recent-article-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .recent-article-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .recent-article-link {
            text-decoration: none;
            color: var(--text-dark);
            transition: color 0.3s;
            display: block;
            font-weight: 500;
        }

        .recent-article-link:hover {
            color: var(--secondary-color);
        }

        .recent-article-date {
            font-size: 0.8rem;
            color: #777;
            margin-top: 0.3rem;
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
        }

        @media (max-width: 768px) {
            .spotlight-grid, .articles-grid {
                grid-template-columns: 1fr;
            }
            
            .magazine-header h1 {
                font-size: 2rem;
            }
            
            .magazine-header p {
                font-size: 1rem;
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
    <!-- Ajouter Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- Magazine Header with Search -->
    <section class="magazine-header">
        <div class="container">
            <h1>Magazine Santé MediStatView</h1>
            <p>Découvrez les derniers articles et conseils rédigés par nos médecins experts pour prendre soin de votre santé au quotidien.</p>
            
            <form action="Magazine.php" method="GET" class="search-box">
                <input type="text" name="search" placeholder="Rechercher un article..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Rechercher</button>
            </form>
        </div>
    </section>
    
    <!-- Magazine Content -->
    <div class="magazine-layout">
        <div class="content-area">
            <!-- Catégories -->
            <div class="categories-list">
                <a href="Magazine.php" class="category-link <?= $categorie_id == 0 ? 'active' : '' ?>">Tous</a>
                <?php foreach ($categories as $categorie): ?>
                    <a href="Magazine.php?categorie=<?= $categorie['id'] ?>" class="category-link <?= $categorie_id == $categorie['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($categorie['nom']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Articles à la une -->
            <?php if (count($articles_spotlight) > 0): ?>
                <section class="spotlight-section">
                    <h2 class="section-title">À la une</h2>
                    <div class="spotlight-grid">
                        <?php foreach ($articles_spotlight as $article): ?>
                            <article class="spotlight-article">
                                <div class="article-image" style="background-image: url('<?= !empty($article['image_url']) ? htmlspecialchars($article['image_url']) : 'assets/images/default-article.jpg' ?>')">
                                    <span class="article-category"><?= htmlspecialchars($article['categorie_nom']) ?></span>
                                </div>
                                <div class="article-content">
                                    <a href="doctor-articles.php?slug=<?= htmlspecialchars($article['slug']) ?>" class="article-title">
                                        <h3><?= htmlspecialchars($article['titre']) ?></h3>
                                    </a>
                                    <div class="article-excerpt">
                                        <?= truncate_text(htmlspecialchars($article['resume'])) ?>
                                    </div>
                                    <div class="article-meta">
                                        <div class="doctor-info">
                                            <div class="doctor-avatar">
                                                <?= substr(htmlspecialchars($article['medecin_prenom']), 0, 1) . substr(htmlspecialchars($article['medecin_nom']), 0, 1) ?>
                                            </div>
                                            <span>Dr. <?= htmlspecialchars($article['medecin_prenom']) ?> <?= htmlspecialchars($article['medecin_nom']) ?></span>
                                        </div>
                                        <div class="article-date">
                                            <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                                            <?= format_date($article['date_publication']) ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            
            <!-- Tous les articles -->
            <section class="all-articles">
                <h2 class="section-title">
                    <?php if ($categorie_id > 0): ?>
                        Articles dans <?= htmlspecialchars($categories[array_search($categorie_id, array_column($categories, 'id'))]['nom']) ?>
                    <?php elseif (!empty($search)): ?>
                        Résultats de recherche pour "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Tous les articles
                    <?php endif; ?>
                </h2>
                
                <?php if (count($articles) > 0): ?>
                    <div class="articles-grid">
                        <?php foreach ($articles as $article): ?>
                            <article class="article-card">
                                <div class="article-image" style="background-image: url('<?= !empty($article['image_url']) ? htmlspecialchars($article['image_url']) : 'assets/images/default-article.jpg' ?>')">
                                    <span class="article-category"><?= htmlspecialchars($article['categorie_nom']) ?></span>
                                </div>
                                <div class="article-content">
                                    <a href="doctor-articles.php?slug=<?= htmlspecialchars($article['slug']) ?>" class="article-title">
                                        <h3><?= htmlspecialchars($article['titre']) ?></h3>
                                    </a>
                                    <div class="article-excerpt">
                                        <?= truncate_text(htmlspecialchars($article['resume'])) ?>
                                    </div>
                                    <div class="article-meta">
                                        <div class="doctor-info">
                                            <div class="doctor-avatar">
                                                <?= substr(htmlspecialchars($article['medecin_prenom']), 0, 1) . substr(htmlspecialchars($article['medecin_nom']), 0, 1) ?>
                                            </div>
                                            <span>Dr. <?= htmlspecialchars($article['medecin_prenom']) ?> <?= htmlspecialchars($article['medecin_nom']) ?></span>
                                        </div>
                                        <div class="article-date">
                                            <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                                            <?= format_date($article['date_publication']) ?>
                                            <?php if ($article['nb_commentaires'] > 0): ?>
                                                <i class="far fa-comments" style="margin-left: 10px; margin-right: 5px;"></i>
                                                <?= $article['nb_commentaires'] ?>
                                            <?php endif; ?>
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
                                <a href="?page=<?= $page - 1 ?><?= $categorie_id ? '&categorie=' . $categorie_id : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<a href="?page=1' . ($categorie_id ? '&categorie=' . $categorie_id : '') . (!empty($search) ? '&search=' . urlencode($search) : '') . '">1</a>';
                                if ($start_page > 2) {
                                    echo '<span>...</span>';
                                }
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo '<span class="current">' . $i . '</span>';
                                } else {
                                    echo '<a href="?page=' . $i . ($categorie_id ? '&categorie=' . $categorie_id : '') . (!empty($search) ? '&search=' . urlencode($search) : '') . '">' . $i . '</a>';
                                }
                            }
                            
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<span>...</span>';
                                }
                                echo '<a href="?page=' . $total_pages . ($categorie_id ? '&categorie=' . $categorie_id : '') . (!empty($search) ? '&search=' . urlencode($search) : '') . '">' . $total_pages . '</a>';
                            }
                            ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?><?= $categorie_id ? '&categorie=' . $categorie_id : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Aucun article trouvé</h3>
                        <p>Nous n'avons pas trouvé d'articles correspondant à votre recherche. Essayez avec d'autres termes ou consultez une autre catégorie.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
        
        <div class="sidebar">
            <!-- Médecins populaires -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Médecins contributeurs</h3>
                <ul class="popular-doctors">
                    <?php foreach ($popular_doctors as $doctor): ?>
                        <li class="doctor-item">
                            <a href="doctor.php?id=<?= $doctor['id'] ?>" class="doctor-link">
                                <div class="doctor-avatar">
                                    <?= substr(htmlspecialchars($doctor['prenom']), 0, 1) . substr(htmlspecialchars($doctor['nom']), 0, 1) ?>
                                </div>
                                <div class="doctor-details">
                                    <div class="doctor-name">Dr. <?= htmlspecialchars($doctor['prenom']) ?> <?= htmlspecialchars($doctor['nom']) ?></div>
                                    <div class="doctor-specialty"><?= htmlspecialchars($doctor['specialite']) ?></div>
                                    <div class="doctor-stats">
                                        <i class="fas fa-file-medical" style="margin-right: 5px;"></i>
                                        <?= $doctor['nb_articles'] ?> article<?= $doctor['nb_articles'] > 1 ? 's' : '' ?>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Articles récents -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Articles récents</h3>
                <ul class="recent-articles">
                    <?php foreach ($recent_articles as $recent): ?>
                        <li class="recent-article-item">
                            <a href="doctor-articles.php?slug=<?= htmlspecialchars($recent['slug']) ?>" class="recent-article-link">
                                <?= htmlspecialchars($recent['titre']) ?>
                            </a>
                            <div class="recent-article-date">
                                <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                                <?= format_date($recent['date_publication']) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Newsletter -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Newsletter</h3>
                <p style="margin-bottom: 1rem;">Recevez les derniers articles et conseils directement dans votre boîte mail.</p>
                <form action="newsletter-subscribe.php" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                    <input type="email" name="email" placeholder="Votre adresse email" required style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
                    <button type="submit" style="background-color: var(--accent-color1); color: var(--text-dark); border: none; padding: 0.8rem; border-radius: 5px; font-weight: 600; cursor: pointer;">
                        S'abonner
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>À propos</h3>
                <ul class="footer-links">
                    <li><a href="about.php">Qui sommes-nous</a></li>
                    <li><a href="mission.php">Notre mission</a></li>
                    <li><a href="team.php">Notre équipe</a></li>
                    <li><a href="blog.php">Blog</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Services</h3>
                <ul class="footer-links">
                    <li><a href="docFilterMedcin.php">Trouver un médecin</a></li>
                    <li><a href="userPharmacie.php">Pharmacies</a></li>
                    <li><a href="medicaments.php">Médicaments</a></li>
                    <li><a href="Questions.php">Questions & Réponses</a></li>
                    <li><a href="Magazine.php">Magazine Santé</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact</h3>
                <p><i class="fas fa-map-marker-alt contact-icon"></i> 123 Rue de la Santé, Ville</p>
                <p><i class="fas fa-phone contact-icon"></i> +33 1 23 45 67 89</p>
                <p><i class="fas fa-envelope contact-icon"></i> contact@medistatview.com</p>
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> MediStatView. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>