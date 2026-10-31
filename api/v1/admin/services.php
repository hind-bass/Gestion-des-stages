<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            // Récupérer la liste des services avec le nombre de stagiaires
            $stmt = $pdo->prepare("
                SELECT s.*, 
                       COUNT(st.id) as nombre_stagiaires,
                       CONCAT(r.prenom, ' ', r.nom) as responsable
                FROM services s
                LEFT JOIN responsables r ON s.responsable_id = r.id
                LEFT JOIN stagiaires st ON s.nom = st.service_demande AND st.statut = 'accepte'
                GROUP BY s.id
                ORDER BY s.nom
            ");
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'services' => $services
            ]);
            break;
            
        case 'details':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                exit();
            }
            
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$service) {
                echo json_encode(['success' => false, 'message' => 'Service non trouvé']);
                exit();
            }
            
            echo json_encode([
                'success' => true,
                'service' => $service
            ]);
            break;
            
        default:
            // Par défaut, retourner la liste
            $stmt = $pdo->prepare("
                SELECT s.*, 
                       COUNT(st.id) as nombre_stagiaires,
                       COALESCE(CONCAT(r.prenom, ' ', r.nom), 'Non assigné') as responsable
                FROM services s
                LEFT JOIN responsables r ON s.responsable_id = r.id
                LEFT JOIN stagiaires st ON s.nom = st.service_demande AND st.statut = 'accepte'
                GROUP BY s.id
                ORDER BY s.nom
            ");
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'services' => $services
            ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>