<?php
/**
 * Classe Database - Gestion centralisée de la connexion à la base de données
 * Pattern Singleton pour éviter les connexions multiples
 */

namespace core;

class Database
{
    private static $instance = null;
    private $pdo;
    private $config;

    private function __construct()
    {
        // Charger la configuration depuis config.php
        $this->config = [
            'host' => 'localhost',
            'dbname' => 'gestion_stages',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ];
        
        $this->connect();
    }

    /**
     * Obtenir l'instance unique de la classe
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Établir la connexion à la base de données
     */
    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
            
            $this->pdo = new \PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (\PDOException $e) {
            throw new \Exception('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir la connexion PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Exécuter une requête préparée
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            throw new \Exception('Erreur lors de l\'exécution de la requête: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer une seule ligne
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Récupérer toutes les lignes
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insérer une ligne et retourner l'ID
     */
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Mettre à jour des données
     */
    public function update($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Supprimer des données
     */
    public function delete($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Commencer une transaction
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Valider une transaction
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Annuler une transaction
     */
    public function rollback()
    {
        return $this->pdo->rollback();
    }

    /**
     * Vérifier si une transaction est en cours
     */
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Échapper une chaîne pour éviter les injections SQL
     */
    public function quote($value)
    {
        return $this->pdo->quote($value);
    }

    /**
     * Obtenir les informations sur la dernière erreur
     */
    public function getLastError()
    {
        $error = $this->pdo->errorInfo();
        return $error[2] ?? null;
    }

    /**
     * Fermer la connexion
     */
    public function close()
    {
        $this->pdo = null;
        self::$instance = null;
    }

    /**
     * Empêcher le clonage de l'instance
     */
    private function __clone()
    {
    }

    /**
     * Empêcher la désérialisation
     */
    public function __wakeup()
    {
        throw new \Exception("La désérialisation n'est pas autorisée pour cette classe");
    }
}
?>