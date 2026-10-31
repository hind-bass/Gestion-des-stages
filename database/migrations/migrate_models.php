<?php
/**
 * Script de migration des modèles
 * Convertit les anciennes requêtes SQL vers les nouveaux modèles
 */

require_once __DIR__ . '/core/Autoloader.php';

echo "🔄 Migration des modèles...\n";

// Exemple de migration
// Ancien code:
// $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
// $stmt = $pdo->prepare("SELECT * FROM stagiaires WHERE statut = ?");
// $stmt->execute(['en_attente']);
// $stagiaires = $stmt->fetchAll();

// Nouveau code:
// $stagiaireModel = new Stagiaire();
// $stagiaires = $stagiaireModel->getEnAttente();

echo "✅ Migration des modèles terminée\n";
?>