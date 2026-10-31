<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les statistiques
    $stats = [];
    
    // Total stagiaires
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires");
    $stmt->execute();
    $stats['total_stagiaires'] = $stmt->fetchColumn();
    
    // Stagiaires validés
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires WHERE statut = 'accepte'");
    $stmt->execute();
    $stats['stagiaires_valides'] = $stmt->fetchColumn();
    
    // Stagiaires en attente
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires WHERE statut = 'en_attente'");
    $stmt->execute();
    $stats['stagiaires_attente'] = $stmt->fetchColumn();
    
    // Total responsables
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM responsables");
    $stmt->execute();
    $stats['total_responsables'] = $stmt->fetchColumn();
    
    // Données pour les graphiques
    $charts = [];
    
    // Répartition par service
    $stmt = $pdo->prepare("
        SELECT service_demande, COUNT(*) as count 
        FROM stagiaires 
        WHERE statut = 'accepte' 
        GROUP BY service_demande 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $services_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $charts['services'] = [
        'labels' => array_column($services_data, 'service_demande'),
        'values' => array_column($services_data, 'count')
    ];
    
    // Évolution des inscriptions (1 an)
    $stmt = $pdo->prepare("
        SELECT DATE(date_inscription) as date, COUNT(*) as count 
        FROM stagiaires 
        WHERE date_inscription >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
        GROUP BY DATE(date_inscription) 
        ORDER BY date
    ");
    $stmt->execute();
    $evolution_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $charts['evolution'] = [
        'labels' => array_column($evolution_data, 'date'),
        'values' => array_column($evolution_data, 'count')
    ];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'charts' => $charts
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 