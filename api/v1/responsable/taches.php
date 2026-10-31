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
    
    // Récupérer les tâches des stagiaires affectés au responsable
    $stmt = $pdo->prepare("
        SELECT t.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire
        FROM taches t 
        JOIN stages st ON t.stage_id = st.id
        JOIN stagiaires s ON st.stagiaire_id = s.id
        JOIN affectations a ON s.id = a.stagiaire_id 
        WHERE a.responsable_id = ? AND a.statut = 'active'
        ORDER BY t.date_creation DESC
    ");
    $stmt->execute([$responsable_id]);
    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($taches as &$tache) {
        $tache['date_creation'] = date('d/m/Y H:i', strtotime($tache['date_creation']));
        $tache['date_echeance'] = $tache['date_fin'] ? date('d/m/Y', strtotime($tache['date_fin'])) : 'Non définie';
        
        // Traduire le statut
        $statuts = [
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'a_faire' => 'À faire'
        ];
        $tache['statut'] = $statuts[$tache['statut']] ?? $tache['statut'];
    }
    
    echo json_encode([
        'success' => true,
        'taches' => $taches
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 