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
    
    // Récupérer le rapport du stagiaire (le plus récent)
    $stmt = $pdo->prepare("
        SELECT id, titre, description, chemin_fichier, date_depot, statut, commentaire_responsable
        FROM rapports 
        WHERE stagiaire_id = ? 
        ORDER BY date_depot DESC 
        LIMIT 1
    ");
    $stmt->execute([$stagiaire_id]);
    $rapport = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($rapport) {
        // Formater les données
        $rapport['date_depot'] = date('d/m/Y H:i', strtotime($rapport['date_depot']));
        
        // Traduire le statut
        $statuts = [
            'en_attente' => 'En attente',
            'valide' => 'Validé',
            'refuse' => 'Refusé'
        ];
        $rapport['statut'] = $statuts[$rapport['statut']] ?? $rapport['statut'];
    }
    
    echo json_encode([
        'success' => true,
        'rapport' => $rapport
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 