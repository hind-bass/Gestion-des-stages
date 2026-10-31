<?php
var_dump($_POST);
exit;
require_once '../vendor/autoload.php'; // ou ton fichier de connexion à la BDD

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_stages;charset=utf8', 'root', '');

// Récupération des données POST
$stagiaire_id   = $_POST['stagiaire_id'] ?? null;
$responsable_id = $_POST['responsable_id'] ?? null;
$decision       = $_POST['decision'] ?? null; // 'valide' ou 'refuse'
$note_finale    = $_POST['note_finale'] ?? null;
$commentaire    = $_POST['commentaire'] ?? null;

if (!$stagiaire_id || !$responsable_id || !$decision) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

// Vérifier si une décision existe déjà
$stmt = $pdo->prepare("SELECT id FROM decisions WHERE stagiaire_id = ?");
$stmt->execute([$stagiaire_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Mise à jour
    $stmt = $pdo->prepare("UPDATE decisions SET responsable_id=?, decision=?, note_finale=?, commentaire=?, date_decision=NOW() WHERE stagiaire_id=?");
    $success = $stmt->execute([$responsable_id, $decision, $note_finale, $commentaire, $stagiaire_id]);
} else {
    // Insertion
    $stmt = $pdo->prepare("INSERT INTO decisions (stagiaire_id, responsable_id, decision, note_finale, commentaire) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$stagiaire_id, $responsable_id, $decision, $note_finale, $commentaire]);
}

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Décision enregistrée']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
}
?>