<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant que responsable
if (!isset($_SESSION['responsable_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $responsable_id = $_SESSION['responsable_id'];
    
    // Récupérer les décisions prises par le responsable
    $stmt = $pdo->prepare("
        SELECT d.*, 
               CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire_nom,
               s.email as stagiaire_email,
               s.numero_stagiaire
        FROM decisions d
        JOIN stagiaires s ON d.stagiaire_id = s.id
        WHERE d.responsable_id = ?
        ORDER BY d.date_decision DESC
    ");
    $stmt->execute([$responsable_id]);
    $decisions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les données
    foreach ($decisions as &$decision) {
        $decision['date_decision'] = date('d/m/Y H:i', strtotime($decision['date_decision']));
        
        // Traduire la décision
        $decision['decision_libelle'] = $decision['decision'] === 'valide' ? 'Validé' : 'Refusé';
        
        // Formater la note
        if ($decision['note_finale']) {
            $decision['note_formatee'] = number_format($decision['note_finale'], 2) . '/20';
        } else {
            $decision['note_formatee'] = 'Non noté';
        }
    }
    
    echo json_encode([
        'success' => true,
        'decisions' => $decisions
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?> 