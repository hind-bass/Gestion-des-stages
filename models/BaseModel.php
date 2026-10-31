<?php
/**
 * Classe de base pour tous les modèles
 * Fournit les opérations CRUD communes
 */

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Trouver un enregistrement par son ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Trouver tous les enregistrements
     */
    public function findAll($orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Trouver des enregistrements avec des conditions
     */
    public function where($conditions, $params = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Trouver un enregistrement avec des conditions
     */
    public function whereOne($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions LIMIT 1";
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Compter les enregistrements
     */
    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Créer un nouvel enregistrement
     */
    public function create($data)
    {
        // Filtrer les données selon les champs autorisés
        $data = $this->filterFillable($data);
        
        if (empty($data)) {
            throw new Exception('Aucune donnée valide à insérer');
        }

        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$this->table} ($fieldList) VALUES ($placeholders)";
        
        return $this->db->insert($sql, $data);
    }

    /**
     * Mettre à jour un enregistrement
     */
    public function update($id, $data)
    {
        // Filtrer les données selon les champs autorisés
        $data = $this->filterFillable($data);
        
        if (empty($data)) {
            throw new Exception('Aucune donnée valide à mettre à jour');
        }

        $setParts = [];
        foreach (array_keys($data) as $field) {
            $setParts[] = "$field = :$field";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :id";
        
        $data['id'] = $id;
        return $this->db->update($sql, $data);
    }

    /**
     * Supprimer un enregistrement
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->delete($sql, [$id]);
    }

    /**
     * Supprimer des enregistrements avec des conditions
     */
    public function deleteWhere($conditions, $params = [])
    {
        $sql = "DELETE FROM {$this->table} WHERE $conditions";
        return $this->db->delete($sql, $params);
    }

    /**
     * Vérifier si un enregistrement existe
     */
    public function exists($id)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        return ($result['total'] ?? 0) > 0;
    }

    /**
     * Vérifier si un enregistrement existe avec des conditions
     */
    public function existsWhere($conditions, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE $conditions";
        $result = $this->db->fetchOne($sql, $params);
        return ($result['total'] ?? 0) > 0;
    }

    /**
     * Paginer les résultats
     */
    public function paginate($page = 1, $perPage = 10, $conditions = null, $params = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;
        
        // Compter le total
        $total = $this->count($conditions, $params);
        
        // Récupérer les données
        $sql = "SELECT * FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $data = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    /**
     * Filtrer les données selon les champs autorisés
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Masquer les champs sensibles
     */
    protected function hideFields($data)
    {
        if (empty($this->hidden)) {
            return $data;
        }
        
        if (is_array($data)) {
            foreach ($this->hidden as $field) {
                unset($data[$field]);
            }
        }
        
        return $data;
    }

    /**
     * Exécuter une requête personnalisée
     */
    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Exécuter une requête personnalisée et récupérer une ligne
     */
    public function queryOne($sql, $params = [])
    {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Exécuter une requête personnalisée et récupérer toutes les lignes
     */
    public function queryAll($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
}
?>
