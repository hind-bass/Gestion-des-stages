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
    if (!isset($_POST['decision_stagiaire_id']) || !isset($_POST['decision_type']) || !isset($_POST['decision_commentaire'])) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
        exit();
    }
    
    $stagiaire_id = intval($_POST['decision_stagiaire_id']);
    $decision = trim($_POST['decision_type']);
    $commentaire = trim($_POST['decision_commentaire']);
    $note_finale = isset($_POST['decision_note']) ? floatval($_POST['decision_note']) : null;
    
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
    
    // Vérifier que la décision est valide
    if (!in_array($decision, ['valide', 'refuse'])) {
        echo json_encode(['success' => false, 'message' => 'Type de décision invalide']);
        exit();
    }
    
    // Vérifier que la note est valide si la décision est "valide"
    if ($decision === 'valide' && ($note_finale === null || $note_finale < 0 || $note_finale > 20)) {
        echo json_encode(['success' => false, 'message' => 'Note invalide pour une validation']);
        exit();
    }
    
    // Vérifier si une décision existe déjà pour ce stagiaire
    $stmt = $pdo->prepare("SELECT id FROM decisions WHERE stagiaire_id = ?");
    $stmt->execute([$stagiaire_id]);
    $existing_decision = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_decision) {
        // Mettre à jour la décision existante
        $stmt = $pdo->prepare("
            UPDATE decisions 
            SET decision = ?, note_finale = ?, commentaire = ?, date_decision = NOW() 
            WHERE stagiaire_id = ?
        ");
        $stmt->execute([$decision, $note_finale, $commentaire, $stagiaire_id]);
    } else {
        // Insérer une nouvelle décision
        $stmt = $pdo->prepare("
            INSERT INTO decisions (stagiaire_id, responsable_id, decision, note_finale, commentaire) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$stagiaire_id, $responsable_id, $decision, $note_finale, $commentaire]);
    }
    
    // Mettre à jour le statut du stagiaire
    $stmt = $pdo->prepare("UPDATE stagiaires SET statut = ? WHERE id = ?");
    $stmt->execute([$decision, $stagiaire_id]);
    
    // Ajouter une entrée dans le suivi
    $stmt = $pdo->prepare("
        INSERT INTO suivi_stagiaire (stagiaire_id, responsable_id, action, description, type_action) 
        VALUES (?, ?, ?, ?, 'etape')
    ");
    $action = "Décision prise";
    $description = "Décision: " . ($decision === 'valide' ? 'Validation' : 'Refus') . " - " . $commentaire;
    $stmt->execute([$stagiaire_id, $responsable_id, $action, $description]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Décision enregistrée avec succès'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 