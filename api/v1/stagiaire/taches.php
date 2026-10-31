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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer toutes les tâches du stagiaire
    $stmt = $pdo->prepare("
        SELECT t.id, t.titre, t.description, t.date_debut, t.date_fin, t.statut, t.priorite, t.date_creation
        FROM taches t 
        JOIN stages s ON t.stage_id = s.id 
        WHERE s.stagiaire_id = ? 
        ORDER BY t.date_creation DESC
    ");
    $stmt->execute([$stagiaire_id]);
    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($taches as &$tache) {
        $tache['date_creation'] = date('d/m/Y H:i', strtotime($tache['date_creation']));
        $tache['date_debut'] = $tache['date_debut'] ? date('d/m/Y', strtotime($tache['date_debut'])) : 'Non définie';
        $tache['date_fin'] = $tache['date_fin'] ? date('d/m/Y', strtotime($tache['date_fin'])) : 'Non définie';
        
        // Traduire les statuts
        $statuts = [
            'a_faire' => 'À faire',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée'
        ];
        $tache['statut'] = $statuts[$tache['statut']] ?? $tache['statut'];
        
        // Traduire les priorités
        $priorites = [
            'basse' => 'Basse',
            'moyenne' => 'Moyenne',
            'haute' => 'Haute'
        ];
        $tache['priorite'] = $priorites[$tache['priorite']] ?? $tache['priorite'];
    }
    
    echo json_encode([
        'success' => true,
        'taches' => $taches
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 