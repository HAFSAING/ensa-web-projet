-- Insertion dans la table specialites
INSERT INTO specialites (nom, description) VALUES
('Médecine Générale', 'Soins généraux et suivi médical'),
('Cardiologie', 'Spécialisée dans les maladies du cœur'),
('Pédiatrie', 'Soins aux enfants et nourrissons'),
('Dermatologie', 'Soins de la peau et affections cutanées'),
('Gynécologie', 'Santé féminine et grossesse'),
('Ophtalmologie', 'Problèmes oculaires et chirurgie visuelle');

-- Insertion dans la table villes
INSERT INTO villes (nom, code_postal) VALUES
('Casablanca', '20000'),
('Rabat', '10000'),
('Marrakech', '40000'),
('Fès', '30000'),
('Tanger', '90000'),
('Agadir', '80000');

-- Insertion dans la table medecins
INSERT INTO medecins (civilite, nom, prenom, cin, date_naissance, specialite_id, num_inpe, num_ordre, carte_professionnelle,
                      adresse_cabinet, ville_id, telephone_cabinet, telephone_mobile, email, password, statut)
VALUES
('Dr.', 'El Moussaoui', 'Ahmed', 'AB123456', '1975-04-15', 1, 'INPE123456', 'NO789012', '/cartes/dr_ahmed.pdf',
 'Avenue des FAR, Casablanca', 1, '0522345678', '0612345678', 'dr.ahmed@example.com', 'password123', 'actif'),
('Pr.', 'Bennani', 'Fatima', 'CD789012', '1980-06-20', 2, 'INPE789012', 'NO345678', '/cartes/pr_fatima.pdf',
 'Rue Ibn Sina, Rabat', 2, '0533456789', '0623456789', 'pr.fatima@example.com', 'password123', 'actif'),
('Dr.', 'Chafiq', 'Mohamed', 'EF345678', '1985-02-10', 3, 'INPE345678', 'NO901234', '/cartes/dr_mohamed.pdf',
 'Quartier Hivernage, Marrakech', 3, '0544567890', '0634567890', 'dr.mohamed@example.com', 'password123', 'en_attente'),
('Dr.', 'Lamrani', 'Nadia', 'GH901234', '1990-08-25', 4, 'INPE901234', 'NO567890', '/cartes/dr_nadia.pdf',
 'Avenue Hassan II, Fès', 4, '0535678901', '0645678901', 'dr.nadia@example.com', 'password123', 'suspendu'),
('Pr.', 'Khalidi', 'Said', 'IJ567890', '1978-11-05', 5, 'INPE567890', 'NO123456', '/cartes/pr_said.pdf',
 'Zone Industrielle, Tanger', 5, '0536789012', '0656789012', 'pr.said@example.com', 'password123', 'actif'),
('Dr.', 'Zahiri', 'Hanae', 'KL123456', '1988-03-14', 6, 'INPE123450', 'NO678901', '/cartes/dr_hanae.pdf',
 'Boulevard Mohamed VI, Agadir', 6, '0524567890', '0667890123', 'dr.hanae@example.com', 'password123', 'en_attente');

-- Insertion dans la table patients
INSERT INTO patients (nom, prenom, cin, date_naissance, sexe, email, telephone, adresse, ville_id, mutuelle, password, username)
VALUES
('Ouazzani', 'Karim', 'P1234567', '1992-01-10', 'M', 'karim.ouazzani@example.com', '0611223344', 'Hay Salam, Casablanca', 1, 'cnss', 'password123', 'karim_oua'),
('Larbi', 'Imane', 'P7890123', '1985-05-20', 'F', 'imane.larbi@example.com', '0622334455', 'Ville Nouvelle, Rabat', 2, 'cnops', 'password123', 'imane_lar'),
('Benali', 'Youssef', 'P4567890', '2000-11-30', 'M', 'youssef.benali@example.com', '0633445566', 'Guéliz, Marrakech', 3, 'amo', 'password123', 'youssef_b'),
('El Khalfaoui', 'Nisrine', 'P9012345', '1995-08-15', 'F', 'nisrine.elk@example.com', '0644556677', 'Hay Fès, Fès', 4, 'ramed', 'password123', 'nisrine_e'),
('Ait Brahim', 'Reda', 'P6789012', '1983-04-22', 'M', 'reda.aitb@example.com', '0655667788', 'Villa Al Irfane, Tanger', 5, 'autre', 'password123', 'reda_a'),
('Saadi', 'Souad', 'P2345678', '1976-12-05', 'F', 'souad.saadi@example.com', '0666778899', 'Quartier Aïn Chqarf, Agadir', 6, 'aucune', 'password123', 'souad_s');

-- Insertion dans la table consultations
INSERT INTO consultations (patient_id, medecin_id, date_consultation, motif, symptomes, diagnostic, traitement, notes)
VALUES
(1, 1, '2024-10-01 09:00:00', 'Douleurs abdominales', 'Nausée, vomissements', 'Gastro-entérite aiguë', 'Antibiotique + repos', 'Aucun antécédent'),
(2, 2, '2024-10-02 11:00:00', 'Palpitations cardiaques', 'Essoufflement, fatigue', 'Tachycardie sinusale', 'Beta-bloquant', 'Suivi recommandé'),
(3, 3, '2024-10-03 14:00:00', 'Fièvre et toux', 'Mal de gorge, frissons', 'Infection virale', 'Antipyrétique', 'Hydratation nécessaire'),
(4, 4, '2024-10-04 10:30:00', 'Éruption cutanée', 'Démangeaisons', 'Allergie saisonnière', 'Crème corticoidale', 'Pas de contact allergène connu'),
(5, 5, '2024-10-05 15:00:00', 'Douleur pelvienne', 'Perte de sang anormale', 'Kyste ovarien', 'Surveillance échographique', 'Planification d’un bilan'),
(6, 6, '2024-10-06 16:00:00', 'Vision trouble', 'Céphalées', 'Presbytie', 'Correction optique', 'Examen ophtalmologique');

-- Insertion dans la table analyses
INSERT INTO analyses (consultation_id, type_analyse, resultat, fichier_resultat, date_analyse, date_resultat)
VALUES
(1, 'Hémogramme', 'Normale', '/analyses/analyse1.pdf', '2024-10-01', '2024-10-02'),
(2, 'Électrocardiogramme', 'Tachycardie confirmée', '/analyses/analyse2.pdf', '2024-10-02', '2024-10-03'),
(3, 'PCR', 'Infection virale', '/analyses/analyse3.pdf', '2024-10-03', '2024-10-04'),
(4, 'Patch test', 'Réaction allergique positive', '/analyses/analyse4.pdf', '2024-10-04', '2024-10-05'),
(5, 'Échographie pelvienne', 'Kyste de 3 cm', '/analyses/analyse5.pdf', '2024-10-05', '2024-10-06'),
(6, 'Acuité visuelle', 'Vision réduite à 0.5', '/analyses/analyse6.pdf', '2024-10-06', '2024-10-07');

-- Insertion dans la table prescriptions
INSERT INTO prescriptions (consultation_id, medicament, posologie, duree, notes)
VALUES
(1, 'Paracétamol', '1g x3/jour', '5 jours', 'À prendre après les repas'),
(2, 'Aténolol', '50mg x1/jour', '10 jours', 'À surveiller TA'),
(3, 'Ibuprofène', '400mg x2/jour', '3 jours', 'En cas de fièvre persistante'),
(4, 'Dérmovate', 'Appliquer localement x2/jour', '7 jours', 'Éviter exposition solaire'),
(5, 'Aucun', '-', '-', 'Surveillance uniquement'),
(6, 'Lentilles correctrices', '-', '-', 'Corrections permanentes');

-- Insertion dans la table statistiques
INSERT INTO statistiques (specialite_id, pathologie, nombre_cas, tranche_age, sexe, ville_id, date_debut, date_fin)
VALUES
(1, 'Hypertension', 15, '40-60', 'Tous', 1, '2024-09-01', '2024-09-30'),
(2, 'Infarctus du myocarde', 5, '50-70', 'M', 2, '2024-09-01', '2024-09-30'),
(3, 'Otite moyenne', 20, '0-10', 'Tous', 3, '2024-09-01', '2024-09-30'),
(4, 'Psoriasis', 8, '20-40', 'F', 4, '2024-09-01', '2024-09-30'),
(5, 'Endométriose', 12, '25-40', 'F', 5, '2024-09-01', '2024-09-30'),
(6, 'Cataracte', 7, '60-80', 'Tous', 6, '2024-09-01', '2024-09-30');

-- Insertion dans la table rendez_vous
INSERT INTO rendez_vous (patient_id, medecin_id, date_heure, motif, statut)
VALUES
(1, 1, '2024-10-07 10:00:00', 'Consultation générale', 'confirme'),
(2, 2, '2024-10-08 11:00:00', 'Suivi cardiaque', 'termine'),
(3, 3, '2024-10-09 09:30:00', 'Fièvre persistante', 'en_attente'),
(4, 4, '2024-10-10 14:00:00', 'Éruption cutanée', 'annule'),
(5, 5, '2024-10-11 15:00:00', 'Douleur pelvienne', 'confirme'),
(6, 6, '2024-10-12 16:00:00', 'Vision trouble', 'en_attente');

-- Insertion dans la table documents_medicaux
INSERT INTO documents_medicaux (patient_id, medecin_id, type_document, titre, chemin_fichier, date_document)
VALUES
(1, 1, 'ordonnance', 'Ordonnance Octobre 2024', '/documents/doc1.pdf', '2024-10-01'),
(2, 2, 'analyse', 'ECG - Palpitations', '/documents/doc2.pdf', '2024-10-02'),
(3, 3, 'radiologie', 'Scanner abdominal', '/documents/doc3.pdf', '2024-10-03'),
(4, 4, 'analyse', 'Analyse sanguine', '/documents/doc4.pdf', '2024-10-04'),
(5, 5, 'radiologie', 'Échographie pelvienne', '/documents/doc5.pdf', '2024-10-05'),
(6, 6, 'autre', 'Certificat médical', '/documents/doc6.pdf', '2024-10-06');

-- Insertion dans la table notifications
INSERT INTO notifications (utilisateur_id, type_utilisateur, titre, message, lien, lue)
VALUES
(1, 'patient', 'Nouveau rendez-vous', 'Votre rendez-vous avec Dr. Ahmed est confirmé.', '/rendezvous/1', FALSE),
(2, 'medecin', 'Nouvelle consultation', 'La consultation n°2 a été ajoutée.', '/consultations/2', TRUE),
(3, 'patient', 'Rappel de prise', 'Prendre Paracétamol aujourd’hui.', NULL, FALSE),
(4, 'medecin', 'Nouvelle analyse', 'Analyse disponible pour Karim Ouazzani.', '/analyses/1', TRUE),
(5, 'patient', 'Article publié', 'Le Dr. Fatima a publié un nouvel article.', '/articles/1', FALSE),
(6, 'medecin', 'Message reçu', 'Un nouveau message patient est en attente.', '/messages', TRUE);

-- Insertion dans la table logs_acces
INSERT INTO logs_acces (utilisateur_id, type_utilisateur, action, ip_address, user_agent)
VALUES
(1, 'patient', 'Connexion', '192.168.1.1', 'Chrome/Mac'),
(2, 'medecin', 'Connexion', '192.168.1.2', 'Firefox/Windows'),
(3, 'patient', 'Consultation dossier', '192.168.1.3', 'Safari/iOS'),
(4, 'medecin', 'Impression ordonnance', '192.168.1.4', 'Edge/Windows'),
(5, 'patient', 'Téléchargement document', '192.168.1.5', 'Opera/Linux'),
(6, 'medecin', 'Ajout article', '192.168.1.6', 'Brave/Linux');

-- Insertion dans la table categories_articles
INSERT INTO categories_articles (nom, description, slug)
VALUES
('Santé cardiovasculaire', 'Articles sur le coeur et les vaisseaux', 'sante-cardiovasculaire'),
('Bien-être mental', 'Psychologie et santé mentale', 'bien-etre-mental'),
('Nutrition', 'Alimentation et bien-être', 'nutrition'),
('Grossesse', 'Conseils et accompagnement', 'grossesse'),
('Soins de la peau', 'Dermatologie et soins quotidiens', 'soins-peau'),
('Ophtalmologie', 'Santé visuelle', 'ophtalmologie');

-- Insertion dans la table articles
INSERT INTO articles (medecin_id, categorie_id, titre, slug, contenu, image_principale, resume, statut, date_publication)
VALUES
(1, 1, 'Comment prévenir l’hypertension ?', 'comment-prevenir-hypertension', 'Contenu détaillé...', '/images/art1.jpg', 'Guide pratique', 'publie', '2024-10-01'),
(2, 2, 'Stress et santé mentale', 'stress-sante-mentale', 'Contenu détaillé...', '/images/art2.jpg', 'Comprendre le stress', 'publie', '2024-10-02'),
(3, 3, 'Alimentation équilibrée', 'alimentation-equilibree', 'Contenu détaillé...', '/images/art3.jpg', 'Recommandations alimentaires', 'brouillon', NULL),
(4, 4, 'Premiers mois de grossesse', 'premiers-mois-grossesse', 'Contenu détaillé...', '/images/art4.jpg', 'Ce qu’il faut savoir', 'archive', '2024-09-01'),
(5, 5, 'Routine soin quotidien', 'routine-soin-quoditien', 'Contenu détaillé...', '/images/art5.jpg', 'Routine beauté simple', 'publie', '2024-10-03'),
(6, 6, 'Protection des yeux', 'protection-des-yeux', 'Contenu détaillé...', '/images/art6.jpg', 'Protéger sa vision', 'publie', '2024-10-04');

-- Insertion dans la table tags
INSERT INTO tags (nom, slug)
VALUES
('Santé', 'sante'),
('Conseil', 'conseil'),
('Prévention', 'prevention'),
('Alimentation', 'alimentation'),
('Bien-être', 'bien-etre'),
('Médecin', 'medecin');

-- Insertion dans la table articles_tags
INSERT INTO articles_tags (article_id, tag_id)
VALUES
(1, 1), (1, 3), (2, 1), (2, 5), (3, 4), (4, 1), (4, 2), (5, 1), (5, 5), (6, 1), (6, 2);