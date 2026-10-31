<?php
include 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Début de l'insertion des données...\n";
    
    // Vérifier si les tables existent
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'decisions'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        echo "Création de la table decisions...\n";
        $pdo->exec("
            CREATE TABLE decisions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                stagiaire_id INT NOT NULL,
                responsable_id INT NOT NULL,
                decision ENUM('valide', 'refuse') NOT NULL,
                note_finale DECIMAL(5,2) DEFAULT NULL,
                date_decision DATETIME DEFAULT CURRENT_TIMESTAMP,
                commentaire TEXT,
                FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
                FOREIGN KEY (responsable_id) REFERENCES responsables(id)
            )
        ");
    }
    
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'suivi_stagiaire'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        echo "Création de la table suivi_stagiaire...\n";
        $pdo->exec("
            CREATE TABLE suivi_stagiaire (
                id INT AUTO_INCREMENT PRIMARY KEY,
                stagiaire_id INT NOT NULL,
                responsable_id INT NOT NULL,
                action VARCHAR(255) NOT NULL,
                description TEXT,
                date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
                type_action ENUM('document', 'etape', 'autre') DEFAULT 'autre',
                FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id),
                FOREIGN KEY (responsable_id) REFERENCES responsables(id)
            )
        ");
    }
    
    // Insérer les données avec les bonnes dates (2024-2025)
    echo "Insertion des données de test...\n";
    
    // Vider les tables existantes pour éviter les doublons
    $pdo->exec("DELETE FROM suivi_stagiaire");
    $pdo->exec("DELETE FROM decisions");
    $pdo->exec("DELETE FROM remarques");
    $pdo->exec("DELETE FROM taches");
    $pdo->exec("DELETE FROM stages");
    $pdo->exec("DELETE FROM affectations");
    $pdo->exec("DELETE FROM stagiaires");
    $pdo->exec("DELETE FROM responsables");
    
    // Insertion de responsables
    $stmt = $pdo->prepare("
        INSERT INTO responsables (nom, prenom, email, password, service, telephone, nombre_stagiaires_actuel, statut) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $responsables = [
        ['Alami', 'Hassan', 'hassan.alami@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Informatique', '0612345678', 15, 'disponible'],
        ['Benjelloun', 'Fatima', 'fatima.benjelloun@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ressources Humaines', '0623456789', 8, 'disponible'],
        ['Tazi', 'Mohammed', 'mohammed.tazi@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Finances', '0634567890', 20, 'occupe'],
        ['Rachidi', 'Amina', 'amina.rachidi@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Communication', '0645678901', 12, 'disponible'],
        ['El Fassi', 'Karim', 'karim.elfassi@commune.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Urbanisme', '0656789012', 18, 'disponible']
    ];
    
    foreach ($responsables as $responsable) {
        $stmt->execute($responsable);
    }
    
    // Insertion de stagiaires
    $stmt = $pdo->prepare("
        INSERT INTO stagiaires (numero_stagiaire, nom_fr, nom_ar, prenom_fr, prenom_ar, cin, etablissement, email, telephone, date_debut_souhaitee, date_fin_souhaitee, service_demande, statut, responsable_id, date_validation) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stagiaires = [
        // Stagiaires en attente
        ['STG001', 'Benali', 'بنعلي', 'Fatima', 'فاطمة', 'AB123456', 'Faculté des Sciences', 'fatima.benali@gmail.com', '0612345678', '2025-03-01', '2025-03-31', 'Informatique', 'en_attente', NULL, NULL],
        ['STG002', 'Tazi', 'تازي', 'Ahmed', 'أحمد', 'CD789012', 'EST', 'ahmed.tazi@gmail.com', '0623456789', '2025-03-01', '2025-03-31', 'Informatique', 'en_attente', NULL, NULL],
        ['STG003', 'Alami', 'العلمي', 'Sara', 'سارة', 'EF345678', 'ENSA', 'sara.alami@gmail.com', '0634567890', '2025-03-01', '2025-03-31', 'Ressources Humaines', 'en_attente', NULL, NULL],
        ['STG004', 'Rachidi', 'الرشيدي', 'Youssef', 'يوسف', 'GH901234', 'Faculté des Sciences', 'youssef.rachidi@gmail.com', '0645678901', '2025-03-01', '2025-03-31', 'Finances', 'en_attente', NULL, NULL],
        ['STG005', 'El Fassi', 'الفاسي', 'Amina', 'أمينة', 'IJ567890', 'EST', 'amina.elfassi@gmail.com', '0656789012', '2025-03-01', '2025-03-31', 'Communication', 'en_attente', NULL, NULL],
        
        // Stagiaires validés
        ['STG006', 'Benjelloun', 'بنجلون', 'Karim', 'كريم', 'KL123456', 'ENSA', 'karim.benjelloun@gmail.com', '0667890123', '2025-02-01', '2025-02-28', 'Informatique', 'valide', 1, '2025-01-15'],
        ['STG007', 'Tazi', 'تازي', 'Layla', 'ليلى', 'MN789012', 'Faculté des Sciences', 'layla.tazi@gmail.com', '0678901234', '2025-02-01', '2025-02-28', 'Informatique', 'valide', 1, '2025-01-16'],
        ['STG008', 'Alami', 'العلمي', 'Omar', 'عمر', 'OP345678', 'EST', 'omar.alami@gmail.com', '0689012345', '2025-02-01', '2025-02-28', 'Ressources Humaines', 'valide', 2, '2025-01-17'],
        ['STG009', 'Rachidi', 'الرشيدي', 'Nour', 'نور', 'QR901234', 'ENSA', 'nour.rachidi@gmail.com', '0690123456', '2025-02-01', '2025-02-28', 'Finances', 'valide', 3, '2025-01-18'],
        ['STG010', 'El Fassi', 'الفاسي', 'Zineb', 'زينب', 'ST567890', 'Faculté des Sciences', 'zineb.elfassi@gmail.com', '0601234567', '2025-02-01', '2025-02-28', 'Communication', 'valide', 4, '2025-01-19'],
        
        // Stagiaires refusés
        ['STG011', 'Benali', 'بنعلي', 'Hassan', 'حسن', 'UV123456', 'EST', 'hassan.benali@gmail.com', '0612345679', '2025-01-01', '2025-01-31', 'Informatique', 'refuse', NULL, NULL],
        ['STG012', 'Tazi', 'تازي', 'Mariam', 'مريم', 'WX789012', 'ENSA', 'mariam.tazi@gmail.com', '0623456780', '2025-01-01', '2025-01-31', 'Urbanisme', 'refuse', NULL, NULL],
        ['STG013', 'Alami', 'العلمي', 'Adam', 'آدم', 'YZ345678', 'Faculté des Sciences', 'adam.alami@gmail.com', '0634567891', '2025-01-01', '2025-01-31', 'Finances', 'refuse', NULL, NULL],
        
        // Stagiaires terminés (pour les archives)
        ['STG014', 'Benjelloun', 'بنجلون', 'Imane', 'إيمان', 'AA123456', 'EST', 'imane.benjelloun@gmail.com', '0645678902', '2024-12-01', '2024-12-31', 'Informatique', 'valide', 1, '2024-11-15'],
        ['STG015', 'Tazi', 'تازي', 'Yassin', 'ياسين', 'BB789012', 'ENSA', 'yassin.tazi@gmail.com', '0656789013', '2024-12-01', '2024-12-31', 'Ressources Humaines', 'valide', 2, '2024-11-16']
    ];
    
    foreach ($stagiaires as $stagiaire) {
        $stmt->execute($stagiaire);
    }
    
    // Insertion d'affectations
    $stmt = $pdo->prepare("
        INSERT INTO affectations (stagiaire_id, responsable_id, service, statut, date_affectation) VALUES
        (?, ?, ?, ?, ?)
    ");
    
    $affectations = [
        [6, 1, 'Informatique', 'active', '2025-01-15 10:00:00'],
        [7, 1, 'Informatique', 'active', '2025-01-16 10:00:00'],
        [8, 2, 'Ressources Humaines', 'active', '2025-01-17 10:00:00'],
        [9, 3, 'Finances', 'active', '2025-01-18 10:00:00'],
        [10, 4, 'Communication', 'active', '2025-01-19 10:00:00'],
        [14, 1, 'Informatique', 'terminee', '2024-11-15 10:00:00'],
        [15, 2, 'Ressources Humaines', 'terminee', '2024-11-16 10:00:00']
    ];
    
    foreach ($affectations as $affectation) {
        $stmt->execute($affectation);
    }
    
    // Insertion de stages
    $stmt = $pdo->prepare("
        INSERT INTO stages (stagiaire_id, responsable_id, service, sujet, date_debut, date_fin, etat) VALUES
        (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stages = [
        [6, 1, 'Informatique', 'Développement d\'une application web', '2025-02-01', '2025-02-28', 'en_cours'],
        [7, 1, 'Informatique', 'Maintenance système', '2025-02-01', '2025-02-28', 'en_cours'],
        [8, 2, 'Ressources Humaines', 'Gestion des ressources humaines', '2025-02-01', '2025-02-28', 'en_cours'],
        [9, 3, 'Finances', 'Comptabilité générale', '2025-02-01', '2025-02-28', 'en_cours'],
        [10, 4, 'Communication', 'Communication interne', '2025-02-01', '2025-02-28', 'en_cours'],
        [14, 1, 'Informatique', 'Développement mobile', '2024-12-01', '2024-12-31', 'termine'],
        [15, 2, 'Ressources Humaines', 'Recrutement', '2024-12-01', '2024-12-31', 'termine']
    ];
    
    foreach ($stages as $stage) {
        $stmt->execute($stage);
    }
    
    // Insertion de tâches
    $stmt = $pdo->prepare("
        INSERT INTO taches (stage_id, titre, description, date_fin, priorite, statut, date_creation) VALUES
        (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $taches = [
        [1, 'Analyse des besoins', 'Analyser les besoins du projet', '2025-02-05', 'haute', 'terminee'],
        [1, 'Conception de la base de données', 'Créer le schéma de la base de données', '2025-02-10', 'haute', 'en_cours'],
        [1, 'Développement frontend', 'Créer l\'interface utilisateur', '2025-02-20', 'moyenne', 'a_faire'],
        [2, 'Installation des serveurs', 'Configurer les serveurs de développement', '2025-02-03', 'haute', 'terminee'],
        [2, 'Mise à jour des systèmes', 'Mettre à jour les systèmes existants', '2025-02-15', 'moyenne', 'en_cours'],
        [3, 'Étude des processus RH', 'Analyser les processus actuels', '2025-02-07', 'haute', 'terminee'],
        [3, 'Optimisation des procédures', 'Améliorer les procédures existantes', '2025-02-21', 'moyenne', 'en_cours'],
        [4, 'Vérification des comptes', 'Vérifier les comptes de l\'exercice', '2025-02-10', 'haute', 'terminee'],
        [4, 'Préparation des rapports', 'Préparer les rapports financiers', '2025-02-25', 'moyenne', 'en_cours'],
        [5, 'Création de contenu', 'Créer du contenu pour les réseaux sociaux', '2025-02-14', 'moyenne', 'en_cours'],
        [5, 'Gestion des événements', 'Organiser les événements internes', '2025-02-28', 'basse', 'a_faire']
    ];
    
    foreach ($taches as $tache) {
        $stmt->execute($tache);
    }
    
    // Insertion de remarques
    $stmt = $pdo->prepare("
        INSERT INTO remarques (stage_id, responsable_id, titre, contenu, type, date_creation) VALUES
        (?, ?, ?, ?, ?, ?)
    ");
    
    $remarques = [
        [1, 1, 'Excellent travail', 'Le stagiaire montre une excellente compréhension des technologies web', 'positif', '2025-02-03 14:00:00'],
        [1, 1, 'Amélioration nécessaire', 'Besoin d\'améliorer la documentation du code', 'negatif', '2025-02-05 16:00:00'],
        [2, 1, 'Bon niveau technique', 'Très bon niveau en administration système', 'positif', '2025-02-02 11:00:00'],
        [3, 2, 'Progression satisfaisante', 'Le stagiaire progresse bien dans la compréhension des processus RH', 'positif', '2025-02-04 15:00:00'],
        [4, 3, 'Attention aux détails', 'Il faut être plus attentif aux détails dans les calculs', 'negatif', '2025-02-06 10:00:00'],
        [5, 4, 'Créativité remarquable', 'Excellente créativité dans la création de contenu', 'positif', '2025-02-03 13:00:00']
    ];
    
    foreach ($remarques as $remarque) {
        $stmt->execute($remarque);
    }
    
    // Insertion de décisions
    $stmt = $pdo->prepare("
        INSERT INTO decisions (stagiaire_id, responsable_id, decision, note_finale, commentaire, date_decision) VALUES
        (?, ?, ?, ?, ?, ?)
    ");
    
    $decisions = [
        [6, 1, 'valide', 18.5, 'Excellent stagiaire, très motivé et compétent', '2025-01-15 16:00:00'],
        [7, 1, 'valide', 16.0, 'Bon travail, quelques améliorations possibles', '2025-01-16 14:30:00'],
        [8, 2, 'valide', 17.5, 'Très bonne compréhension des enjeux RH', '2025-01-17 15:45:00'],
        [9, 3, 'valide', 15.5, 'Satisfaisant, quelques points à améliorer', '2025-01-18 11:20:00'],
        [10, 4, 'valide', 19.0, 'Excellente performance, très créatif', '2025-01-19 13:15:00'],
        [11, 1, 'refuse', NULL, 'Documents incomplets et mal organisés', '2025-01-10 09:00:00'],
        [12, 5, 'refuse', NULL, 'Profil ne correspond pas aux besoins du service', '2025-01-11 10:30:00'],
        [13, 3, 'refuse', NULL, 'Dossier incomplet, documents manquants', '2025-01-12 14:45:00']
    ];
    
    foreach ($decisions as $decision) {
        $stmt->execute($decision);
    }
    
    // Insertion de suivi
    $stmt = $pdo->prepare("
        INSERT INTO suivi_stagiaire (stagiaire_id, responsable_id, action, description, type_action, date_action) VALUES
        (?, ?, ?, ?, ?, ?)
    ");
    
    $suivi = [
        [6, 1, 'Validation du dossier', 'Dossier validé après vérification des documents', 'etape', '2025-01-15 10:00:00'],
        [6, 1, 'Affectation au service', 'Stagiaire affecté au service informatique', 'etape', '2025-01-15 10:30:00'],
        [6, 1, 'Première tâche assignée', 'Tâche d\'analyse des besoins assignée', 'document', '2025-02-01 09:00:00'],
        [7, 1, 'Validation du dossier', 'Dossier validé, documents complets', 'etape', '2025-01-16 11:00:00'],
        [8, 2, 'Début du stage', 'Stage commencé dans le service RH', 'etape', '2025-02-01 08:00:00'],
        [9, 3, 'Affectation', 'Stagiaire affecté au service finances', 'etape', '2025-01-18 12:00:00'],
        [10, 4, 'Validation', 'Dossier validé pour le service communication', 'etape', '2025-01-19 14:00:00']
    ];
    
    foreach ($suivi as $action) {
        $stmt->execute($action);
    }
    
    echo "Données insérées avec succès !\n";
    echo "Les dates sont maintenant entre 2024 et 2025 pour les statistiques.\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?> 