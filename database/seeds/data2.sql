-- Script pour ajouter les tables et colonnes manquantes
-- Exécuter ce script sur votre base de données
use gestion_stages;

-- Vérifier et corriger les valeurs d'énumération dans la table stagiaires
ALTER TABLE stagiaires MODIFY COLUMN statut ENUM('en_attente', 'valide', 'accepte', 'refuse') DEFAULT 'en_attente';

-- Insérer des données de test si les tables sont vides
INSERT IGNORE INTO stagiaires (nom_fr, nom_ar, prenom_fr, prenom_ar, cin, etablissement, email, telephone, service_demande, statut)
VALUES 
('ALAMI', 'العلمي', 'Mohammed', 'محمد', 'AB123456', 'Université Mohammed V', 'mohammed.alami@email.com', '0661234567', 'Informatique', 'en_attente'),
('BENALI', 'بن علي', 'Fatima', 'فاطمة', 'CD789012', 'ENSA Oujda', 'fatima.benali@email.com', '0667891234', 'Ressources Humaines', 'accepte'),
('CHRAIBI', 'الشرايبي', 'Ahmed', 'أحمد', 'EF345678', 'FST Settat', 'ahmed.chraibi@email.com', '0663456789', 'Finances', 'en_attente');

-- Insérer quelques remarques de test
INSERT IGNORE INTO remarques (stage_id, responsable_id, titre, contenu, type, date_creation)
SELECT 
    1, 1, 'Première évaluation', 'Le stagiaire montre une bonne motivation et s\'intègre bien dans l\'équipe.', 'positif', NOW()
WHERE EXISTS (SELECT 1 FROM stages WHERE id = 1)
AND EXISTS (SELECT 1 FROM responsables WHERE id = 1);

-- Insérer quelques documents de test
-- ALTER TABLE documents_stagiaire ADD COLUMN nom_fichier VARCHAR(255) NOT NULL;
ALTER TABLE documents_stagiaire ADD COLUMN statut  ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente';

INSERT IGNORE INTO documents_stagiaire (stagiaire_id, type_document, nom_fichier, chemin_fichier, statut)
SELECT 
    1, 'cv', 'cv_mohammed_alami.pdf', 'uploads/documents/cv_mohammed_alami.pdf', 'en_attente'
WHERE EXISTS (SELECT 1 FROM stagiaires WHERE id = 1);

