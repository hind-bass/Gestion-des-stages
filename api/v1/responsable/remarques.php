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
    
    // Récupérer les remarques des stagiaires affectés au responsable
    $stmt = $pdo->prepare("
        SELECT r.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire
        FROM remarques r 
        JOIN affectations a ON r.stagiaire_id = a.stagiaire_id 
        JOIN stagiaires s ON r.stagiaire_id = s.id
        WHERE a.responsable_id = ? AND a.statut = 'active'
        ORDER BY r.date_creation DESC
    ");
    $stmt->execute([$responsable_id]);
    $remarques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($remarques as &$remarque) {
        $remarque['date_creation'] = date('d/m/Y H:i', strtotime($remarque['date_creation']));
        
        // Traduire le type
        $types = [
            'positif' => 'Positif',
            'negatif' => 'Négatif',
            'neutre' => 'Neutre',
            'recommandation' => 'Recommandation'
        ];
        $remarque['type'] = $types[$remarque['type']] ?? $remarque['type'];
    }
    
    echo json_encode([
        'success' => true,
        'remarques' => $remarques
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 