<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM responsables WHERE id = ?");
    $stmt->execute([$id]);
    $responsable = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($responsable) {
        echo json_encode(['success' => true, 'responsable' => $responsable]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Responsable non trouvé']);
    }
    exit();
}

try {
    // Récupérer tous les responsables avec le nombre de stagiaires

$stmt = $pdo->prepare("
    SELECT r.*, 
           CONCAT(r.prenom, ' ', r.nom) as nom_complet,
           COUNT(s.id) as nombre_stagiaires_actuel,
           20 as nombre_stagiaires_max,
           CASE 
               WHEN COUNT(s.id) >= 20 THEN 'complet'
               ELSE 'disponible'
           END as statut
    FROM responsables r 
    LEFT JOIN stagiaires s ON r.id = s.responsable_id AND s.statut = 'accepte'
    GROUP BY r.id 
    ORDER BY r.nom, r.prenom
");
    $stmt->execute();
    $responsables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($responsables as &$responsable) {
        $responsable['date_creation'] = date('d/m/Y H:i', strtotime($responsable['date_creation']));
        
        // Traduire le statut
        $statuts = [
            'disponible' => 'Disponible',
            'complet' => 'Complet'
        ];
        $responsable['statut'] = $statuts[$responsable['statut']] ?? $responsable['statut'];
    }
    
    echo json_encode([
        'success' => true,
        'responsables' => $responsables
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 