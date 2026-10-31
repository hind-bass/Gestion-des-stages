<?php
require_once '../config.php';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'add') {
    $stagiaire_id = $_SESSION['stagiaire_id'] ?? null;
    $responsable_id = $_SESSION['responsable_id'] ?? null;
    $type_action = $_POST['type_action'] ?? 'autre';
    $action_label = $_POST['action_label'] ?? '';
    $description = $_POST['description'] ?? '';
    if ($stagiaire_id && $responsable_id && $action_label) {
        $stmt = $pdo->prepare('INSERT INTO suivi_stagiaire (stagiaire_id, responsable_id, action, description, type_action) VALUES (?, ?, ?, ?, ?)');
        $ok = $stmt->execute([$stagiaire_id, $responsable_id, $action_label, $description, $type_action]);
        echo json_encode(['success' => $ok]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM suivi_stagiaire WHERE id = ?');
        $ok = $stmt->execute([$id]);
        echo json_encode(['success' => $ok]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

if ($action === 'list') {
    $stagiaire_id = $_GET['stagiaire_id'] ?? null;
    if ($stagiaire_id) {
        $stmt = $pdo->prepare('SELECT * FROM suivi_stagiaire WHERE stagiaire_id = ? ORDER BY date_action DESC');
        $stmt->execute([$stagiaire_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Stagiaire non spécifié']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action inconnue']); 
?>