<?php
/**
 * Autoloader simplifié pour charger les classes
 */

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/../';

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});

// Définir les constantes de chemin
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

if (!defined('CORE_PATH')) {
    define('CORE_PATH', ROOT_PATH . '/core');
}

if (!defined('CONTROLLERS_PATH')) {
    define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
}

if (!defined('MODELS_PATH')) {
    define('MODELS_PATH', ROOT_PATH . '/models');
}

if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH', ROOT_PATH . '/views');
}

// Debug: Afficher les chemins définis
error_log("Autoloader initialisé - ROOT_PATH: " . ROOT_PATH);
error_log("Autoloader initialisé - CORE_PATH: " . CORE_PATH);
?>