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
    
    $activites = [];
    
    // Récupérer les nouvelles inscriptions
    $stmt = $pdo->prepare("
        SELECT 'inscription' as type, 
               CONCAT('Nouvelle inscription: ', prenom_fr, ' ', nom_fr) as titre, 
               date_inscription as date,
               'info' as color,
               'fa-user-plus' as icon
        FROM stagiaires 
        WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY date_inscription DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les validations de documents
    $stmt = $pdo->prepare("
        SELECT 'validation' as type, 
               CONCAT('Documents validés pour ', s.prenom_fr, ' ', s.nom_fr) as titre, 
               d.date_validation as date,
               'success' as color,
               'fa-check-circle' as icon
        FROM documents d 
        JOIN stagiaires s ON d.stagiaire_id = s.id 
        WHERE d.date_validation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY d.date_validation DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $validations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les affectations
    $stmt = $pdo->prepare("
        SELECT 'affectation' as type, 
               CONCAT('Affectation: ', s.prenom_fr, ' ', s.nom_fr, ' → ', res.prenom, ' ', res.nom) as titre, 
               a.date_affectation as date,
               'warning' as color,
               'fa-link' as icon
        FROM affectations a 
        JOIN stagiaires s ON a.stagiaire_id = s.id 
        JOIN responsables res ON a.responsable_id = res.id
        WHERE a.date_affectation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY a.date_affectation DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $affectations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les générations d'attestations
    $stmt = $pdo->prepare("
        SELECT 'attestation' as type, 
               CONCAT('Attestation générée pour ', s.prenom_fr, ' ', s.nom_fr) as titre, 
               a.date_generation as date,
               'success' as color,
               'fa-file-pdf' as icon
        FROM attestations a 
        JOIN stagiaires s ON a.stagiaire_id = s.id 
        WHERE a.date_generation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY a.date_generation DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combiner toutes les activités et les trier par date
    $activites = array_merge($inscriptions, $validations, $affectations, $attestations);
    
    // Trier par date décroissante
    usort($activites, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Limiter à 10 activités
    $activites = array_slice($activites, 0, 10);
    
    // Formater les dates
    foreach ($activites as &$activite) {
        $activite['date'] = date('d/m/Y H:i', strtotime($activite['date']));
    }
    
    echo json_encode([
        'success' => true,
        'activites' => $activites
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 