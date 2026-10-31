<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant que responsable
if (!isset($_SESSION['responsable_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $responsable_id = $_SESSION['responsable_id'];
    
    // Récupérer les stagiaires affectés au responsable avec leurs tâches
    $stmt = $pdo->prepare("
        SELECT s.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as nom_complet,
               a.date_affectation,
               a.service,
               a.statut as statut_affectation,
               s.date_debut_souhaitee,
               s.date_fin_souhaitee,
               COUNT(t.id) as nombre_taches,
               COUNT(CASE WHEN t.statut = 'en_cours' THEN 1 END) as taches_en_cours,
               COUNT(CASE WHEN t.statut = 'terminee' THEN 1 END) as taches_terminees
        FROM stagiaires s 
        JOIN affectations a ON s.id = a.stagiaire_id 
        LEFT JOIN stages st ON s.id = st.stagiaire_id
        LEFT JOIN taches t ON st.id = t.stage_id
        WHERE a.responsable_id = ? AND a.statut = 'active'
        GROUP BY s.id
        ORDER BY a.date_affectation DESC
    ");
    $stmt->execute([$responsable_id]);
    $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($stagiaires as &$stagiaire) {
        $stagiaire['date_affectation'] = date('d/m/Y H:i', strtotime($stagiaire['date_affectation']));
        $stagiaire['date_debut'] = $stagiaire['date_debut_souhaitee'] ? date('d/m/Y', strtotime($stagiaire['date_debut_souhaitee'])) : 'Non définie';
        $stagiaire['date_fin'] = $stagiaire['date_fin_souhaitee'] ? date('d/m/Y', strtotime($stagiaire['date_fin_souhaitee'])) : 'Non définie';
        
        // Traduire le statut
        $statuts = [
            'active' => 'Active',
            'terminee' => 'Terminée'
        ];
        $stagiaire['statut'] = $statuts[$stagiaire['statut_affectation']] ?? $stagiaire['statut_affectation'];
    }
    
    echo json_encode([
        'success' => true,
        'stagiaires' => $stagiaires
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 