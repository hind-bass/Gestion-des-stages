<?php

namespace core;

use PDO;
use PDOException;
use Utils\Security;

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $fillable = [];
    protected $hidden = [];
    protected $primaryKey = 'id';
    protected $timestamps = true;

    public function __construct()
    {
        $this->connect();
    }

    protected function connect()
    {
        try {
            $config = require __DIR__ . '/../config/database.php';
            
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $this->db = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function find($id, array $columns = ['*'])
    {
        $columns = $this->prepareColumns($columns);
        $sql = "SELECT {$columns} FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $data = $this->filterFillable($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }

    public function update($id, array $data)
    {
        $data = $this->filterFillable($data);
        $updates = [];
        
        foreach (array_keys($data) as $key) {
            $updates[] = "{$key} = :{$key}";
        }
        
        $setClause = implode(', ', $updates);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }

    public function delete($id, $softDelete = true)
    {
        if ($softDelete && $this->timestamps) {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }
        
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    public function all(array $columns = ['*'])
    {
        $columns = $this->prepareColumns($columns);
        $sql = "SELECT {$columns} FROM {$this->table}";
        
        if ($this->timestamps && $this->hasColumn('deleted_at')) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginate($perPage = 15, $page = 1, array $columns = ['*'])
    {
        $columns = $this->prepareColumns($columns);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT {$columns} FROM {$this->table}";
        
        if ($this->timestamps && $this->hasColumn('deleted_at')) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        
        $sql .= " LIMIT :offset, :perPage";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function filterFillable(array $data)
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function prepareColumns(array $columns)
    {
        if (in_array('*', $columns)) {
            return '*';
        }
        
        return implode(', ', array_diff($columns, $this->hidden));
    }

    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollBack()
    {
        return $this->db->rollBack();
    }

    /**
     * Exécuter une requête avec conditions
     */
    public function where($conditions = null, $params = [], $orderBy = null, $limit = null)
    {
        $columns = $this->prepareColumns(['*']);
        $sql = "SELECT {$columns} FROM {$this->table}";
        
        if ($this->timestamps && $this->hasColumn('deleted_at')) {
            $sql .= " WHERE deleted_at IS NULL";
            if ($conditions) {
                $sql .= " AND ({$conditions})";
            }
        } elseif ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exécuter une requête avec conditions et retourner un seul résultat
     */
    public function whereOne($conditions, $params = [])
    {
        $columns = $this->prepareColumns(['*']);
        $sql = "SELECT {$columns} FROM {$this->table}";
        
        if ($this->timestamps && $this->hasColumn('deleted_at')) {
            $sql .= " WHERE deleted_at IS NULL AND ({$conditions})";
        } else {
            $sql .= " WHERE {$conditions}";
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Exécuter une requête SQL personnalisée
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exécuter une requête SQL personnalisée et retourner un seul résultat
     */
    public function queryOne($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Compter les enregistrements avec conditions
     */
    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if ($this->timestamps && $this->hasColumn('deleted_at')) {
            $sql .= " WHERE deleted_at IS NULL";
            if ($conditions) {
                $sql .= " AND ({$conditions})";
            }
        } elseif ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Vérifier si une colonne existe dans la table
     */
    protected function hasColumn($columnName)
    {
        try {
            $sql = "SHOW COLUMNS FROM {$this->table} LIKE ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$columnName]);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
