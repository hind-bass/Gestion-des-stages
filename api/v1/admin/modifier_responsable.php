<?php
include '../config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $id = intval($_POST['responsable_id']);
    $nom = trim($_POST['responsable_nom']);
    $prenom = trim($_POST['responsable_prenom']);
    $email = trim($_POST['responsable_email']);
    $service = trim($_POST['responsable_service']);
    $telephone = trim($_POST['responsable_telephone'] ?? '');
    $stmt = $pdo->prepare("UPDATE responsables SET nom = ?, prenom = ?, email = ?, service = ?, telephone = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $email, $service, $telephone, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 
?>