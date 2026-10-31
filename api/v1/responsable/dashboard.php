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

    // Récupérer les statistiques
    $stats = [];

    // Nombre de stagiaires affectés
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM stagiaires 
        WHERE responsable_id = ? AND statut = 'accepte'
    ");
    $stmt->execute([$responsable_id]);
    $stats['mes_stagiaires'] = $stmt->fetchColumn();

    // Nombre de tâches actives (exemple : taches liées à un stagiaire de ce responsable)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM taches t
        JOIN stagiaires s ON t.stagiaire_id = s.id
        WHERE s.responsable_id = ? AND t.statut = 'accepte'
    ");
    $stmt->execute([$responsable_id]);
    $stats['taches_actives'] = $stmt->fetchColumn();

    // Nombre de remarques (exemple : remarques liées à un stagiaire de ce responsable)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM remarques r
        JOIN stagiaires s ON r.stagiaire_id = s.id
        WHERE s.responsable_id = ?
    ");
    $stmt->execute([$responsable_id]);
    $stats['remarques'] = $stmt->fetchColumn();

    // Nombre d'attestations générées (exemple : attestations de fin pour les stagiaires de ce responsable)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM attestations att
        JOIN stagiaires s ON att.stagiaire_id = s.id
        WHERE s.responsable_id = ? AND att.type = 'fin'
    ");
    $stmt->execute([$responsable_id]);
    $stats['attestations'] = $stmt->fetchColumn();

    // Liste des stagiaires du responsable (pour affichage ou suivi)
    $stmt = $pdo->prepare("
        SELECT * FROM stagiaires 
        WHERE responsable_id = ? AND statut = 'accepte'
    ");
    $stmt->execute([$responsable_id]);
    $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'stagiaires' => $stagiaires // tu peux utiliser ce tableau côté JS pour le suivi
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>