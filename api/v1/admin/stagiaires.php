<?php
include '../config.php';
session_start();

// Vérifier que l'utilisateur est administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit();
}

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $action = $_GET['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            // Récupérer la liste des stagiaires
            $status_filter = $_GET['statut'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $sql = "SELECT s.*, 
                           CASE 
                               WHEN s.service_demande IS NOT NULL THEN s.service_demande 
                               ELSE 'Non spécifié' 
                           END as service_demande_display
                    FROM stagiaires s WHERE 1=1";
            $params = [];
            
            if ($status_filter && $status_filter !== '') {
                $sql .= " AND s.statut = ?";
                $params[] = $status_filter;
            }
            
            if ($search && $search !== '') {
                $sql .= " AND (s.nom_fr LIKE ? OR s.prenom_fr LIKE ? OR s.email LIKE ? OR s.cin LIKE ?)";
                $search_param = "%$search%";
                $params[] = $search_param;
                $params[] = $search_param;
                $params[] = $search_param;
                $params[] = $search_param;
            }
            
            $sql .= " ORDER BY s.id DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ajouter des informations supplémentaires
            foreach ($stagiaires as &$stagiaire) {
                // Vérifier les documents
                $stmt_docs = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE stagiaire_id = ?");
                $stmt_docs->execute([$stagiaire['id']]);
                $stagiaire['nombre_documents'] = $stmt_docs->fetchColumn();
                
                // Formatage des dates
                $stagiaire['date_inscription_formatted'] = $stagiaire['date_inscription'] ? 
                    date('d/m/Y H:i', strtotime($stagiaire['date_inscription'])) : 'N/A';
                
                // Statut formaté
                $statuts = [
                    'en_attente' => 'En attente',
                    'valide' => 'Validé',
                    'accepte' => 'Accepté',
                    'refuse' => 'Refusé'
                ];
                $stagiaire['statut_display'] = $statuts[$stagiaire['statut']] ?? $stagiaire['statut'];
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $stagiaires,
                'count' => count($stagiaires)
            ]);
            break;
            
        case 'details':
            // Récupérer les détails d'un stagiaire
            $id = intval($_GET['id']);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID invalide']);
                exit();
            }
            
            $stmt = $pdo->prepare("SELECT * FROM stagiaires WHERE id = ?");
            $stmt->execute([$id]);
            $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stagiaire) {
                echo json_encode(['success' => false, 'error' => 'Stagiaire non trouvé']);
                exit();
            }
            
            // Récupérer les documents
            $stmt = $pdo->prepare("
                SELECT id, type_document, nom_fichier, chemin_fichier, statut, date_upload
                FROM documents 
                WHERE stagiaire_id = ? 
                ORDER BY date_upload DESC
            ");
            $stmt->execute([$id]);
            $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Récupérer les remarques
            $stmt = $pdo->prepare("
                SELECT r.*, CONCAT(res.prenom, ' ', res.nom) as responsable_nom
                FROM remarques r 
                LEFT JOIN stages s ON r.stage_id = s.id 
                LEFT JOIN responsables res ON r.responsable_id = res.id
                WHERE s.stagiaire_id = ? 
                ORDER BY r.date_creation DESC
            ");
            $stmt->execute([$id]);
            $remarques = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Récupérer les attestations
            $stmt = $pdo->prepare("
                SELECT * FROM attestations 
                WHERE stagiaire_id = ? 
                ORDER BY date_generation DESC
            ");
            $stmt->execute([$id]);
            $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stagiaire['documents'] = $documents;
            $stagiaire['remarques'] = $remarques;
            $stagiaire['attestations'] = $attestations;
            
            echo json_encode(['success' => true, 'data' => $stagiaire]);
            break;
            
        case 'accepter':
            $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID invalide']);
                exit();
            }
            
            // Vérifier si le stagiaire existe
            $stmt = $pdo->prepare("SELECT * FROM stagiaires WHERE id = ?");
            $stmt->execute([$id]);
            $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stagiaire) {
                echo json_encode(['success' => false, 'error' => 'Stagiaire non trouvé']);
                exit();
            }
            
            // Générer un numéro unique si pas déjà attribué
            if (empty($stagiaire['numero_stagiaire'])) {
                $year = date('Y');
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) FROM stagiaires 
                    WHERE YEAR(COALESCE(date_validation, NOW())) = ? 
                    AND numero_stagiaire IS NOT NULL
                ");
                $stmt->execute([$year]);
                $count = $stmt->fetchColumn() + 1;
                $numero = sprintf("STG%s%04d", $year, $count);
                
                // Mettre à jour avec le numéro et accepter
                $stmt = $pdo->prepare("
                    UPDATE stagiaires 
                    SET numero_stagiaire = ?, statut = 'accepte', date_validation = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$numero, $id]);
            } else {
                // Juste accepter
                $stmt = $pdo->prepare("UPDATE stagiaires SET statut = 'accepte' WHERE id = ?");
                $stmt->execute([$id]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Stagiaire accepté avec succès']);
            break;
            
        case 'refuser':
            $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
            $motif = $_POST['motif'] ?? 'Aucun motif spécifié';
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID invalide']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE stagiaires SET statut = 'refuse', motif_refus = ? WHERE id = ?");
            $stmt->execute([$motif, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Stagiaire refusé']);
            break;
            
        case 'stats':
            // Statistiques des stagiaires
            $stats = [];
            
            // Total stagiaires
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires");
            $stmt->execute();
            $stats['total'] = $stmt->fetchColumn();
            
            // Stagiaires en attente
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires WHERE statut = 'en_attente'");
            $stmt->execute();
            $stats['en_attente'] = $stmt->fetchColumn();
            
            // Stagiaires acceptés
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires WHERE statut = 'accepte'");
            $stmt->execute();
            $stats['acceptes'] = $stmt->fetchColumn();
            
            // Stagiaires refusés
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM stagiaires WHERE statut = 'refuse'");
            $stmt->execute();
            $stats['refuses'] = $stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
    }
    
} catch (PDOException $e) {
    error_log("Erreur base de données: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>