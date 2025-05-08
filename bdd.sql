-- Création de la base de données
CREATE DATABASE IF NOT EXISTS medistatview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE medistatview;

-- Table des spécialités médicales
CREATE TABLE specialites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des villes
CREATE TABLE villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    code_postal VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des médecins
CREATE TABLE medecins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    civilite ENUM('Dr.', 'Pr.') NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    cin VARCHAR(20) NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    specialite_id INT NOT NULL,
    num_inpe VARCHAR(50) NOT NULL UNIQUE COMMENT 'Identifiant National des Professionnels de la Santé',
    num_ordre VARCHAR(50) NOT NULL UNIQUE COMMENT 'Numéro d\'inscription à l\'Ordre National des Médecins',
    carte_professionnelle VARCHAR(255) COMMENT 'Chemin vers le fichier uploadé',
    adresse_cabinet TEXT NOT NULL,
    ville_id INT NOT NULL,
    telephone_cabinet VARCHAR(20) NOT NULL,
    telephone_mobile VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    statut ENUM('actif', 'en_attente', 'suspendu') DEFAULT 'en_attente',
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (specialite_id) REFERENCES specialites(id),
    FOREIGN KEY (ville_id) REFERENCES villes(id)
);

-- Table des patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    cin VARCHAR(20) NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    sexe ENUM('M', 'F') NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telephone VARCHAR(20) NOT NULL,
    adresse TEXT,
    ville_id INT,
    mutuelle ENUM('cnops', 'cnss', 'ramed', 'amo', 'autre', 'aucune'),
    password VARCHAR(255) NOT NULL,
    security_question VARCHAR(255),
    security_answer VARCHAR(255),
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    notifications BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES villes(id)
);

-- Table des consultations
CREATE TABLE consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT NOT NULL,
    date_consultation DATETIME NOT NULL,
    motif TEXT NOT NULL,
    symptomes TEXT,
    diagnostic TEXT,
    traitement TEXT,
    notes TEXT,
    statut ENUM('planifiee', 'terminee', 'annulee') DEFAULT 'planifiee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (medecin_id) REFERENCES medecins(id)
);

-- Table des analyses médicales
CREATE TABLE analyses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    type_analyse VARCHAR(100) NOT NULL,
    resultat TEXT,
    fichier_resultat VARCHAR(255),
    date_analyse DATE NOT NULL,
    date_resultat DATE,
    statut ENUM('en_attente', 'complete', 'annulee') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id)
);

-- Table des prescriptions
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    medicament TEXT NOT NULL,
    posologie TEXT NOT NULL,
    duree VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id)
);

-- Table des statistiques épidémiologiques
CREATE TABLE statistiques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    specialite_id INT,
    pathologie VARCHAR(100) NOT NULL,
    nombre_cas INT NOT NULL,
    tranche_age VARCHAR(50),
    sexe ENUM('M', 'F', 'Tous'),
    ville_id INT,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (specialite_id) REFERENCES specialites(id),
    FOREIGN KEY (ville_id) REFERENCES villes(id)
);

-- Table des rendez-vous
CREATE TABLE rendez_vous (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT NOT NULL,
    date_heure DATETIME NOT NULL,
    motif TEXT NOT NULL,
    statut ENUM('confirme', 'annule', 'en_attente', 'termine') DEFAULT 'en_attente',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (medecin_id) REFERENCES medecins(id)
);

-- Table des documents médicaux
CREATE TABLE documents_medicaux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT,
    type_document ENUM('ordonnance', 'analyse', 'radiologie', 'autre') NOT NULL,
    titre VARCHAR(100) NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL,
    date_document DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (medecin_id) REFERENCES medecins(id)
);

-- Table des notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_utilisateur ENUM('medecin', 'patient') NOT NULL,
    titre VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    lien VARCHAR(255),
    lue BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des logs d'accès
CREATE TABLE logs_acces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_utilisateur ENUM('medecin', 'patient') NOT NULL,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE patients 
    ADD COLUMN statut ENUM('actif', 'en_attente', 'suspendu') DEFAULT 'en_attente' AFTER last_login_at;