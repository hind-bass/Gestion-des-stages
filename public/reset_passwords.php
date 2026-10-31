<?php
// Script de reset des mots de passe (à supprimer après usage)
// Définit tous les mots de passe sur "password" pour administrateurs, responsables et stagiaires

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../core/Autoloader.php';

try {
    $db = \core\Database::getInstance();

    // Hash bcrypt correspondant au mot de passe "password"
    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    $queries = [
        [
            'sql' => 'UPDATE administrateurs SET mot_de_passe = :hash',
            'label' => 'administrateurs'
        ],
        [
            'sql' => 'UPDATE responsables SET mot_de_passe = :hash',
            'label' => 'responsables'
        ],
        [
            'sql' => 'UPDATE stagiaires SET mot_de_passe = :hash',
            'label' => 'stagiaires'
        ],
    ];

    $results = [];
    foreach ($queries as $q) {
        $count = $db->update($q['sql'], ['hash' => $hash]);
        $results[] = $q['label'] . ': ' . $count . ' lignes mises à jour';
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo "Reset des mots de passe terminé.\n";
    echo implode("\n", $results);
    echo "\nMot de passe défini sur: password\n";
} catch (Exception $e) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo 'Erreur: ' . $e->getMessage();
}
?>
 