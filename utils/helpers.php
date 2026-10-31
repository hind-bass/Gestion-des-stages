<?php
include 'config.php';
include 'email_config.php';

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_type']);
}

// Fonction pour vérifier le type d'utilisateur
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

// Fonction pour obtenir les informations de l'utilisateur connecté
function getCurrentUser() {
    $type = getUserType();
    if (!$type) return null;
    
    $id_field = $type . '_id';
    $email_field = $type . '_email';
    
    return [
        'id' => $_SESSION[$id_field] ?? null,
        'email' => $_SESSION[$email_field] ?? null,
        'type' => $type
    ];
}

// Fonction pour rediriger avec un message
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit();
}

// Fonction pour afficher les messages
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        $message = $_SESSION['message'];
        
        $color_class = [
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700'
        ];
        
        $class = $color_class[$type] ?? $color_class['info'];
        
        echo "<div class='$class border px-4 py-3 rounded relative mb-4' role='alert'>";
        echo "<span class='block sm:inline'>$message</span>";
        echo "</div>";
        
        unset($_SESSION['message'], $_SESSION['message_type']);
    }
}

// Fonction pour valider un fichier uploadé
function validateUploadedFile($file, $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], $max_size = 5242880) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors de l'upload du fichier";
        return $errors;
    }
    
    // Vérifier la taille
    if ($file['size'] > $max_size) {
        $errors[] = "Le fichier est trop volumineux (max " . ($max_size / 1024 / 1024) . "MB)";
    }
    
    // Vérifier le type
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        $errors[] = "Type de fichier non autorisé. Types autorisés : " . implode(', ', $allowed_types);
    }
    
    return $errors;
}

// Fonction pour sauvegarder un fichier uploadé
function saveUploadedFile($file, $destination_dir, $new_name = null) {
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $new_name ? $new_name . '.' . $extension : uniqid() . '.' . $extension;
    $filepath = $destination_dir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    return false;
}

// Fonction pour obtenir les services disponibles
function getServices() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM services WHERE statut = 'actif' ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir les stagiaires d'un responsable
function getStagiairesByResponsable($responsable_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, a.date_affectation, a.statut as statut_affectation
            FROM stagiaires s
            LEFT JOIN affectations a ON s.id = a.id_stagiaire
            WHERE a.id_responsable = ? AND a.statut = 'active'
            ORDER BY s.nom, s.prenom
        ");
        $stmt->execute([$responsable_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir les tâches d'un stagiaire
function getTachesByStagiaire($stagiaire_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM taches_stagiaires 
            WHERE id_stagiaire = ? 
            ORDER BY date_creation DESC
        ");
        $stmt->execute([$stagiaire_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir les remarques d'un stagiaire
function getRemarquesByStagiaire($stagiaire_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, resp.nom as nom_responsable, resp.prenom as prenom_responsable
            FROM remarques r
            JOIN responsables resp ON r.id_responsable = resp.id
            WHERE r.id_stagiaire = ?
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute([$stagiaire_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour vérifier si un responsable peut accepter plus de stagiaires
function canResponsableAcceptMoreStagiaires($responsable_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count, s.capacite_max
            FROM affectations a
            JOIN services s ON s.responsable_id = a.id_responsable
            WHERE a.id_responsable = ? AND a.statut = 'active'
            GROUP BY s.capacite_max
        ");
        $stmt->execute([$responsable_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) return true; // Pas de service assigné
        
        return $result['count'] < $result['capacite_max'];
    } catch (PDOException $e) {
        return false;
    }
}

// Fonction pour générer un PDF d'attestation
function generateAttestationPDF($stagiaire_id, $type = 'debut_stage') {
    // Cette fonction utilisera une bibliothèque comme TCPDF ou FPDF
    // Pour l'instant, on simule la génération
    
    $numero = generateNumeroAttestation($type);
    
    // Enregistrer dans la base de données
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO attestations (id_stagiaire, type, numero_attestation, chemin_pdf)
            VALUES (?, ?, ?, ?)
        ");
        
        $chemin_pdf = "uploads/attestations/{$numero}.pdf";
        $stmt->execute([$stagiaire_id, $type, $numero, $chemin_pdf]);
        
        return $numero;
    } catch (PDOException $e) {
        return false;
    }
}

// Fonction pour paginer les résultats
function paginateResults($query, $params = [], $page = 1, $per_page = 10) {
    global $pdo;
    
    try {
        // Compter le total
        $count_query = "SELECT COUNT(*) FROM ($query) as count_table";
        $stmt = $pdo->prepare($count_query);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();
        
        // Calculer les limites
        $total_pages = ceil($total / $per_page);
        $offset = ($page - 1) * $per_page;
        
        // Récupérer les données
        $data_query = $query . " LIMIT $per_page OFFSET $offset";
        $stmt = $pdo->prepare($data_query);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'total' => $total,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'per_page' => $per_page
        ];
    } catch (PDOException $e) {
        return [
            'data' => [],
            'total' => 0,
            'total_pages' => 0,
            'current_page' => 1,
            'per_page' => $per_page
        ];
    }
}

// Fonction pour nettoyer les données d'entrée
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Fonction pour valider une date
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Fonction pour formater une date
function formatDate($date, $format = 'd/m/Y') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

// Fonction pour obtenir le statut coloré
function getStatusBadge($status) {
    $colors = [
        'en_attente' => 'bg-yellow-100 text-yellow-800',
        'valide' => 'bg-green-100 text-green-800',
        'refuse' => 'bg-red-100 text-red-800',
        'en_cours' => 'bg-blue-100 text-blue-800',
        'terminee' => 'bg-gray-100 text-gray-800',
        'a_faire' => 'bg-orange-100 text-orange-800',
        'en_retard' => 'bg-red-100 text-red-800'
    ];
    
    $color = $colors[$status] ?? 'bg-gray-100 text-gray-800';
    $label = ucfirst(str_replace('_', ' ', $status));
    
    return "<span class='px-2 py-1 text-xs font-medium rounded-full $color'>$label</span>";
}

// Fonction pour vérifier les permissions
function hasPermission($permission) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    $permissions = [
        'stagiaire' => ['view_own_profile', 'upload_documents', 'view_tasks', 'view_remarks'],
        'responsable' => ['manage_stagiaires', 'manage_tasks', 'manage_remarks', 'generate_attestations'],
        'administrateur' => ['manage_all', 'create_responsables', 'validate_stagiaires', 'manage_services']
    ];
    
    return in_array($permission, $permissions[$user['type']] ?? []);
}
?> 