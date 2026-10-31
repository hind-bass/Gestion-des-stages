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
    
    // Récupérer toutes les remarques du stagiaire
    $stmt = $pdo->prepare("
        SELECT r.id, r.titre, r.contenu, r.type, r.date_creation, r.date_modification,
               CONCAT(res.prenom, ' ', res.nom) as responsable
        FROM remarques r 
        JOIN stages s ON r.stage_id = s.id 
        JOIN responsables res ON r.responsable_id = res.id
        WHERE s.stagiaire_id = ? 
        ORDER BY r.date_creation DESC
    ");
    $stmt->execute([$stagiaire_id]);
    $remarques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($remarques as &$remarque) {
        $remarque['date_creation'] = date('d/m/Y H:i', strtotime($remarque['date_creation']));
        $remarque['date_modification'] = $remarque['date_modification'] ? date('d/m/Y H:i', strtotime($remarque['date_modification'])) : null;
        
        // Traduire les types
        $types = [
            'positif' => 'Positif',
            'negatif' => 'Négatif',
            'neutre' => 'Neutre'
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