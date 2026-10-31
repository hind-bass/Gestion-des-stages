<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $ok = $stmt->execute([$_POST['id']]);
    echo json_encode(['success' => $ok]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 
?>