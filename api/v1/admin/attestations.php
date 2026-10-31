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
    
    // Récupérer les attestations de début de stage
    $stmt = $pdo->prepare("
        SELECT a.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire,
               s.service_demande as service
        FROM attestations a 
        JOIN stagiaires s ON a.stagiaire_id = s.id 
        WHERE a.type = 'debut' AND s.statut = 'accepte'
        ORDER BY a.date_generation DESC
    ");
    $stmt->execute();
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
    
    // Récupérer les stagiaires sans attestation de début de stage
    $stmt = $pdo->prepare("
        SELECT s.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire,
               s.service_demande as service
        FROM stagiaires s
        LEFT JOIN attestations a ON a.stagiaire_id = s.id AND a.type = 'debut'
        WHERE s.statut = 'accepte' AND a.id IS NULL
        ORDER BY s.date_inscription DESC
    ");
    $stmt->execute();
    $stagiaires_sans_attestation = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'stagiaires_sans_attestation' => $stagiaires_sans_attestation
    ]);
    exit;
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}

if (empty($stagiaires_sans_attestation)) {
    echo "<p>Aucun stagiaire en attente d'attestation de début.</p>";
} else {
    foreach ($stagiaires_sans_attestation as $stagiaire) {
        echo "<p>{$stagiaire['stagiaire']} - {$stagiaire['service']}</p>";
        // bouton pour générer l'attestation...
    }
}
?>