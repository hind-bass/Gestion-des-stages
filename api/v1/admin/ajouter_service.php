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

// Vérifier les données reçues
$required_fields = ['nom', 'description'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'message' => "Le champ '$field' est obligatoire"]);
        exit();
    }
}

$nom = trim($_POST['nom']);
$description = trim($_POST['description']);
$nombre_max = intval($_POST['nombre_stagiaires_max'] ?? 20);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si le service existe déjà
    $stmt = $pdo->prepare("SELECT id FROM services WHERE nom = ?");
    $stmt->execute([$nom]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ce nom de service est déjà utilisé']);
        exit();
    }
    
    // Insérer le nouveau service
    $stmt = $pdo->prepare("
        INSERT INTO services (nom, description, nombre_stagiaires_max)
        VALUES (?, ?, ?)
    ");
    
    $stmt->execute([$nom, $description, $nombre_max]);
    
    $service_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Service ajouté avec succès',
        'service_id' => $service_id
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>