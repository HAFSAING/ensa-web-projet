<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $pdo = getDatabaseConnection();

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['patient_id'])) {
        throw new Exception('Utilisateur non connecté');
    }

    // Récupérer les données du formulaire
    $patient_id = $_POST['patient_id'] ?? null;
    $appointmentType = $_POST['appointmentType'] ?? '';
    $specialty = $_POST['specialty'] ?? '';
    $doctor = $_POST['doctor'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Valider les champs obligatoires
    if (!$patient_id || !$appointmentType || !$specialty || !$doctor || !$date || !$time) {
        throw new Exception('Tous les champs obligatoires doivent être remplis');
    }

    // Récupérer l'ID de la spécialité
    $stmt_specialty = $pdo->prepare("SELECT id FROM specialites WHERE nom = ?");
    $stmt_specialty->execute([ucfirst($specialty)]);
    $specialty_id = $stmt_specialty->fetchColumn();

    if (!$specialty_id) {
        throw new Exception('Spécialité non trouvée');
    }

    // Récupérer l'ID du médecin
    list($prenom, $nom) = explode(' ', $doctor, 2);
    $nom = str_replace('Dr. ', '', $nom); // Enlever "Dr." du nom
    $stmt_doctor = $pdo->prepare("SELECT id FROM medecins WHERE prenom = ? AND nom = ? AND specialite_id = ?");
    $stmt_doctor->execute([$prenom, $nom, $specialty_id]);
    $medecin_id = $stmt_doctor->fetchColumn();

    if (!$medecin_id) {
        throw new Exception('Médecin non trouvé');
    }

    // Combiner date et heure pour créer date_heure
    $date_heure = $date . ' ' . $time . ':00';
    if (!DateTime::createFromFormat('Y-m-d H:i:s', $date_heure)) {
        throw new Exception('Format de date ou d\'heure invalide');
    }

    // Insérer le rendez-vous dans la table rendez_vous
    $stmt = $pdo->prepare("
        INSERT INTO rendez_vous (patient_id, medecin_id, date_heure, motif, statut, notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'en_attente', ?, NOW(), NOW())
    ");
    $stmt->execute([$patient_id, $medecin_id, $date_heure, $reason, $notes]);

    $response['success'] = true;
    $response['message'] = 'Rendez-vous créé avec succès';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>