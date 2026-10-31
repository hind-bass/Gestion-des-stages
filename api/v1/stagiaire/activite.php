<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant que stagiaire
if (!isset($_SESSION['stagiaire_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$stagiaire_id = $_SESSION['stagiaire_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $activites = [];
    
    // Récupérer les documents récents
    $stmt = $pdo->prepare("
        SELECT 'document' as type, type_document as titre, date_upload as date, 
               CASE 
                   WHEN statut = 'valide' THEN 'success'
                   WHEN statut = 'refuse' THEN 'error'
                   ELSE 'warning'
               END as color,
               'fa-file-alt' as icon
        FROM documents 
        WHERE stagiaire_id = ? 
        ORDER BY date_upload DESC 
        LIMIT 5
    ");
    $stmt->execute([$stagiaire_id]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les tâches récentes
    $stmt = $pdo->prepare("
        SELECT 'tache' as type, t.titre, t.date_creation as date,
               CASE 
                   WHEN t.statut = 'terminee' THEN 'success'
                   WHEN t.date_fin < CURDATE() THEN 'error'
                   ELSE 'warning'
               END as color,
               'fa-tasks' as icon
        FROM taches t 
        JOIN stages s ON t.stage_id = s.id 
        WHERE s.stagiaire_id = ? 
        ORDER BY t.date_creation DESC 
        LIMIT 5
    ");
    $stmt->execute([$stagiaire_id]);
    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les remarques récentes
    $stmt = $pdo->prepare("
        SELECT 'remarque' as type, r.titre, r.date_creation as date,
               CASE 
                   WHEN r.type = 'positif' THEN 'success'
                   WHEN r.type = 'negatif' THEN 'error'
                   ELSE 'info'
               END as color,
               'fa-comments' as icon
        FROM remarques r 
        JOIN stages s ON r.stage_id = s.id 
        WHERE s.stagiaire_id = ? 
        ORDER BY r.date_creation DESC 
        LIMIT 5
    ");
    $stmt->execute([$stagiaire_id]);
    $remarques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les attestations récentes
    $stmt = $pdo->prepare("
        SELECT 'attestation' as type, 
               CONCAT('Attestation de ', type) as titre, 
               date_generation as date,
               CASE 
                   WHEN statut = 'envoyee' THEN 'success'
                   WHEN statut = 'signee' THEN 'info'
                   ELSE 'warning'
               END as color,
               'fa-file-pdf' as icon
        FROM attestations 
        WHERE stagiaire_id = ? 
        ORDER BY date_generation DESC 
        LIMIT 5
    ");
    $stmt->execute([$stagiaire_id]);
    $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combiner toutes les activités et les trier par date
    $activites = array_merge($documents, $taches, $remarques, $attestations);
    
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