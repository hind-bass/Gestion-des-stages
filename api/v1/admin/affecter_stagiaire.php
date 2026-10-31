<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Vérifier les données reçues
if (!isset($_POST['affectation_stagiaire_id']) || !isset($_POST['affectation_service']) || 
    !isset($_POST['affectation_responsable'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

$stagiaire_id = (int)$_POST['affectation_stagiaire_id'];
$service = trim($_POST['affectation_service']);
$responsable_id = (int)$_POST['affectation_responsable'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier que le stagiaire existe et est validé
    $stmt = $pdo->prepare("SELECT id, statut FROM stagiaires WHERE id = ?");
    $stmt->execute([$stagiaire_id]);
    $stagiaire = $stmt->fetch();
    
    if (!$stagiaire) {
        echo json_encode(['success' => false, 'message' => 'Stagiaire non trouvé']);
        exit();
    }
    
    if ($stagiaire['statut'] !== 'valide') {
        echo json_encode(['success' => false, 'message' => 'Le stagiaire doit être validé avant d\'être affecté']);
        exit();
    }
    
    // Vérifier que le responsable existe
    $stmt = $pdo->prepare("SELECT id FROM responsables WHERE id = ?");
    $stmt->execute([$responsable_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Responsable non trouvé']);
        exit();
    }
    
    // Vérifier si le stagiaire est déjà affecté
    $stmt = $pdo->prepare("SELECT id FROM affectations WHERE stagiaire_id = ? AND statut = 'active'");
    $stmt->execute([$stagiaire_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ce stagiaire est déjà affecté']);
        exit();
    }
    
    // Vérifier la capacité du responsable
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM affectations 
        WHERE responsable_id = ? AND statut = 'active'
    ");
    $stmt->execute([$responsable_id]);
    $count = $stmt->fetchColumn();
    
    if ($count >= 20) {
        echo json_encode(['success' => false, 'message' => 'Ce responsable a atteint sa capacité maximale (20 stagiaires)']);
        exit();
    }
    
    // Créer l'affectation
    $stmt = $pdo->prepare("
        INSERT INTO affectations (stagiaire_id, responsable_id, service, statut, date_affectation)
        VALUES (?, ?, ?, 'active', NOW())
    ");
    $stmt->execute([$stagiaire_id, $responsable_id, $service]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Stagiaire affecté avec succès'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 