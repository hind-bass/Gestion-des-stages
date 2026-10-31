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
    
    // Récupérer les attestations de fin de stage des stagiaires affectés au responsable
    $stmt = $pdo->prepare("
        SELECT att.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire
        FROM attestations att 
        JOIN affectations a ON att.stagiaire_id = a.stagiaire_id 
        JOIN stagiaires s ON att.stagiaire_id = s.id
        WHERE a.responsable_id = ? AND att.type = 'fin'
        ORDER BY att.date_generation DESC
    ");
    $stmt->execute([$responsable_id]);
    $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($attestations as &$attestation) {
        $attestation['date_generation'] = date('d/m/Y H:i', strtotime($attestation['date_generation']));
        
        // Traduire le statut
        $statuts = [
            'generee' => 'Générée',
            'signee' => 'Signée',
            'envoyee' => 'Envoyée'
        ];
        $attestation['statut'] = $statuts[$attestation['statut']] ?? $attestation['statut'];
    }
    
    echo json_encode([
        'success' => true,
        'attestations' => $attestations
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 