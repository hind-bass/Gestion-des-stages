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
$required_fields = ['nom', 'prenom', 'email', 'service'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'message' => "Le champ '$field' est obligatoire"]);
        exit();
    }
}

$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$email = trim($_POST['email']);
$service = trim($_POST['service']);
$telephone = trim($_POST['telephone'] ?? '');
$nombre_max = intval($_POST['nombre_stagiaires_max'] ?? 20);

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM responsables WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cette adresse email est déjà utilisée']);
        exit();
    }
    
    // Générer un mot de passe par défaut
    $password_default = 'responsable123'; // Vous pouvez générer un mot de passe aléatoire
    $password_hash = password_hash($password_default, PASSWORD_DEFAULT);
    
    // Insérer le nouveau responsable
    $stmt = $pdo->prepare("
        INSERT INTO responsables (nom, prenom, email, password, service, telephone, nombre_stagiaires_max, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'disponible')
    ");
    
    $stmt->execute([
        $nom, $prenom, $email, $password_hash, 
        $service, $telephone, $nombre_max
    ]);
    
    $responsable_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Responsable créé avec succès',
        'responsable_id' => $responsable_id,
        'info' => "Mot de passe par défaut: $password_default"
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>