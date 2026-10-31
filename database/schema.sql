CREATE DATABASE IF NOT EXISTS gestion_stages;
USE gestion_stages;

-- Table des stagiaires avec tous les champs nécessaires
CREATE TABLE stagiaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_stagiaire VARCHAR(20) UNIQUE,
    nom_fr VARCHAR(100) NOT NULL,
    nom_ar VARCHAR(100) NOT NULL,
    prenom_fr VARCHAR(100) NOT NULL,
    prenom_ar VARCHAR(100) NOT NULL,
    cin VARCHAR(20) NOT NULL UNIQUE,
    etablissement VARCHAR(200) NOT NULL,
    filiere VARCHAR(100),
    niveau VARCHAR(50),
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    mot_de_passe VARCHAR(255) NOT NULL,
    date_debut_souhaitee DATE,
    date_fin_souhaitee DATE,
    service_demande VARCHAR(100),
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'valide', 'refuse', 'rejete') DEFAULT 'en_attente',
    responsable_id INT NULL,
    date_validation DATE NULL,
    motif_refus TEXT NULL
);

-- Table des responsables
CREATE TABLE responsables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    service VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    nombre_stagiaires_max INT DEFAULT 20,
    nombre_stagiaires_actuel INT DEFAULT 0,
    statut ENUM('disponible', 'occupe', 'actif', 'inactif') DEFAULT 'actif',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des administrateurs
CREATE TABLE administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des services/départements
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    responsable_id INT,
    nombre_stagiaires_max INT DEFAULT 20,
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);

-- Table des stages
CREATE TABLE stages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    responsable_id INT,
    service VARCHAR(100),
    sujet VARCHAR(255),
    date_debut DATE,
    date_fin DATE,
    etat ENUM('en_attente', 'en_cours', 'termine', 'refuse') DEFAULT 'en_attente',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);

-- Table des documents
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    type_document ENUM('cv', 'attestation_admin', 'carte_identite', 'demande_stage', 'engagement') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
);

-- Table des tâches
CREATE TABLE taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stage_id INT NOT NULL,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    statut ENUM('a_faire', 'en_cours', 'terminee') DEFAULT 'a_faire',
    priorite ENUM('basse', 'moyenne', 'haute') DEFAULT 'moyenne',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stage_id) REFERENCES stages(id)
);

-- Table des remarques
CREATE TABLE remarques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stage_id INT NOT NULL,
    responsable_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    contenu TEXT NOT NULL,
    type ENUM('positif', 'negatif', 'neutre') DEFAULT 'neutre',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stage_id) REFERENCES stages(id),
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);

-- Table des attestations
CREATE TABLE attestations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    type ENUM('debut', 'fin') NOT NULL,
    numero_attestation VARCHAR(50) UNIQUE,
    date_generation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_signature DATE NULL,
    statut ENUM('generee', 'signee', 'envoyee') DEFAULT 'generee',
    chemin_pdf VARCHAR(500) NULL,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
);

-- Table des rapports de stage
CREATE TABLE rapports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    chemin_fichier VARCHAR(500) NOT NULL,
    date_depot DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
    commentaire_responsable TEXT NULL,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id)
);

-- Table des affectations (historique)
CREATE TABLE affectations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    responsable_id INT NOT NULL,
    service VARCHAR(100) NOT NULL,
    date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_fin_affectation DATETIME NULL,
    statut ENUM('active', 'terminee') DEFAULT 'active',
    motif_fin TEXT NULL,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);

-- Table des notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_utilisateur ENUM('stagiaire', 'responsable', 'administrateur') NOT NULL,
    titre VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type_notification ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    date_notification DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    lien VARCHAR(500) NULL
);

-- Table des stagiaires passés (archives)
CREATE TABLE stagiaires_passes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    responsable_id INT NULL,
    service VARCHAR(100),
    date_debut_stage DATE,
    date_fin_stage DATE,
    motif_fin TEXT,
    date_archivage DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
    FOREIGN KEY (responsable_id) REFERENCES responsables(id)
);

-- Insertion d'un administrateur par défaut
INSERT INTO administrateurs (nom, prenom, email, mot_de_passe, statut) VALUES 
('Admin', 'System', 'admin@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'actif'); -- password: password

-- Insertion de quelques services par défaut
INSERT INTO services (nom, description) VALUES 
('Informatique', 'Service informatique et développement'),
('Ressources Humaines', 'Gestion des ressources humaines'),
('Finances', 'Service financier et comptabilité'),
('Urbanisme', 'Service d urbanisme et aménagement'),
('Communication', 'Service de communication et relations publiques');

