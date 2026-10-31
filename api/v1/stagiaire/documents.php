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
    
    // Récupérer tous les documents du stagiaire
    $stmt = $pdo->prepare("
        SELECT id, type_document, nom_fichier, statut, date_upload
        FROM documents 
        WHERE stagiaire_id = ? 
        ORDER BY date_upload DESC
    ");
    $stmt->execute([$stagiaire_id]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($documents as &$document) {
        $document['date_upload'] = date('d/m/Y H:i', strtotime($document['date_upload']));
        
        // Traduire le type de document
        $types = [
            'cv' => 'CV',
            'attestation_admin' => 'Attestation de stage donnée par l\'administration',
            'carte_identite' => 'Photocopie de la carte d\'identité',
            'demande_stage' => 'Demande de stage destinée au directeur de la commune',
            'engagement' => 'Document d\'engagement'
        ];
        $document['type_document'] = $types[$document['type_document']] ?? $document['type_document'];
    }
    
    echo json_encode([
        'success' => true,
        'documents' => $documents
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 