<?php
/**
 * Configuration de l'application
 */

return [
    // Configuration de l'URL de base
    'base_url' => 'http://localhost/Projet4/public/',
    'base_path' => '/Projet4/public',
    
    // Configuration des dossiers
    'public_path' => '/Projet4/public',
    'assets_path' => '/Projet4/public/assets',
    
    // Configuration de la base de données
    'database' => [
        'host' => 'localhost',
        'dbname' => 'gestion_stages',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    
    // Configuration des sessions
    'session' => [
        'name' => 'gestion_stages_session',
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ],
    
    // Configuration des uploads
    'uploads' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        'path' => __DIR__ . '/../public/uploads'
    ],
    
    // Configuration des attestations
    'attestations' => [
        'path' => __DIR__ . '/../public/uploads/attestations',
        'logo_path' => __DIR__ . '/../public/assets/images/logo_commune.png'
    ]
];
?>
