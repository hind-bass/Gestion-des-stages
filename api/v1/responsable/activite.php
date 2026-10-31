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
    $activites = [];
    
    // Récupérer les tâches récentes
    $stmt = $pdo->prepare("
        SELECT 'tache' as type, 
               CONCAT('Nouvelle tâche: ', t.titre, ' pour ', s.prenom_fr, ' ', s.nom_fr) as titre, 
               t.date_creation as date,
               'info' as color,
               'fa-tasks' as icon
        FROM taches t 
        JOIN affectations a ON t.stagiaire_id = a.stagiaire_id 
        JOIN stagiaires s ON t.stagiaire_id = s.id
        WHERE a.responsable_id = ? AND t.date_creation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY t.date_creation DESC 
        LIMIT 5
    ");
    $stmt->execute([$responsable_id]);
    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les remarques récentes
    $stmt = $pdo->prepare("
        SELECT 'remarque' as type, 
               CONCAT('Remarque ajoutée pour ', s.prenom_fr, ' ', s.nom_fr) as titre, 
               r.date_creation as date,
               'warning' as color,
               'fa-comments' as icon
        FROM remarques r 
        JOIN affectations a ON r.stagiaire_id = a.stagiaire_id 
        JOIN stagiaires s ON r.stagiaire_id = s.id
        WHERE a.responsable_id = ? AND r.date_creation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY r.date_creation DESC 
        LIMIT 5
    ");
    $stmt->execute([$responsable_id]);
    $remarques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les attestations générées
    $stmt = $pdo->prepare("
        SELECT 'attestation' as type, 
               CONCAT('Attestation générée pour ', s.prenom_fr, ' ', s.nom_fr) as titre, 
               att.date_generation as date,
               'success' as color,
               'fa-file-pdf' as icon
        FROM attestations att 
        JOIN affectations a ON att.stagiaire_id = a.stagiaire_id 
        JOIN stagiaires s ON att.stagiaire_id = s.id
        WHERE a.responsable_id = ? AND att.type = 'fin' AND att.date_generation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY att.date_generation DESC 
        LIMIT 5
    ");
    $stmt->execute([$responsable_id]);
    $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combiner toutes les activités et les trier par date
    $activites = array_merge($taches, $remarques, $attestations);
    
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