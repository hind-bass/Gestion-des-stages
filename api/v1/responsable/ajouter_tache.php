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
    if (!isset($_POST['tache_stagiaire_id']) || !isset($_POST['tache_titre']) || 
        !isset($_POST['tache_description']) || !isset($_POST['tache_date_echeance'])) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
        exit();
    }
    
    $stagiaire_id = intval($_POST['tache_stagiaire_id']);
    $titre = trim($_POST['tache_titre']);
    $description = trim($_POST['tache_description']);
    $date_echeance = $_POST['tache_date_echeance'];
    $priorite = isset($_POST['tache_priorite']) ? $_POST['tache_priorite'] : 'normale';
    
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
    
    // Insérer la tâche
    $stmt = $pdo->prepare("
        INSERT INTO taches (stage_id, titre, description, date_fin, priorite, statut, date_creation) 
        VALUES (?, ?, ?, ?, ?, 'en_cours', NOW())
    ");
    
    $stmt->execute([$stage['id'], $titre, $description, $date_echeance, $priorite]);
    
    // Ajouter une entrée dans le suivi
    $stmt = $pdo->prepare("
        INSERT INTO suivi_stagiaire (stagiaire_id, responsable_id, action, description, type_action) 
        VALUES (?, ?, ?, ?, 'etape')
    ");
    $action = "Nouvelle tâche assignée";
    $description = "Tâche: " . $titre . " - Échéance: " . $date_echeance;
    $stmt->execute([$stagiaire_id, $responsable_id, $action, $description]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Tâche ajoutée avec succès'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 