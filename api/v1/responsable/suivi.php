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
    
    // Récupérer le suivi des stagiaires du responsable
    $stmt = $pdo->prepare("
        SELECT ss.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire_nom,
               s.numero_stagiaire
        FROM suivi_stagiaire ss
        JOIN stagiaires s ON ss.stagiaire_id = s.id
        WHERE ss.responsable_id = ?
        ORDER BY ss.date_action DESC
    ");
    $stmt->execute([$responsable_id]);
    $suivi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($suivi as &$action) {
        $action['date_action'] = date('d/m/Y H:i', strtotime($action['date_action']));
        
        // Traduire le type d'action
        $types = [
            'document' => 'Document',
            'etape' => 'Étape',
            'autre' => 'Autre'
        ];
        $action['type_action_libelle'] = $types[$action['type_action']] ?? $action['type_action'];
    }
    
    echo json_encode([
        'success' => true,
        'suivi' => $suivi
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 