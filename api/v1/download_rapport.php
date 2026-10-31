<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['stagiaire_id']) && !isset($_SESSION['responsable_id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier l'ID du rapport
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de rapport invalide']);
    exit();
}

$rapport_id = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les informations du rapport
    $stmt = $pdo->prepare("
        SELECT r.*, s.email as stagiaire_email 
        FROM rapports r 
        JOIN stagiaires s ON r.stagiaire_id = s.id 
        WHERE r.id = ?
    ");
    $stmt->execute([$rapport_id]);
    $rapport = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$rapport) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Rapport non trouvé']);
        exit();
    }
    
    // Vérifier les permissions
    $user_id = $_SESSION['stagiaire_id'] ?? $_SESSION['responsable_id'] ?? $_SESSION['admin_id'];
    $user_type = isset($_SESSION['stagiaire_id']) ? 'stagiaire' : (isset($_SESSION['responsable_id']) ? 'responsable' : 'admin');
    
    if ($user_type === 'stagiaire' && $rapport['stagiaire_id'] != $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Accès interdit']);
        exit();
    }
    
    // Vérifier que le fichier existe
    if (!file_exists($rapport['chemin_fichier'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Fichier non trouvé']);
        exit();
    }
    
    // Déterminer le type MIME
    $extension = strtolower(pathinfo($rapport['chemin_fichier'], PATHINFO_EXTENSION));
    $mime_types = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    $mime_type = $mime_types[$extension] ?? 'application/octet-stream';
    
    // Envoyer le fichier
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="rapport_' . $rapport['stagiaire_id'] . '_' . date('Y-m-d', strtotime($rapport['date_depot'])) . '.' . $extension . '"');
    header('Content-Length: ' . filesize($rapport['chemin_fichier']));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    readfile($rapport['chemin_fichier']);
    exit();
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 