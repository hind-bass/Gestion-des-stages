<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger l'autoloader
require_once __DIR__ . '/../core/Autoloader.php';

// Charger la configuration
$config = require_once __DIR__ . '/../config/app.php';

// Définir la constante BASE_URL si elle n'est pas déjà définie
if (!defined('BASE_URL')) {
    define('BASE_URL', $config['base_url']);
}

// Récupérer la route demandée
$request = $_SERVER['REQUEST_URI'];
$request = parse_url($request, PHP_URL_PATH);

// Calcule le chemin de base de l'application pour le retirer de l'URI
$base_path = dirname($_SERVER['SCRIPT_NAME']);

// Debug: Afficher les informations de routage
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
error_log("Base path: " . $base_path);

// Gère le cas où l'application est dans un sous-dossier
// Si le chemin de base contient 'public', on le retire pour avoir le bon chemin
if (strpos($base_path, '/public') !== false) {
    $base_path = str_replace('/public', '', $base_path);
}

if ($base_path === '/' || $base_path === '\\') {
    $base_path = '';
}

// Retirer le chemin de base de la requête
// Retirer le chemin de base de la requête
$request = substr($request, strlen($base_path));

// Si la requête restante commence encore par /public, la retirer (cas WAMP sous-dossier)
if (strpos($request, '/public/') === 0) {
    $request = substr($request, strlen('/public/'));
} elseif ($request === '/public') {
    $request = '/';
}

$request = trim($request, '/');

// Si la requête est vide, c'est la page d'accueil
if (empty($request)) {
    $request = '';
}

error_log("Request après traitement: " . $request);

// Routes disponibles
$routes = [
    '' => ['AccueilController', 'index'],
    'accueil' => ['AccueilController', 'index'],
    'contact' => ['AccueilController', 'contact'],

    // Auth
    'inscription' => ['AuthController', 'inscription'],
    'login' => ['AuthController', 'login'],
    'logout' => ['AuthController', 'logout'],
    'responsable/login' => ['ResponsableAuthController', 'login'],
    'responsable/logout' => ['ResponsableAuthController', 'logout'],

    // Admin Auth
    'admin/login' => ['AdminAuthController', 'login'],
    'admin/logout' => ['AdminAuthController', 'logout'],

    // Admin
    'admin' => ['AdminController', 'dashboard'],
    'admin/dashboard' => ['DashboardController', 'index'],
    'admin/stagiaires' => ['AdminController', 'stagiaires'],
    'admin/stagiaires/{id}' => ['AdminController', 'show'],
    'admin/responsables' => ['AdminController', 'responsables'],
    'admin/statistiques' => ['AdminController', 'statistiques'],
    'admin/attestations' => ['AdminController', 'attestations'],
    'admin/valider-stagiaire' => ['AdminController', 'validerStagiaire'],
    'admin/refuser-stagiaire' => ['AdminController', 'refuserStagiaire'],
    'admin/export' => ['AdminController', 'export'],
    'admin/generer-attestation-debut/{id}' => ['AdminController', 'genererAttestationDebut'],
    'admin/telecharger-attestation/{type}/{id}' => ['AdminController', 'telechargerAttestation'],

    // Responsable
    'responsable' => ['ResponsableController', 'dashboard'],
    'responsable/stagiaires' => ['ResponsableController', 'stagiaires'],
    'responsable/stagiaires/{id}' => ['ResponsableController', 'showStagiaire'],
    'responsable/taches' => ['ResponsableController', 'taches'],
    'responsable/taches/creer' => ['ResponsableController', 'showCreerTacheForm'],
    'responsable/creer-tache' => ['ResponsableController', 'creerTache'], // POST
    'responsable/evaluations' => ['ResponsableController', 'evaluations'],
    'responsable/evaluations/{id}' => ['ResponsableController', 'showEvaluations'],
    'responsable/rapports' => ['ResponsableController', 'rapports'],
    'responsable/attestations' => ['ResponsableController', 'attestations'],
    'responsable/generer-attestation-fin/{id}' => ['ResponsableController', 'genererAttestationFin'],
    'responsable/telecharger-attestation-fin/{id}' => ['ResponsableController', 'telechargerAttestationFin'],

    // Stagiaire
    'stagiaire' => ['StagiaireController', 'dashboard'],
    'stagiaire/inscription' => ['AuthController', 'inscription'],
    'stagiaire/profil' => ['StagiaireController', 'profil'],
    'stagiaire/documents' => ['StagiaireController', 'documents'],
    'stagiaire/taches' => ['StagiaireController', 'taches'],
    'stagiaire/attestations' => ['StagiaireController', 'attestations'],
    'stagiaire/attestations/fin' => ['StagiaireController', 'attestationFin'],
    'stagiaire/evaluations' => ['StagiaireController', 'evaluations'],
    'stagiaire/telecharger-attestation/{id}' => ['StagiaireController', 'telechargerAttestation'],
    'stagiaire/afficher-attestation/{id}' => ['StagiaireController', 'afficherAttestation'],
];

// Trouver la route correspondante
$controllerName = null;
$action = null;
$params = [];

foreach ($routes as $route => $handler) {
    // Convertir la route en une expression régulière pour capturer les paramètres
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
    $pattern = "#^" . $pattern . "$#";

    if (preg_match($pattern, $request, $matches)) {
        $controllerName = $handler[0];
        $action = $handler[1];
        
        // Filtrer les correspondances pour ne garder que les paramètres nommés
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        break;
    }
}

// Si aucune route ne correspond, afficher une erreur 404
if ($controllerName === null) {
    header("HTTP/1.0 404 Not Found");
    echo '404 Not Found';
    exit;
}

// Inclure le fichier du contrôleur
$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Obtenir le nom complet de la classe avec l'espace de noms
    $fullControllerName = 'controllers\\' . $controllerName;
    
    // Vérifier si le contrôleur et la méthode existent
    if (class_exists($fullControllerName) && method_exists($fullControllerName, $action)) {
        $controller = new $fullControllerName();
        call_user_func_array([$controller, $action], $params);
    } else {
        // Journalisation pour le débogage
        error_log("Controller ou méthode non trouvés: " . $fullControllerName . "->" . $action);
        
        header("HTTP/1.0 500 Internal Server Error");
        echo '500 Internal Server Error';
        exit;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo '404 Not Found - Controller file not found: ' . $controllerFile;
    exit;
}
?>
