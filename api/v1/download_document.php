<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['stagiaire_id']) && !isset($_SESSION['responsable_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier l'ID du document
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de document invalide']);
    exit();
}

$document_id = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les informations du document
    $stmt = $pdo->prepare("
        SELECT d.*, s.email as stagiaire_email 
        FROM documents d 
        JOIN stagiaires s ON d.stagiaire_id = s.id 
        WHERE d.id = ?
    ");
    $stmt->execute([$document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Document non trouvé']);
        exit();
    }
    
    // Vérifier les permissions
    $user_id = $_SESSION['stagiaire_id'] ?? $_SESSION['responsable_id'] ?? $_SESSION['admin_id'];
    $user_type = isset($_SESSION['stagiaire_id']) ? 'stagiaire' : (isset($_SESSION['responsable_id']) ? 'responsable' : 'admin');
    
    if ($user_type === 'stagiaire' && $document['stagiaire_id'] != $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Accès interdit']);
        exit();
    }
    
    // Vérifier que le fichier existe
    if (!file_exists($document['chemin_fichier'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Fichier non trouvé']);
        exit();
    }
    
    // Déterminer le type MIME
    $extension = strtolower(pathinfo($document['nom_fichier'], PATHINFO_EXTENSION));
    $mime_types = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ];
    $mime_type = $mime_types[$extension] ?? 'application/octet-stream';
    
    // Envoyer le fichier
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $document['nom_fichier'] . '"');
    header('Content-Length: ' . filesize($document['chemin_fichier']));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    readfile($document['chemin_fichier']);
    exit();
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 