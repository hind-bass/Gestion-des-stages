<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['stagiaire_id']) && !isset($_SESSION['responsable_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier l'ID de l'attestation
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID d\'attestation invalide']);
    exit();
}

$attestation_id = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les informations de l'attestation
    $stmt = $pdo->prepare("
        SELECT a.*, s.email as stagiaire_email 
        FROM attestations a 
        JOIN stagiaires s ON a.stagiaire_id = s.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$attestation_id]);
    $attestation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$attestation) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Attestation non trouvée']);
        exit();
    }
    
    // Vérifier les permissions
    $user_id = $_SESSION['stagiaire_id'] ?? $_SESSION['responsable_id'] ?? $_SESSION['admin_id'];
    $user_type = isset($_SESSION['stagiaire_id']) ? 'stagiaire' : (isset($_SESSION['responsable_id']) ? 'responsable' : 'admin');
    
    if ($user_type === 'stagiaire' && $attestation['stagiaire_id'] != $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Accès interdit']);
        exit();
    }
    
    // Vérifier que le fichier existe
    if (!$attestation['chemin_pdf'] || !file_exists($attestation['chemin_pdf'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Fichier PDF non trouvé']);
        exit();
    }
    
    // Envoyer le fichier PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="attestation_' . $attestation['type'] . '_' . $attestation['numero_attestation'] . '.pdf"');
    header('Content-Length: ' . filesize($attestation['chemin_pdf']));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    readfile($attestation['chemin_pdf']);
    exit();
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 