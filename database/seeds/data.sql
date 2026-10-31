 -- Données de test pour le système de gestion des stages
USE gestion_stages;

-- Insertion de responsables
INSERT INTO responsables (nom, prenom, email, password, service, telephone, nombre_stagiaires_actuel, statut) VALUES
('Alami', 'Hassan', 'hassan.alami@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Informatique', '0612345678', 15, 'disponible'),
('Benjelloun', 'Fatima', 'fatima.benjelloun@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ressources Humaines', '0623456789', 8, 'disponible'),
('Tazi', 'Mohammed', 'mohammed.tazi@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Finances', '0634567890', 20, 'occupe'),
('Rachidi', 'Amina', 'amina.rachidi@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Communication', '0645678901', 12, 'disponible'),
('El Fassi', 'Karim', 'karim.elfassi@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Urbanisme', '0656789012', 18, 'disponible');

-- Insertion de stagiaires avec différents statuts
INSERT INTO stagiaires (numero_stagiaire, nom_fr, nom_ar, prenom_fr, prenom_ar, cin, etablissement, email, telephone, date_debut_souhaitee, date_fin_souhaitee, service_demande, statut, responsable_id, date_validation) VALUES
-- Stagiaires en attente
('STG001', 'Benali', 'بنعلي', 'Fatima', 'فاطمة', 'AB123456', 'Faculté des Sciences', 'fatima.benali@gmail.com', '0612345678', '2025-03-01', '2025-03-31', 'Informatique', 'en_attente', NULL, NULL),
('STG002', 'Tazi', 'تازي', 'Ahmed', 'أحمد', 'CD789012', 'EST', 'ahmed.tazi@gmail.com', '0623456789', '2025-03-01', '2025-03-31', 'Informatique', 'en_attente', NULL, NULL),
('STG003', 'Alami', 'العلمي', 'Sara', 'سارة', 'EF345678', 'ENSA', 'sara.alami@gmail.com', '0634567890', '2025-03-01', '2025-03-31', 'Ressources Humaines', 'en_attente', NULL, NULL),
('STG004', 'Rachidi', 'الرشيدي', 'Youssef', 'يوسف', 'GH901234', 'Faculté des Sciences', 'youssef.rachidi@gmail.com', '0645678901', '2025-03-01', '2025-03-31', 'Finances', 'en_attente', NULL, NULL),
('STG005', 'El Fassi', 'الفاسي', 'Amina', 'أمينة', 'IJ567890', 'EST', 'amina.elfassi@gmail.com', '0656789012', '2025-03-01', '2025-03-31', 'Communication', 'en_attente', NULL, NULL),

-- Stagiaires validés
('STG006', 'Benjelloun', 'بنجلون', 'Karim', 'كريم', 'KL123456', 'ENSA', 'karim.benjelloun@gmail.com', '0667890123', '2025-02-01', '2025-02-28', 'Informatique', 'valide', 1, '2025-01-15'),
('STG007', 'Tazi', 'تازي', 'Layla', 'ليلى', 'MN789012', 'Faculté des Sciences', 'layla.tazi@gmail.com', '0678901234', '2025-02-01', '2025-02-28', 'Informatique', 'valide', 1, '2025-01-16'),
('STG008', 'Alami', 'العلمي', 'Omar', 'عمر', 'OP345678', 'EST', 'omar.alami@gmail.com', '0689012345', '2025-02-01', '2025-02-28', 'Ressources Humaines', 'valide', 2, '2025-01-17'),
('STG009', 'Rachidi', 'الرشيدي', 'Nour', 'نور', 'QR901234', 'ENSA', 'nour.rachidi@gmail.com', '0690123456', '2025-02-01', '2025-02-28', 'Finances', 'valide', 3, '2025-01-18'),
('STG010', 'El Fassi', 'الفاسي', 'Zineb', 'زينب', 'ST567890', 'Faculté des Sciences', 'zineb.elfassi@gmail.com', '0601234567', '2025-02-01', '2025-02-28', 'Communication', 'valide', 4, '2025-01-19'),

-- Stagiaires refusés
('STG011', 'Benali', 'بنعلي', 'Hassan', 'حسن', 'UV123456', 'EST', 'hassan.benali@gmail.com', '0612345679', '2025-01-01', '2025-01-31', 'Informatique', 'refuse', NULL, NULL),
('STG012', 'Tazi', 'تازي', 'Mariam', 'مريم', 'WX789012', 'ENSA', 'mariam.tazi@gmail.com', '0623456780', '2025-01-01', '2025-01-31', 'Urbanisme', 'refuse', NULL, NULL),
('STG013', 'Alami', 'العلمي', 'Adam', 'آدم', 'YZ345678', 'Faculté des Sciences', 'adam.alami@gmail.com', '0634567891', '2025-01-01', '2025-01-31', 'Finances', 'refuse', NULL, NULL),

-- Stagiaires terminés (pour les archives)
('STG014', 'Benjelloun', 'بنجلون', 'Imane', 'إيمان', 'AA123456', 'EST', 'imane.benjelloun@gmail.com', '0645678902', '2024-12-01', '2024-12-31', 'Informatique', 'valide', 1, '2024-11-15'),
('STG015', 'Tazi', 'تازي', 'Yassin', 'ياسين', 'BB789012', 'ENSA', 'yassin.tazi@gmail.com', '0656789013', '2024-12-01', '2024-12-31', 'Ressources Humaines', 'valide', 2, '2024-11-16');

-- Insertion d'affectations
INSERT INTO affectations (stagiaire_id, responsable_id, service, statut) VALUES
(6, 1, 'Informatique', 'active'),
(7, 1, 'Informatique', 'active'),
(8, 2, 'Ressources Humaines', 'active'),
(9, 3, 'Finances', 'active'),
(10, 4, 'Communication', 'active'),
(14, 1, 'Informatique', 'terminee'),
(15, 2, 'Ressources Humaines', 'terminee');

-- Insertion de stages
INSERT INTO stages (stagiaire_id, responsable_id, service, sujet, date_debut, date_fin, etat) VALUES
(6, 1, 'Informatique', 'Développement d\'une application web', '2025-02-01', '2025-02-28', 'en_cours'),
(7, 1, 'Informatique', 'Maintenance système', '2025-02-01', '2025-02-28', 'en_cours'),
(8, 2, 'Ressources Humaines', 'Gestion des ressources humaines', '2025-02-01', '2025-02-28', 'en_cours'),
(9, 3, 'Finances', 'Comptabilité générale', '2025-02-01', '2025-02-28', 'en_cours'),
(10, 4, 'Communication', 'Communication interne', '2025-02-01', '2025-02-28', 'en_cours'),
(14, 1, 'Informatique', 'Développement mobile', '2024-12-01', '2024-12-31', 'termine'),
(15, 2, 'Ressources Humaines', 'Recrutement', '2024-12-01', '2024-12-31', 'termine');

-- Insertion de tâches
INSERT INTO taches (stage_id, titre, description, date_debut, date_fin, statut, priorite) VALUES
(1, 'Analyse des besoins', 'Analyser les besoins du projet', '2025-02-01', '2025-02-05', 'terminee', 'haute'),
(1, 'Conception de la base de données', 'Créer le schéma de la base de données', '2025-02-06', '2025-02-10', 'en_cours', 'haute'),
(1, 'Développement frontend', 'Créer l\'interface utilisateur', '2025-02-11', '2025-02-20', 'a_faire', 'moyenne'),
(2, 'Installation des serveurs', 'Configurer les serveurs de développement', '2025-02-01', '2025-02-03', 'terminee', 'haute'),
(2, 'Mise à jour des systèmes', 'Mettre à jour les systèmes existants', '2025-02-04', '2025-02-15', 'en_cours', 'moyenne'),
(3, 'Étude des processus RH', 'Analyser les processus actuels', '2025-02-01', '2025-02-07', 'terminee', 'haute'),
(3, 'Optimisation des procédures', 'Améliorer les procédures existantes', '2025-02-08', '2025-02-21', 'en_cours', 'moyenne'),
(4, 'Vérification des comptes', 'Vérifier les comptes de l\'exercice', '2025-02-01', '2025-02-10', 'terminee', 'haute'),
(4, 'Préparation des rapports', 'Préparer les rapports financiers', '2025-02-11', '2025-02-25', 'en_cours', 'moyenne'),
(5, 'Création de contenu', 'Créer du contenu pour les réseaux sociaux', '2025-02-01', '2025-02-14', 'en_cours', 'moyenne'),
(5, 'Gestion des événements', 'Organiser les événements internes', '2025-02-15', '2025-02-28', 'a_faire', 'basse');

-- Insertion de remarques
INSERT INTO remarques (stage_id, responsable_id, titre, contenu, type) VALUES
(1, 1, 'Excellent travail', 'Le stagiaire montre une excellente compréhension des technologies web', 'positif'),
(1, 1, 'Amélioration nécessaire', 'Besoin d\'améliorer la documentation du code', 'negatif'),
(2, 1, 'Bon niveau technique', 'Très bon niveau en administration système', 'positif'),
(3, 2, 'Progression satisfaisante', 'Le stagiaire progresse bien dans la compréhension des processus RH', 'positif'),
(4, 3, 'Attention aux détails', 'Il faut être plus attentif aux détails dans les calculs', 'negatif'),
(5, 4, 'Créativité remarquable', 'Excellente créativité dans la création de contenu', 'positif');

-- Insertion de décisions
INSERT INTO decisions (stagiaire_id, responsable_id, decision, note_finale, commentaire) VALUES
(6, 1, 'valide', 18.5, 'Excellent stagiaire, très motivé et compétent'),
(7, 1, 'valide', 16.0, 'Bon travail, quelques améliorations possibles'),
(8, 2, 'valide', 17.5, 'Très bonne compréhension des enjeux RH'),
(9, 3, 'valide', 15.5, 'Satisfaisant, quelques points à améliorer'),
(10, 4, 'valide', 19.0, 'Excellente performance, très créatif'),
(11, 1, 'refuse', NULL, 'Documents incomplets et mal organisés'),
(12, 5, 'refuse', NULL, 'Profil ne correspond pas aux besoins du service'),
(13, 3, 'refuse', NULL, 'Dossier incomplet, documents manquants');

-- Insertion de suivi
INSERT INTO suivi_stagiaire (stagiaire_id, responsable_id, action, description, type_action) VALUES
(6, 1, 'Validation du dossier', 'Dossier validé après vérification des documents', 'etape'),
(6, 1, 'Affectation au service', 'Stagiaire affecté au service informatique', 'etape'),
(6, 1, 'Première tâche assignée', 'Tâche d\'analyse des besoins assignée', 'document'),
(7, 1, 'Validation du dossier', 'Dossier validé, documents complets', 'etape'),
(8, 2, 'Début du stage', 'Stage commencé dans le service RH', 'etape'),
(9, 3, 'Affectation', 'Stagiaire affecté au service finances', 'etape'),
(10, 4, 'Validation', 'Dossier validé pour le service communication', 'etape');

-- Insertion d'attestations
INSERT INTO attestations (stagiaire_id, type, numero_attestation, statut) VALUES
(6, 'debut', 'ATT-DEB-2025-001', 'generee'),
(7, 'debut', 'ATT-DEB-2025-002', 'generee'),
(8, 'debut', 'ATT-DEB-2025-003', 'generee'),
(9, 'debut', 'ATT-DEB-2025-004', 'generee'),
(10, 'debut', 'ATT-DEB-2025-005', 'generee'),
(14, 'fin', 'ATT-FIN-2024-001', 'generee'),
(15, 'fin', 'ATT-FIN-2024-002', 'generee');

-- Insertion de documents (simulation)
INSERT INTO documents (stagiaire_id, type_document, nom_fichier, chemin_fichier, statut) VALUES
(6, 'cv', 'cv_fatima_benali.pdf', 'uploads/documents/cv_fatima_benali.pdf', 'valide'),
(6, 'attestation_admin', 'attestation_admin_fatima.pdf', 'uploads/documents/attestation_admin_fatima.pdf', 'valide'),
(6, 'carte_identite', 'cni_fatima.jpg', 'uploads/documents/cni_fatima.jpg', 'valide'),
(6, 'demande_stage', 'demande_stage_fatima.pdf', 'uploads/documents/demande_stage_fatima.pdf', 'valide'),
(6, 'engagement', 'engagement_fatima.pdf', 'uploads/documents/engagement_fatima.pdf', 'valide'),
(7, 'cv', 'cv_ahmed_tazi.pdf', 'uploads/documents/cv_ahmed_tazi.pdf', 'valide'),
(7, 'attestation_admin', 'attestation_admin_ahmed.pdf', 'uploads/documents/attestation_admin_ahmed.pdf', 'valide'),
(7, 'carte_identite', 'cni_ahmed.jpg', 'uploads/documents/cni_ahmed.jpg', 'valide'),
(7, 'demande_stage', 'demande_stage_ahmed.pdf', 'uploads/documents/demande_stage_ahmed.pdf', 'valide'),
(7, 'engagement', 'engagement_ahmed.pdf', 'uploads/documents/engagement_ahmed.pdf', 'valide');

-- Insertion de stagiaires passés (archives)
INSERT INTO stagiaires_passes (stagiaire_id, responsable_id, service, date_debut_stage, date_fin_stage, motif_fin) VALUES
(14, 1, 'Informatique', '2024-12-01', '2024-12-31', 'Stage terminé avec succès'),
(15, 2, 'Ressources Humaines', '2024-12-01', '2024-12-31', 'Stage terminé avec succès');

-- Mise à jour du nombre de stagiaires actuels pour les responsables
UPDATE responsables SET nombre_stagiaires_actuel = 2 WHERE id = 1;
UPDATE responsables SET nombre_stagiaires_actuel = 1 WHERE id = 2;
UPDATE responsables SET nombre_stagiaires_actuel = 1 WHERE id = 3;
UPDATE responsables SET nombre_stagiaires_actuel = 1 WHERE id = 4;
UPDATE responsables SET nombre_stagiaires_actuel = 0 WHERE id = 5;

