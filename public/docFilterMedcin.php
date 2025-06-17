<?php
// Connexion à la base de données
$servername = "localhost";
$username = "votre_utilisateur";
$password = "votre_mot_de_passe";
$dbname = "medistatview";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Préparer les conditions de filtrage
$conditions = [];
$params = [];

if (!empty($_GET['specialite']) && $_GET['specialite'] !== 'Toutes les spécialités') {
    $conditions[] = "s.nom = :specialite";
    $params[':specialite'] = $_GET['specialite'];
}
if (!empty($_GET['pays']) && $_GET['pays'] !== 'Tous les pays') {
    $conditions[] = "m.pays = :pays";
    $params[':pays'] = $_GET['pays'];
}
if (!empty($_GET['ville']) && $_GET['ville'] !== 'Toutes les villes') {
    $conditions[] = "v.nom = :ville";
    $params[':ville'] = $_GET['ville'];
}
if (!empty($_GET['genre']) && $_GET['genre'] !== 'Tous') {
    $conditions[] = "m.genre = :genre";
    $params[':genre'] = $_GET['genre'];
}
if (!empty($_GET['langue']) && $_GET['langue'] !== 'Toutes les langues') {
    $conditions[] = "m.langue_parlee LIKE :langue";
    $params[':langue'] = "%{$_GET['langue']}%";
}
if (!empty($_GET['visite_domicile'])) {
    $conditions[] = "m.visite_domicile = 1";
}
if (!empty($_GET['garde_24h'])) {
    $conditions[] = "m.garde_24h = 1";
}

// Requête SQL pour récupérer les médecins
$sql = "SELECT 
            m.id,
            m.civilite,
            m.nom,
            m.prenom,
            m.adresse_cabinet,
            m.telephone_cabinet,
            m.email,
            m.genre,
            m.pays,
            m.langue_parlee,
            m.visite_domicile,
            m.garde_24h,
            s.nom AS specialite,
            v.nom AS ville
        FROM 
            medecins m
            INNER JOIN specialites s ON m.specialite_id = s.id
            INNER JOIN villes v ON m.ville_id = v.id
        WHERE 
            m.statut = 'actif'";
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY m.nom, m.prenom";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Listes pour les filtres
$specialites = ['Toutes les spécialités', 'Cardiologie', 'Dermatologie', 'Gastro-entérologie', 'Gynécologie', 'Neurologie', 'Ophtalmologie', 'ORL', 'Pédiatrie', 'Psychiatrie', 'Rhumatologie'];
$pays = ['Tous les pays', 'Maroc', 'France', 'Belgique', 'Suisse', 'Canada'];
$villes = ['Toutes les villes', 'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', 'Oujda'];
$genres = ['Tous', 'Homme', 'Femme'];
$langues = ['Toutes les langues', 'Français', 'Arabe', 'Anglais', 'Espagnol'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Médecins - MediStatView</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 2em; }
        nav { margin-top: 10px; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-size: 1.1em; }
        nav a:hover { text-decoration: underline; }
        .filter-section { background-color: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filter-section h2 { margin-top: 0; color: #2c3e50; }
        .filter-section form { display: flex; flex-wrap: wrap; gap: 10px; }
        .filter-section select, .filter-section button { padding: 10px; font-size: 1em; border: 1px solid #ccc; border-radius: 5px; }
        .filter-section button { background-color: #3498db; color: white; cursor: pointer; }
        .filter-section button:hover { background-color: #2980b9; }
        .filter-section label { margin-right: 10px; }
        .medecin-section { margin: 20px 0; }
        .medecin-card { background-color: white; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .medecin-card h3 { margin: 0; color: #2c3e50; font-size: 1.5em; }
        .medecin-card p { margin: 5px 0; color: #555; }
        .medecin-card .specialite { font-weight: bold; color: #3498db; }
        .medecin-card .adresse { font-style: italic; }
        .medecin-card .services { margin-top: 10px; }
        .medecin-card .btn-rdv { display: inline-block; background-color: #e74c3c; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .medecin-card .btn-rdv:hover { background-color: #c0392b; }
        footer { background-color: #2c3e50; color: white; padding: 20px; text-align: center; margin-top: 20px; }
        footer p { margin: 5px 0; }
        footer a { color: #3498db; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>MediStatView</h1>
            <h2>SERVICES</h2>
            <nav>
                <a href="#">Accueil</a>
                <a href="#">Médecin</a>
                <a href="#">Pharmacie</a>
                <a href="#">Médicaments</a>
                <a href="#">Questions</a>
                <a href="#">Magazine</a>
            </nav>
        </header>

        <section class="filter-section">
            <h2>Filtrer par</h2>
            <form method="GET" action="">
                <select name="specialite">
                    <?php foreach ($specialites as $sp): ?>
                        <option value="<?php echo htmlspecialchars($sp); ?>" <?php echo (isset($_GET['specialite']) && $_GET['specialite'] === $sp) ? 'selected' : ''; ?>><?php echo htmlspecialchars($sp); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="pays">
                    <?php foreach ($pays as $p): ?>
                        <option value="<?php echo htmlspecialchars($p); ?>" <?php echo (isset($_GET['pays']) && $_GET['pays'] === $p) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="ville">
                    <?php foreach ($villes as $v): ?>
                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo (isset($_GET['ville']) && $_GET['ville'] === $v) ? 'selected' : ''; ?>><?php echo htmlspecialchars($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="genre">
                    <?php foreach ($genres as $g): ?>
                        <option value="<?php echo htmlspecialchars($g); ?>" <?php echo (isset($_GET['genre']) && $_GET['genre'] === $g) ? 'selected' : ''; ?>><?php echo htmlspecialchars($g); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="langue">
                    <?php foreach ($langues as $l): ?>
                        <option value="<?php echo htmlspecialchars($l); ?>" <?php echo (isset($_GET['langue']) && $_GET['langue'] === $l) ? 'selected' : ''; ?>><?php echo htmlspecialchars($l); ?></option>
                    <?php endforeach; ?>
                </select>
                <label>
                    <input type="checkbox" name="visite_domicile" value="1" <?php echo (isset($_GET['visite_domicile']) && $_GET['visite_domicile'] === '1') ? 'checked' : ''; ?>> Visite à domicile
                </label>
                <label>
                    <input type="checkbox" name="garde_24h" value="1" <?php echo (isset($_GET['garde_24h']) && $_GET['garde_24h'] === '1') ? 'checked' : ''; ?>> Services de garde 24/7
                </label>
                <button type="submit">OK</button>
                <button type="submit" name="rechercher">RECHERCHER</button>
            </form>
        </section>

        <section class="medecin-section">
            <h2>Médecins</h2>
            <?php if (count($medecins) > 0): ?>
                <?php foreach ($medecins as $medecin): ?>
                    <div class="medecin-card">
                        <h3><?php echo htmlspecialchars($medecin['civilite'] . ' ' . $medecin['prenom'] . ' ' . $medecin['nom']); ?></h3>
                        <p class="specialite"><?php echo htmlspecialchars($medecin['specialite']); ?></p>
                        <p class="adresse"><?php echo htmlspecialchars($medecin['adresse_cabinet'] . ', ' . $medecin['ville'] . ', ' . ($medecin['pays'] ?? 'Non spécifié')); ?></p>
                        <div class="services">
                            <p><strong>Genre :</strong> <?php echo htmlspecialchars($medecin['genre'] ?? 'Non spécifié'); ?></p>
                            <p><strong>Langues parlées :</strong> <?php echo htmlspecialchars($medecin['langue_parlee'] ?? 'Non spécifié'); ?></p>
                            <p><strong>Visite à domicile :</strong> <?php echo $medecin['visite_domicile'] ? 'Oui' : 'Non'; ?></p>
                            <p><strong>Garde 24/7 :</strong> <?php echo $medecin['garde_24h'] ? 'Oui' : 'Non'; ?></p>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($medecin['telephone_cabinet']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($medecin['email']); ?></p>
                        </div>
                        <a href="rendez_vous.php?medecin_id=<?php echo $medecin['id']; ?>" class="btn-rdv">Prendre Rendez-vous</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun médecin trouvé avec ces critères.</p>
            <?php endif; ?>
        </section>

        <footer>
            <h3>MediStatView</h3>
            <p>Votre plateforme de santé connectée pour un suivi médical optimal en toute sécurité.</p>
            <div>
                <h4>Liens Rapides</h4>
                <a href="#">Accueil</a> |
                <a href="#">Nos Services</a> |
                <a href="#">Espaces Personnalisés</a> |
                <a href="#">FAQ</a> |
                <a href="#">Actualités Santé</a> |
                <a href="#">À Propos</a>
            </div>
            <div>
                <h4>Contact</h4>
                <p>📍 123 Avenue de la Santé, 75001 Casa</p>
                <p>📞 +212 5 23 45 67 89</p>
                <p>✉️ <a href="mailto:contact@gmail.com">contact@gmail.com</a></p>
                <p>🕒 Lun - Ven: 9h00 - 18h00</p>
            </div>
            <p>© 2025 MediStatView. Tous droits réservés.</p>
        </footer>
    </div>
</body>
</html>

<?php
$conn = null; 
?>