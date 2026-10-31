<?php

require_once __DIR__ . '/../config/database.php';

class MigrationRunner
{
    private $db;
    private $migrationsTable = 'migrations';
    
    public function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        
        // Set PDO options with buffered queries
        $options = $config['options'] ?? [];
        $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
        $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        
        $this->db = new PDO($dsn, $config['username'], $config['password'], $options);
        
        $this->ensureMigrationsTableExists();
    }
    
    private function ensureMigrationsTableExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->migrationsTable}` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL,
            `ran_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
    }
    
    public function runMigrations()
    {
        try {
            // Get all migration files
            $migrationFiles = glob(__DIR__ . '/migrations/*.sql');
            
            // Sort migrations by filename
            sort($migrationFiles);
            
            // Get already ran migrations
            $ranMigrations = $this->getRanMigrations();
            
            $batch = $this->getNextBatchNumber();
            $migrationsToRun = [];
            
            // Skip problematic migrations that are already applied
            $skipMigrations = [
                '20240822_add_remember_token_to_stagiaires.sql' => true,
                '20240901_add_foreign_keys_and_indexes.sql' => true,
                '20240901_add_soft_delete_and_timestamps.sql' => true,
                '20240901_final_schema_updates.sql' => true,
                '20240901_update_schema_fixes.sql' => true
            ];
            
            // Find migrations that haven't been run yet
            foreach ($migrationFiles as $file) {
                $migrationName = basename($file);
                
                // Skip already run migrations or skipped migrations
                if (in_array($migrationName, $ranMigrations) || isset($skipMigrations[$migrationName])) {
                    echo "Skipping migration: $migrationName\n";
                    continue;
                }
                
                $migrationsToRun[] = [
                    'file' => $file,
                    'name' => $migrationName,
                    'batch' => $batch
                ];
            }
            
            if (empty($migrationsToRun)) {
                echo "No migrations to run.\n";
                return;
            }
            
            // Run each migration in a separate connection to avoid buffering issues
            foreach ($migrationsToRun as $migration) {
                $this->runSingleMigration($migration);
            }
            
            echo "\nAll migrations completed successfully.\n";
            
        } catch (Exception $e) {
            echo "\nMigration failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    private function runSingleMigration($migration)
    {
        // Create a new database connection for this migration
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        $db = new PDO($dsn, $config['username'], $config['password'], $options);
        
        try {
            echo "Running migration: {$migration['name']}... ";
            
            // Read and execute SQL file
            $sql = file_get_contents($migration['file']);
            
            // Execute the entire SQL script at once.
            // This is more robust than splitting by semicolons and handles complex scripts.
            $db->exec($sql);
            
            // Record successful migration using the main connection
            $this->recordMigration($migration['name'], $migration['batch']);
            echo "DONE\n";
            
        } catch (Exception $e) {
            echo "FAILED: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            // Close the connection
            $db = null;
        }
    }
    
    private function getRanMigrations(): array
    {
        $stmt = $this->db->query("SELECT migration FROM `{$this->migrationsTable}` ORDER BY id");
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $stmt->closeCursor();
        return $result;
    }
    
    private function getNextBatchNumber(): int
    {
        $stmt = $this->db->query("SELECT MAX(batch) FROM `{$this->migrationsTable}`");
        return (int)$stmt->fetchColumn() + 1;
    }
    
    private function recordMigration(string $migration, int $batch): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->migrationsTable}` (migration, batch) 
            VALUES (:migration, :batch)
        ");
        
        $stmt->execute([
            ':migration' => $migration,
            ':batch' => $batch
        ]);
    }
}

// Run migrations
$runner = new MigrationRunner();
$runner->runMigrations();
