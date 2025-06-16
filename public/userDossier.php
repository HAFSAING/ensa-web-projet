<?php
// Démarrage de la session
session_start();

// Start output buffering to capture any accidental output
ob_start();

// Inclure la connexion à la base de données
require_once __DIR__ . '/../config/database.php';
// Inclure l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Obtenir la connexion PDO
$pdo = getDatabaseConnection();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['patient_id'])) {
    header("Location: userConnecter.php");
    exit();
}

// Gérer le téléchargement du PDF en premier
if (isset($_GET['download_pdf'])) {
    // Clear the output buffer
    ob_end_clean();

    // Récupérer les données du patient connecté
    $patient_id = $_SESSION['patient_id'];
    $stmt = $pdo->prepare("
        SELECT p.nom, p.prenom, p.email, p.date_naissance, p.telephone, p.adresse, p.sexe, p.mutuelle, v.nom AS ville_nom
        FROM patients p
        LEFT JOIN villes v ON p.ville_id = v.id
        WHERE p.id = ?
    ");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        header("Location: userConnecter.php");
        exit();
    }

    // Récupérer l'historique des consultations
    $stmt_consultations = $pdo->prepare("
        SELECT c.date_consultation, c.diagnostic, c.notes, m.nom AS medecin_nom, m.prenom AS medecin_prenom
        FROM consultations c
        JOIN medecins m ON c.medecin_id = m.id
        WHERE c.patient_id = ? AND c.statut = 'terminee'
        ORDER BY c.date_consultation DESC
        LIMIT 5
    ");
    $stmt_consultations->execute([$patient_id]);
    $consultations = $stmt_consultations->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les prescriptions
    $stmt_prescriptions = $pdo->prepare("
        SELECT p.medicament, p.posologie, p.duree, c.date_consultation
        FROM prescriptions p
        JOIN consultations c ON p.consultation_id = c.id
        WHERE c.patient_id = ? AND c.statut = 'terminee'
        ORDER BY c.date_consultation DESC
        LIMIT 5
    ");
    $stmt_prescriptions->execute([$patient_id]);
    $prescriptions = $stmt_prescriptions->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les analyses médicales
    $stmt_analyses = $pdo->prepare("
        SELECT a.type_analyse, a.date_analyse, a.resultat, m.nom AS medecin_nom, m.prenom AS medecin_prenom
        FROM analyses a
        JOIN consultations c ON a.consultation_id = c.id
        JOIN medecins m ON c.medecin_id = m.id
        WHERE c.patient_id = ? AND a.statut = 'complete'
        ORDER BY a.date_analyse DESC
        LIMIT 5
    ");
    $stmt_analyses->execute([$patient_id]);
    $analyses = $stmt_analyses->fetchAll(PDO::FETCH_ASSOC);

    // Créer une instance de TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Définir les métadonnées du document
    $pdf->SetCreator('MediStatView');
    $pdf->SetAuthor('MediStatView Services');
    $pdf->SetTitle('Dossier Médical - ' . $patient['prenom'] . ' ' . $patient['nom']);
    $pdf->SetSubject('Dossier Médical Numérique');
    $pdf->SetKeywords('MediStatView, Dossier Médical, Patient, Santé');

    // Désactiver l'en-tête et le pied de page par défaut
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Définir les marges
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 25);

    // Ajouter une page
    $pdf->AddPage();

    // Définir les couleurs personnalisées
    $primaryColor = [29, 86, 107];     // Bleu principal
    $accentColor = [123, 186, 154];    // Vert accent
    $lightBlue = [134, 179, 195];      // Bleu clair
    $grayLight = [248, 249, 250];      // Gris très clair
    $textDark = [51, 51, 51];          // Texte sombre
    $textGray = [108, 117, 125];       // Texte gris

    // === EN-TÊTE AMÉLIORÉ ===
    $y_start = $pdf->GetY();

    // Arrière-plan dégradé simulé avec des rectangles
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Rect(0, 0, 210, 50, 'F');

    // Rectangle accent pour le style
    $pdf->SetFillColor($lightBlue[0], $lightBlue[1], $lightBlue[2]);
    $pdf->Rect(0, 45, 210, 5, 'F');

    // Logo stylisé (replicating the SVG from userDashboard.php)
    // Rectangle (equivalent to <rect x="10" y="15" width="20" height="20" fill="#76b5c5" />)
    $pdf->SetFillColor(118, 181, 197); // #76b5c5
    $pdf->Rect(20, 15, 20, 20, 'F');

    // Polygon (equivalent to <polygon points="30,15 40,25 30,35" fill="#a7c5d1" />)
    $pdf->SetFillColor(167, 197, 209); // #a7c5d1
    $pdf->PolyLine([40, 15, 50, 25, 40, 35, 40, 15], 'F');

    // Text "MediStatView" (equivalent to <text x="50" y="25" fill="#ffffff" ...>MediStatView</text>)
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetXY(60, 12);
    $pdf->Cell(0, 10, 'MediStatView', 0, 1, 'L');

    // Text "SERVICES" (equivalent to <text x="50" y="40" fill="#a7c5d1" ...>SERVICES</text>)
    $pdf->SetTextColor(167, 197, 209); // #a7c5d1
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetXY(60, 27);
    $pdf->Cell(0, 10, 'SERVICES', 0, 1, 'L');

    // Informations de génération
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(200, 200, 200);
    $pdf->SetXY(20, 40);
    $pdf->Cell(0, 5, 'Généré le ' . date('d/m/Y à H:i') . ' - Document confidentiel', 0, 1, 'L');

    // ID Patient en haut à droite
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY(150, 15);
    $pdf->Cell(0, 5, 'ID Patient: #' . str_pad($patient_id, 6, '0', STR_PAD_LEFT), 0, 1, 'R');

    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetXY(150, 22);
    $pdf->Cell(0, 5, 'Strictement confidentiel', 0, 1, 'R');

    $pdf->SetY(60); // Adjust to leave space after header

    // === TITRE DU DOSSIER ===
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->Cell(0, 12, 'DOSSIER MÉDICAL PERSONNEL', 0, 1, 'C');
    $pdf->Ln(5);

    // Ligne décorative
    $pdf->SetDrawColor($accentColor[0], $accentColor[1], $accentColor[2]);
    $pdf->SetLineWidth(1);
    $pdf->Line(70, $pdf->GetY(), 140, $pdf->GetY());
    $pdf->Ln(10);

    // === FONCTION POUR CRÉER DES SECTIONS STYLISÉES ===
    function createSectionHeader($pdf, $title, $icon, $colors) {
        $y = $pdf->GetY();
        
        // Arrière-plan de la section
        $pdf->SetFillColor($colors['accent'][0], $colors['accent'][1], $colors['accent'][2]);
        $pdf->RoundedRect(15, $y, 180, 12, 2, '1111', 'F');
        
        // Icône stylisée (simulée avec du texte)
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(20, $y + 3);
        $pdf->Cell(8, 6, $icon, 0, 0, 'C');
        
        // Titre de section
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetXY(32, $y + 2);
        $pdf->Cell(0, 8, $title, 0, 1, 'L');
        
        $pdf->Ln(8);
    }

    function createInfoBox($pdf, $label, $value, $colors) {
        $y = $pdf->GetY();
        
        // Fond alternant pour la lisibilité
        static $alternate = false;
        if ($alternate) {
            $pdf->SetFillColor($colors['lightBg'][0], $colors['lightBg'][1], $colors['lightBg'][2]);
            $pdf->Rect(15, $y, 180, 8, 'F');
        }
        $alternate = !$alternate;
        
        // Label
        $pdf->SetTextColor($colors['primary'][0], $colors['primary'][1], $colors['primary'][2]);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(20, $y + 1);
        $pdf->Cell(50, 6, $label . ':', 0, 0, 'L');
        
        // Valeur
        $pdf->SetTextColor($colors['textDark'][0], $colors['textDark'][1], $colors['textDark'][2]);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(75, $y + 1);
        $pdf->Cell(0, 6, $value, 0, 1, 'L');
        
        $pdf->Ln(2);
    }

    // Définition des couleurs pour les fonctions
    $colors = [
        'primary' => $primaryColor,
        'accent' => $accentColor,
        'lightBg' => $grayLight,
        'textDark' => $textDark,
        'textGray' => $textGray
    ];

    // === SECTION INFORMATIONS PERSONNELLES ===
    createSectionHeader($pdf, 'INFORMATIONS PERSONNELLES', '👤', $colors);
    
    createInfoBox($pdf, 'Nom complet', $patient['prenom'] . ' ' . $patient['nom'], $colors);
    createInfoBox($pdf, 'Date de naissance', date('d/m/Y', strtotime($patient['date_naissance'])), $colors);
    createInfoBox($pdf, 'Sexe', ($patient['sexe'] === 'M' ? 'Masculin' : 'Féminin'), $colors);
    createInfoBox($pdf, 'Email', $patient['email'], $colors);
    createInfoBox($pdf, 'Téléphone', $patient['telephone'], $colors);
    createInfoBox($pdf, 'Adresse', $patient['adresse'] ?? 'Non spécifiée', $colors);
    createInfoBox($pdf, 'Ville', $patient['ville_nom'] ?? 'Non spécifiée', $colors);
    createInfoBox($pdf, 'Mutuelle', $patient['mutuelle'] ?? 'Aucune', $colors);
    
    $pdf->Ln(10);

    // === SECTION CONSULTATIONS ===
    createSectionHeader($pdf, 'HISTORIQUE DES CONSULTATIONS', '🩺', $colors);
    
    if (empty($consultations)) {
        $pdf->SetTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetFont('helvetica', 'I', 11);
        $pdf->Cell(0, 8, 'Aucune consultation terminée enregistrée.', 0, 1, 'C');
    } else {
        foreach ($consultations as $index => $c) {
            $y = $pdf->GetY();
            
            // Boîte pour chaque consultation
            $pdf->SetFillColor(250, 250, 250);
            $pdf->RoundedRect(18, $y, 174, 20, 1, '1111', 'F');
            
            // Numéro de consultation
            $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            $pdf->Circle(25, $y + 10, 4, 0, 360, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetXY(22, $y + 7);
            $pdf->Cell(6, 6, ($index + 1), 0, 0, 'C');
            
            // Informations de consultation
            $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetXY(35, $y + 3);
            $pdf->Cell(0, 6, 'Dr. ' . $c['medecin_prenom'] . ' ' . $c['medecin_nom'], 0, 1, 'L');
            
            $pdf->SetTextColor($textGray[0], $textGray[1], $textGray[2]);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(35, $y + 9);
            $pdf->Cell(50, 5, 'Date: ' . date('d/m/Y', strtotime($c['date_consultation'])), 0, 0, 'L');
            
            $pdf->SetXY(35, $y + 14);
            $pdf->Cell(0, 5, 'Diagnostic: ' . (strlen($c['diagnostic']) > 60 ? substr($c['diagnostic'], 0, 60) . '...' : $c['diagnostic']), 0, 1, 'L');
            
            $pdf->Ln(8);
        }
    }
    
    $pdf->Ln(5);

    // === SECTION PRESCRIPTIONS ===
    createSectionHeader($pdf, 'PRESCRIPTIONS MÉDICALES', '💊', $colors);
    
    if (empty($prescriptions)) {
        $pdf->SetTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetFont('helvetica', 'I', 11);
        $pdf->Cell(0, 8, 'Aucune prescription enregistrée.', 0, 1, 'C');
    } else {
        foreach ($prescriptions as $index => $p) {
            $y = $pdf->GetY();
            
            // Boîte pour chaque prescription
            $pdf->SetFillColor(252, 248, 248);
            $pdf->RoundedRect(18, $y, 174, 18, 1, '1111', 'F');
            
            // Icône médicament
            $pdf->SetFillColor(220, 53, 69);
            $pdf->RoundedRect(22, $y + 6, 6, 6, 1, '1111', 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetXY(23, $y + 7);
            $pdf->Cell(4, 4, 'Rx', 0, 0, 'C');
            
            // Informations prescription
            $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetXY(33, $y + 3);
            $pdf->Cell(0, 6, $p['medicament'], 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(33, $y + 9);
            $pdf->Cell(80, 5, 'Posologie: ' . $p['posologie'], 0, 0, 'L');
            $pdf->SetXY(33, $y + 13);
            $pdf->Cell(80, 5, 'Durée: ' . $p['duree'], 0, 0, 'L');
            
            $pdf->SetXY(120, $y + 9);
            $pdf->Cell(0, 5, 'Prescrit le: ' . date('d/m/Y', strtotime($p['date_consultation'])), 0, 1, 'L');
            
            $pdf->Ln(6);
        }
    }
    
    $pdf->Ln(5);

    // === SECTION ANALYSES ===
    createSectionHeader($pdf, 'ANALYSES MÉDICALES', '🔬', $colors);
    
    if (empty($analyses)) {
        $pdf->SetTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetFont('helvetica', 'I', 11);
        $pdf->Cell(0, 8, 'Aucune analyse médicale enregistrée.', 0, 1, 'C');
    } else {
        foreach ($analyses as $index => $a) {
            $y = $pdf->GetY();
            
            // Boîte pour chaque analyse
            $pdf->SetFillColor(248, 252, 248);
            $pdf->RoundedRect(18, $y, 174, 18, 1, '1111', 'F');
            
            // Icône analyse
            $pdf->SetFillColor(40, 167, 69);
            $pdf->Circle(25, $y + 9, 4, 0, 360, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetXY(22, $y + 6);
            $pdf->Cell(6, 6, '⚗', 0, 0, 'C');
            
            // Informations analyse
            $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetXY(33, $y + 3);
            $pdf->Cell(0, 6, $a['type_analyse'], 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(33, $y + 9);
            $pdf->Cell(80, 5, 'Date: ' . date('d/m/Y', strtotime($a['date_analyse'])), 0, 0, 'L');
            $pdf->SetXY(33, $y + 13);
            $pdf->Cell(80, 5, 'Médecin: Dr. ' . $a['medecin_prenom'] . ' ' . $a['medecin_nom'], 0, 0, 'L');
            
            $pdf->SetXY(120, $y + 9);
            $pdf->Cell(0, 5, 'Résultat: ' . ($a['resultat'] ?? 'En cours'), 0, 1, 'L');
            
            $pdf->Ln(6);
        }
    }

    // === PIED DE PAGE PERSONNALISÉ ===
    $pdf->SetY(-20);
    
    // Ligne décorative
    $pdf->SetDrawColor($accentColor[0], $accentColor[1], $accentColor[2]);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    
    $pdf->Ln(3);
    
    // Informations du pied de page
    $pdf->SetTextColor($textGray[0], $textGray[1], $textGray[2]);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(60, 5, 'MediStatView Services', 0, 0, 'L');
    $pdf->Cell(60, 5, 'Document confidentiel', 0, 0, 'C');
    $pdf->Cell(60, 5, 'Page ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 0, 'R');

    // Générer le nom du fichier avec timestamp
    $timestamp = date('Ymd_His');
    $filename = 'DossierMedical_' . str_replace(' ', '', $patient['prenom'] . $patient['nom']) . '_' . $timestamp . '.pdf';

    // Envoyer le PDF au navigateur pour téléchargement
    $pdf->Output($filename, 'D');
    exit();
}

// Récupérer les données du patient connecté (pour l'affichage HTML)
$patient_id = $_SESSION['patient_id'];
$stmt = $pdo->prepare("
    SELECT p.nom, p.prenom, p.email, p.date_naissance, p.telephone, p.adresse, p.sexe, p.mutuelle, v.nom AS ville_nom
    FROM patients p
    LEFT JOIN villes v ON p.ville_id = v.id
    WHERE p.id = ?
");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header("Location: userConnecter.php");
    exit();
}

// Récupérer l'historique des consultations
$stmt_consultations = $pdo->prepare("
    SELECT c.date_consultation, c.diagnostic, c.notes, m.nom AS medecin_nom, m.prenom AS medecin_prenom
    FROM consultations c
    JOIN medecins m ON c.medecin_id = m.id
    WHERE c.patient_id = ? AND c.statut = 'terminee'
    ORDER BY c.date_consultation DESC
    LIMIT 5
");
$stmt_consultations->execute([$patient_id]);
$consultations = $stmt_consultations->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les prescriptions
$stmt_prescriptions = $pdo->prepare("
    SELECT p.medicament, p.posologie, p.duree, c.date_consultation
    FROM prescriptions p
    JOIN consultations c ON p.consultation_id = c.id
    WHERE c.patient_id = ? AND c.statut = 'terminee'
    ORDER BY c.date_consultation DESC
    LIMIT 5
");
$stmt_prescriptions->execute([$patient_id]);
$prescriptions = $stmt_prescriptions->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les analyses médicales
$stmt_analyses = $pdo->prepare("
    SELECT a.type_analyse, a.date_analyse, a.resultat, m.nom AS medecin_nom, m.prenom AS medecin_prenom
    FROM analyses a
    JOIN consultations c ON a.consultation_id = c.id
    JOIN medecins m ON c.medecin_id = m.id
    WHERE c.patient_id = ? AND a.statut = 'complete'
    ORDER BY a.date_analyse DESC
    LIMIT 5
");
$stmt_analyses->execute([$patient_id]);
$analyses = $stmt_analyses->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Dossier - MediStatView</title>
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

        .dashboard {
            flex: 1;
            display: flex;
            flex-direction: column;
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

        .dashboard-greeting {
            font-size: 1rem;
            color: #666;
        }

        .dashboard-date {
            font-weight: 500;
            color: var(--secondary-color);
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

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dash-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(123, 186, 154, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color1);
            font-size: 1.2rem;
        }

        .meds-icon {
            background-color: rgba(204, 0, 0, 0.1);
            color: var(--accent-color3);
        }

        .card-content {
            margin-bottom: 1rem;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .card-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.3s;
        }

        .card-footer a:hover {
            color: var(--secondary-color);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            width: 30%;
        }

        .info-value {
            color: var(--text-dark);
            width: 70%;
        }

        .history-item, .prescription-item, .analysis-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .history-item:last-child, .prescription-item:last-child, .analysis-item:last-child {
            border-bottom: none;
        }

        .item-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
        }

        .item-details {
            color: #666;
            font-size: 0.9rem;
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

            .dashboard {
                padding: 1rem;
            }

            .dashboard-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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

            .info-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-label, .info-value {
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

            .dashboard-grid {
                grid-template-columns: 1fr;
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
                        <li class="nav-item"><a href="userDossier.php" class="nav-link active"><i class="fas fa-folder-open"></i> Mon dossier</a></li>
                        <li class="nav-item"><a href="userRendezVous.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Rendez-vous</a></li>
                        <li class="nav-item"><a href="userMessage.php" class="nav-link"><i class="fas fa-envelope"></i> Messages</a></li>
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
            <div class="page-header">
                <div>
                    <h1 class="page-title">Mon Dossier Médical</h1>
                    <p class="dashboard-greeting">Bonjour <span class="dashboard-date"><?= htmlspecialchars($patient['prenom']) ?>, le <?= date('d M Y, H:i') ?></span></p>
                </div>
                <div class="action-buttons">
                    <a href="?download_pdf=1" class="btn btn-primary"><i class="fas fa-download"></i> Télécharger mon dossier</a>
                    <a href="#" class="btn btn-outline">Partager avec un médecin</a>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Informations Personnelles</h2>
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="info-row">
                            <div class="info-label">Nom</div>
                            <div class="info-value"><?= htmlspecialchars($patient['nom']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Prénom</div>
                            <div class="info-value"><?= htmlspecialchars($patient['prenom']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Date de naissance</div>
                            <div class="info-value"><?= htmlspecialchars($patient['date_naissance']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Sexe</div>
                            <div class="info-value"><?= $patient['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($patient['email']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value"><?= htmlspecialchars($patient['telephone']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Adresse</div>
                            <div class="info-value"><?= htmlspecialchars($patient['adresse'] ?? 'Non spécifiée') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Ville</div>
                            <div class="info-value"><?= htmlspecialchars($patient['ville_nom'] ?? 'Non spécifiée') ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Mutuelle</div>
                            <div class="info-value"><?= htmlspecialchars($patient['mutuelle'] ?? 'Aucune') ?></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#">Modifier mes informations</a>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Historique des Consultations</h2>
                        <div class="card-icon">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php if (empty($consultations)): ?>
                            <p class="item-details">Aucune consultation terminée pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($consultations as $c): ?>
                                <div class="history-item">
                                    <div class="item-title"><?= htmlspecialchars($c['medecin_prenom'] . ' ' . $c['medecin_nom']) ?> - <?= date('d M Y', strtotime($c['date_consultation'])) ?></div>
                                    <div class="item-details">
                                        <p><strong>Diagnostic:</strong> <?= htmlspecialchars($c['diagnostic'] ?? 'Non spécifié') ?></p>
                                        <p><strong>Notes:</strong> <?= htmlspecialchars($c['notes'] ?? 'Aucune note') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="#">Voir tout l'historique</a>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Prescriptions</h2>
                        <div class="card-icon meds-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php if (empty($prescriptions)): ?>
                            <p class="item-details">Aucune prescription pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($prescriptions as $p): ?>
                                <div class="prescription-item">
                                    <div class="item-title"><?= htmlspecialchars($p['medicament']) ?> - <?= date('d M Y', strtotime($p['date_consultation'])) ?></div>
                                    <div class="item-details">
                                        <p><strong>Posologie:</strong> <?= htmlspecialchars($p['posologie']) ?></p>
                                        <p><strong>Durée:</strong> <?= htmlspecialchars($p['duree']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="#">Voir toutes les prescriptions</a>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="card-header">
                        <h2 class="card-title">Analyses Médicales</h2>
                        <div class="card-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php if (empty($analyses)): ?>
                            <p class="item-details">Aucune analyse médicale pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($analyses as $a): ?>
                                <div class="analysis-item">
                                    <div class="item-title"><?= htmlspecialchars($a['type_analyse']) ?> - <?= date('d M Y', strtotime($a['date_analyse'])) ?></div>
                                    <div class="item-details">
                                        <p><strong>Résultats:</strong> <?= htmlspecialchars($a['resultat'] ?? 'En attente') ?></p>
                                        <p><strong>Médecin:</strong> <?= htmlspecialchars($a['medecin_prenom'] . ' ' . $a['medecin_nom']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="#">Voir toutes les analyses</a>
                    </div>
                </div>
            </div>
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
</body>
</html>