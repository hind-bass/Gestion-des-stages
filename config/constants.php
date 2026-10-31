<?php
/**
 * Constantes de l'application
 */

// Statuts des stagiaires
define('STAGIAIRE_STATUS_EN_ATTENTE', 'en_attente');
define('STAGIAIRE_STATUS_VALIDE', 'valide');
define('STAGIAIRE_STATUS_REFUSE', 'refuse');

// Statuts des stages
define('STAGE_STATUS_EN_COURS', 'en_cours');
define('STAGE_STATUS_TERMINE', 'termine');
define('STAGE_STATUS_ANNULE', 'annule');

// Statuts des tâches
define('TACHE_STATUS_A_FAIRE', 'a_faire');
define('TACHE_STATUS_EN_COURS', 'en_cours');
define('TACHE_STATUS_TERMINEE', 'terminee');

// Priorités des tâches
define('TACHE_PRIORITE_BASSE', 'basse');
define('TACHE_PRIORITE_MOYENNE', 'moyenne');
define('TACHE_PRIORITE_HAUTE', 'haute');

// Types de documents
define('DOCUMENT_TYPE_CV', 'cv');
define('DOCUMENT_TYPE_ATTESTATION', 'attestation');
define('DOCUMENT_TYPE_CNI', 'cni');
define('DOCUMENT_TYPE_ENGAGEMENT', 'engagement');
define('DOCUMENT_TYPE_ASSURANCE', 'assurance');
define('DOCUMENT_TYPE_DEMANDE', 'demande_stage');

// Types d'attestations
define('ATTESTATION_TYPE_DEBUT', 'debut');
define('ATTESTATION_TYPE_FIN', 'fin');

// Rôles utilisateurs
define('ROLE_ADMIN', 'admin');
define('ROLE_RESPONSABLE', 'responsable');
define('ROLE_STAGIAIRE', 'stagiaire');

// Messages d'erreur
define('ERROR_DB_CONNECTION', 'Erreur de connexion à la base de données');
define('ERROR_INVALID_CREDENTIALS', 'Email ou mot de passe incorrect');
define('ERROR_UNAUTHORIZED', 'Accès non autorisé');
define('ERROR_FILE_UPLOAD', 'Erreur lors du téléchargement du fichier');
define('ERROR_VALIDATION', 'Données invalides');

// Messages de succès
define('SUCCESS_LOGIN', 'Connexion réussie');
define('SUCCESS_LOGOUT', 'Déconnexion réussie');
define('SUCCESS_CREATE', 'Création réussie');
define('SUCCESS_UPDATE', 'Modification réussie');
define('SUCCESS_DELETE', 'Suppression réussie');

// Limites
define('MAX_STAGIAIRES_PAR_RESPONSABLE', 20);
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('SESSION_TIMEOUT', 3600); // 1 heure

// Formats de date
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i');

// URL de base - sera définie dans index.php si pas déjà définie
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_path = rtrim(str_replace('/public', '', $script_name), '/');
    define('BASE_URL', $protocol . $host . $base_path . '/');
}

// Chemins - définis seulement s'ils n'existent pas déjà
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('CORE_PATH')) define('CORE_PATH', ROOT_PATH . '/core');
if (!defined('MODELS_PATH')) define('MODELS_PATH', ROOT_PATH . '/models');
if (!defined('CONTROLLERS_PATH')) define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
if (!defined('VIEWS_PATH')) define('VIEWS_PATH', ROOT_PATH . '/views');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('UPLOADS_PATH')) define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
if (!defined('LOGS_PATH')) define('LOGS_PATH', ROOT_PATH . '/logs');
if (!defined('UTILS_PATH')) define('UTILS_PATH', ROOT_PATH . '/utils');
?>