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
    
    $statistiques = [];
    
    // Statistiques par service
    $stmt = $pdo->prepare("
        SELECT s.service, COUNT(st.id) as count 
        FROM affectations s
        JOIN stagiaires st ON s.stagiaire_id = st.id
        WHERE st.statut = 'accepte' 
        GROUP BY s.service 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $services_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $statistiques['services'] = [
        'labels' => array_column($services_data, 'service'),
        'values' => array_column($services_data, 'count')
    ];
    


// Progression des stages (12 derniers mois, groupé par mois)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(date_inscription, '%Y-%m') as mois, COUNT(*) as count
    FROM stagiaires
    WHERE date_inscription >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY mois
    ORDER BY mois;
");
$stmt->execute();
$progression_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statistiques['progression'] = [
    'labels' => array_column($progression_data, 'mois'),
    'values' => array_column($progression_data, 'count')
];

    echo json_encode([
        'success' => true,
        'statistiques' => $statistiques
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 