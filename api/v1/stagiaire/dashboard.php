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
    
    // Récupérer les statistiques
    $stats = [];
    
    // Nombre de documents déposés
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE stagiaire_id = ?");
    $stmt->execute([$stagiaire_id]);
    $stats['documents'] = $stmt->fetchColumn();
    
    // Nombre de tâches terminées
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM taches t 
        JOIN stages s ON t.stage_id = s.id 
        WHERE s.stagiaire_id = ? AND t.statut = 'terminee'
    ");
    $stmt->execute([$stagiaire_id]);
    $stats['taches_terminees'] = $stmt->fetchColumn();
    
    // Nombre de tâches en retard
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM taches t 
        JOIN stages s ON t.stage_id = s.id 
        WHERE s.stagiaire_id = ? AND t.date_fin < CURDATE() AND t.statut != 'terminee'
    ");
    $stmt->execute([$stagiaire_id]);
    $stats['taches_retard'] = $stmt->fetchColumn();
    
    // Nombre d'attestations reçues
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attestations WHERE stagiaire_id = ? AND statut = 'envoyee'");
    $stmt->execute([$stagiaire_id]);
    $stats['attestations'] = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 