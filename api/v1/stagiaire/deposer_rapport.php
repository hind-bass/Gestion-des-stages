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
if (!isset($_POST['rapport_titre']) || !isset($_POST['rapport_description']) || !isset($_FILES['rapport_file'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

$titre = trim($_POST['rapport_titre']);
$description = trim($_POST['rapport_description']);
$file = $_FILES['rapport_file'];

// Vérifier les données
if (empty($titre) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Le titre et la description sont obligatoires']);
    exit();
}

// Vérifier le fichier
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier']);
    exit();
}

// Vérifier la taille du fichier (10MB max)
if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux (max 10MB)']);
    exit();
}

// Vérifier l'extension du fichier
$extensions_autorisees = ['pdf', 'doc', 'docx'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, $extensions_autorisees)) {
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé. Formats acceptés: PDF, DOC, DOCX']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer le dossier d'upload s'il n'existe pas
    $upload_dir = '../uploads/rapports/';
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
    
    // Vérifier si un rapport existe déjà pour ce stagiaire
    $stmt = $pdo->prepare("SELECT id FROM rapports WHERE stagiaire_id = ? ORDER BY date_depot DESC LIMIT 1");
    $stmt->execute([$stagiaire_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Mettre à jour le rapport existant
        $stmt = $pdo->prepare("
            UPDATE rapports 
            SET titre = ?, description = ?, chemin_fichier = ?, date_depot = NOW(), statut = 'en_attente', commentaire_responsable = NULL
            WHERE id = ?
        ");
        $stmt->execute([$titre, $description, $filepath, $existing['id']]);
    } else {
        // Insérer un nouveau rapport
        $stmt = $pdo->prepare("
            INSERT INTO rapports (stagiaire_id, titre, description, chemin_fichier, statut)
            VALUES (?, ?, ?, ?, 'en_attente')
        ");
        $stmt->execute([$stagiaire_id, $titre, $description, $filepath]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Rapport déposé avec succès'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 