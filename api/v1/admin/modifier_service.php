<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if (!isset($_POST['id'], $_POST['nom'], $_POST['description'], $_POST['nombre_stagiaires_max'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $stmt = $pdo->prepare("UPDATE services SET nom = ?, description = ?, nombre_stagiaires_max = ? WHERE id = ?");
    $ok = $stmt->execute([
        $_POST['nom'],
        $_POST['description'],
        $_POST['nombre_stagiaires_max'],
        $_POST['id']
    ]);
    echo json_encode(['success' => $ok]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
 
?>