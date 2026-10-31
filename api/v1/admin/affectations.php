<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer toutes les affectations
    $stmt = $pdo->prepare("
        SELECT a.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire,
               CONCAT(r.prenom, ' ', r.nom) as responsable,
               s.service_demande as service
        FROM affectations a 
        JOIN stagiaires s ON a.stagiaire_id = s.id 
        JOIN responsables r ON a.responsable_id = r.id
        ORDER BY a.date_affectation DESC
    ");
    $stmt->execute();
    $affectations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($affectations as &$affectation) {
        $affectation['date_affectation'] = date('d/m/Y H:i', strtotime($affectation['date_affectation']));
        
        // Traduire le statut
        $statuts = [
            'active' => 'Active',
            'terminee' => 'Terminée'
        ];
        $affectation['statut'] = $statuts[$affectation['statut']] ?? $affectation['statut'];
    }
    
    echo json_encode([
        'success' => true,
        'affectations' => $affectations
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 