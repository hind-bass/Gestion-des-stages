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

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Vérifier les données reçues
if (!isset($_POST['type_document']) || !isset($_FILES['document_file'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

$type_document = $_POST['type_document'];
$file = $_FILES['document_file'];

// Vérifier le type de document
$types_autorises = ['cv', 'attestation_admin', 'carte_identite', 'demande_stage', 'engagement'];
if (!in_array($type_document, $types_autorises)) {
    echo json_encode(['success' => false, 'message' => 'Type de document non autorisé']);
    exit();
}

// Vérifier le fichier
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier']);
    exit();
}

// Vérifier la taille du fichier (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux (max 5MB)']);
    exit();
}

// Vérifier l'extension du fichier
$extensions_autorisees = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, $extensions_autorisees)) {
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer le dossier d'upload s'il n'existe pas
    $upload_dir = '../uploads/documents/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Générer un nom de fichier unique
    $filename = uniqid() . '_' . $stagiaire_id . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Déplacer le fichier
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier']);
        exit();
    }
    
    // Vérifier si un document de ce type existe déjà pour ce stagiaire
    $stmt = $pdo->prepare("SELECT id FROM documents WHERE stagiaire_id = ? AND type_document = ?");
    $stmt->execute([$stagiaire_id, $type_document]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Mettre à jour le document existant
        $stmt = $pdo->prepare("
            UPDATE documents 
            SET nom_fichier = ?, chemin_fichier = ?, date_upload = NOW(), statut = 'en_attente'
            WHERE id = ?
        ");
        $stmt->execute([$file['name'], $filepath, $existing['id']]);
    } else {
        // Insérer un nouveau document
        $stmt = $pdo->prepare("
            INSERT INTO documents (stagiaire_id, type_document, nom_fichier, chemin_fichier, statut)
            VALUES (?, ?, ?, ?, 'en_attente')
        ");
        $stmt->execute([$stagiaire_id, $type_document, $file['name'], $filepath]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Document déposé avec succès'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 