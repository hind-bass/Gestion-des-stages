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
    
    // Récupérer toutes les attestations du stagiaire
    $stmt = $pdo->prepare("
        SELECT id, type, numero_attestation, date_generation, date_signature, statut, chemin_pdf
        FROM attestations 
        WHERE stagiaire_id = ? 
        ORDER BY date_generation DESC
    ");
    $stmt->execute([$stagiaire_id]);
    $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($attestations as &$attestation) {
        $attestation['date_generation'] = date('d/m/Y H:i', strtotime($attestation['date_generation']));
        $attestation['date_signature'] = $attestation['date_signature'] ? date('d/m/Y', strtotime($attestation['date_signature'])) : null;
        
        // Traduire les types
        $types = [
            'debut' => 'début de stage',
            'fin' => 'fin de stage'
        ];
        $attestation['type'] = $types[$attestation['type']] ?? $attestation['type'];
        
        // Traduire les statuts
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