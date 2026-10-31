-- Script de mise à jour de la base de données
-- Exécutez ce script si vous avez déjà une base de données existante

USE gestion_stages;

-- Ajouter le champ mot_de_passe à la table stagiaires s'il n'existe pas
ALTER TABLE stagiaires 
ADD COLUMN IF NOT EXISTS mot_de_passe VARCHAR(255) NOT NULL DEFAULT '' AFTER email,
ADD COLUMN IF NOT EXISTS filiere VARCHAR(100) AFTER etablissement,
ADD COLUMN IF NOT EXISTS niveau VARCHAR(50) AFTER filiere;

-- Mettre à jour le champ statut des stagiaires pour inclure 'rejete'
ALTER TABLE stagiaires 
MODIFY COLUMN statut ENUM('en_attente', 'valide', 'refuse', 'rejete') DEFAULT 'en_attente';

-- Renommer le champ password en mot_de_passe dans la table responsables
ALTER TABLE responsables 
CHANGE COLUMN password mot_de_passe VARCHAR(255) NOT NULL;

-- Mettre à jour le champ statut des responsables
ALTER TABLE responsables 
MODIFY COLUMN statut ENUM('disponible', 'occupe', 'actif', 'inactif') DEFAULT 'actif';

-- Ajouter le champ statut à la table administrateurs
ALTER TABLE administrateurs 
ADD COLUMN IF NOT EXISTS statut ENUM('actif', 'inactif') DEFAULT 'actif' AFTER mot_de_passe;

-- Renommer le champ password en mot_de_passe dans la table administrateurs
ALTER TABLE administrateurs 
CHANGE COLUMN password mot_de_passe VARCHAR(255) NOT NULL;

-- Mettre à jour l'administrateur par défaut
UPDATE administrateurs SET statut = 'actif' WHERE email = 'admin@commune.ma';

-- Mettre à jour les responsables existants
UPDATE responsables SET statut = 'actif' WHERE statut IS NULL OR statut = 'disponible';

-- Mettre à jour les stagiaires existants
UPDATE stagiaires SET mot_de_passe = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE mot_de_passe = '' OR mot_de_passe IS NULL;
