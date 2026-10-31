<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant que responsable
if (!isset($_SESSION['responsable_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $responsable_id = $_SESSION['responsable_id'];
    
    // Vérifier que tous les champs requis sont présents
    if (!isset($_POST['remarque_stagiaire_id']) || !isset($_POST['remarque_type']) || !isset($_POST['remarque_contenu'])) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
        exit();
    }
    
    $stagiaire_id = intval($_POST['remarque_stagiaire_id']);
    $type = trim($_POST['remarque_type']);
    $contenu = trim($_POST['remarque_contenu']);
    
    // Vérifier que le stagiaire est bien affecté au responsable
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM affectations 
        WHERE stagiaire_id = ? AND responsable_id = ? AND statut = 'active'
    ");
    $stmt->execute([$stagiaire_id, $responsable_id]);
    
    if ($stmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Stagiaire non trouvé ou non affecté à ce responsable']);
        exit();
    }
    
    // Récupérer l'ID du stage
    $stmt = $pdo->prepare("SELECT id FROM stages WHERE stagiaire_id = ? AND responsable_id = ?");
    $stmt->execute([$stagiaire_id, $responsable_id]);
    $stage = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$stage) {
        echo json_encode(['success' => false, 'message' => 'Stage non trouvé']);
        exit();
    }
    
    // Insérer la remarque
    $stmt = $pdo->prepare("
        INSERT INTO remarques (stage_id, responsable_id, titre, contenu, type, date_creation) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $titre = "Remarque " . ucfirst($type);
    $stmt->execute([$stage['id'], $responsable_id, $titre, $contenu, $type]);
    
    // Ajouter une entrée dans le suivi
    $stmt = $pdo->prepare("
        INSERT INTO suivi_stagiaire (stagiaire_id, responsable_id, action, description, type_action) 
        VALUES (?, ?, ?, ?, 'autre')
    ");
    $action = "Remarque ajoutée";
    $description = "Nouvelle remarque de type: " . $type;
    $stmt->execute([$stagiaire_id, $responsable_id, $action, $description]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Remarque ajoutée avec succès'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 