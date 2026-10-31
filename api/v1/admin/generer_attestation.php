<?php
session_start();
include '../config.php';

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['stagiaire_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID du stagiaire manquant']);
    exit();
}

$stagiaire_id = (int)$input['stagiaire_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les informations du stagiaire
    $stmt = $pdo->prepare("
        SELECT s.*, 
               COALESCE(s.service_demande, 'Service non spécifié') as service_demande
        FROM stagiaires s 
        WHERE s.id = ?
    ");
    $stmt->execute([$stagiaire_id]);
    $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$stagiaire) {
        echo json_encode(['success' => false, 'message' => 'Stagiaire non trouvé']);
        exit();
    }
    
    // Vérifier que le stagiaire est accepté
    if ($stagiaire['statut'] !== 'accepte') {
        echo json_encode(['success' => false, 'message' => 'Le stagiaire doit être accepté pour générer une attestation']);
        exit();
    }
    
    // Générer un numéro d'attestation unique
    $numero_attestation = 'ATT-' . date('Y') . '-' . str_pad($stagiaire_id, 4, '0', STR_PAD_LEFT);
    
    // Créer le dossier pour les attestations s'il n'existe pas
    $upload_dir = '../uploads/attestations/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Charger le template d'attestation
    $template_path = '../attestation_debut_template.php';
    if (!file_exists($template_path)) {
        echo json_encode(['success' => false, 'message' => 'Template d\'attestation non trouvé']);
        exit();
    }
    
    // Lire le template
    $template_content = file_get_contents($template_path);
    
    // Remplacer les variables dans le template
    $replacements = [
        '{{NUMERO_DEMANDE}}' => $stagiaire['numero_stagiaire'] ?? $stagiaire['id'],
        '{{DATE_DEPOT}}' => $stagiaire['date_inscription'] ? date('d/m/Y', strtotime($stagiaire['date_inscription'])) : date('d/m/Y'),
        '{{NOM_FR}}' => strtoupper($stagiaire['nom_fr']),
        '{{PRENOM_FR}}' => ucfirst($stagiaire['prenom_fr']),
        '{{NOM_AR}}' => $stagiaire['nom_ar'] ?? $stagiaire['nom_fr'],
        '{{PRENOM_AR}}' => $stagiaire['prenom_ar'] ?? $stagiaire['prenom_fr'],
        '{{SERVICE_DEMANDE}}' => $stagiaire['service_demande'],
        '{{DATE_DEBUT_SOUHAITEE}}' => $stagiaire['date_debut_souhaitee'] ? date('d/m/Y', strtotime($stagiaire['date_debut_souhaitee'])) : '___/___/_____',
        '{{DATE_FIN_SOUHAITEE}}' => $stagiaire['date_fin_souhaitee'] ? date('d/m/Y', strtotime($stagiaire['date_fin_souhaitee'])) : '___/___/_____',
        '{{DATE_AUJOURDHUI}}' => date('d/m/Y')
    ];
    
    $html_content = str_replace(array_keys($replacements), array_values($replacements), $template_content);
    
    // Générer le PDF avec DOMPDF ou créer un fichier HTML
    $filename = 'attestation_debut_' . $stagiaire_id . '_' . date('Y-m-d') . '.html';
    $filepath = $upload_dir . $filename;
    
    // Sauvegarder le contenu HTML
    if (file_put_contents($filepath, $html_content) === false) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du fichier']);
        exit();
    }
    
    // Vérifier si une attestation existe déjà
    $stmt = $pdo->prepare("SELECT id FROM attestations WHERE stagiaire_id = ? AND type = 'debut'");
    $stmt->execute([$stagiaire_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Mettre à jour l'attestation existante
        $stmt = $pdo->prepare("
            UPDATE attestations 
            SET numero_attestation = ?, chemin_pdf = ?, date_generation = NOW(), statut = 'generee'
            WHERE id = ?
        ");
        $stmt->execute([$numero_attestation, $filepath, $existing['id']]);
    } else {
        // Créer une nouvelle attestation
        $stmt = $pdo->prepare("
            INSERT INTO attestations (stagiaire_id, type, numero_attestation, chemin_pdf, statut)
            VALUES (?, 'debut', ?, ?, 'generee')
        ");
        $stmt->execute([$stagiaire_id, $numero_attestation, $filepath]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Attestation générée avec succès',
        'numero_attestation' => $numero_attestation,
        'chemin_fichier' => $filepath
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur lors de la génération d'attestation: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>