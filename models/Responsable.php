<?php
/**
 * Modèle Responsable
 * Gère toutes les opérations liées aux responsables
 */

namespace models;

use core\BaseModel;

class Responsable extends BaseModel
{
    protected $table = 'responsables';
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'service',
        'statut',
        'mot_de_passe'
    ];
    protected $hidden = ['mot_de_passe'];

    /**
     * Trouver un responsable par son email
     */
    public function findByEmail($email)
    {
        return $this->whereOne('email = ?', [$email]);
    }

    /**
     * Trouver les responsables par service
     */
    public function findByService($service)
    {
        return $this->where('service = ?', [$service], 'nom ASC');
    }

    /**
     * Trouver les responsables disponibles
     */
    public function getDisponibles()
    {
        return $this->where('statut = ?', ['disponible'], 'nom ASC');
    }

    /**
     * Trouver les responsables avec le nombre de stagiaires
     */
    public function getWithStagiairesCount()
    {
        $sql = "
            SELECT 
                r.*,
                COUNT(s.id) as nombre_stagiaires
            FROM {$this->table} r
            LEFT JOIN stagiaires s ON r.id = s.responsable_id
            GROUP BY r.id
            ORDER BY r.nom ASC
        ";

        return $this->db->fetchAll($sql);
    }

    /**
     * Vérifier si un responsable peut accepter plus de stagiaires
     */
    public function peutAccepterStagiaire($responsableId)
    {
        $sql = "SELECT COUNT(*) as total FROM stagiaires WHERE responsable_id = ? AND statut = 'valide'";
        $result = $this->db->fetchOne($sql, [$responsableId]);
        
        return ($result['total'] ?? 0) < MAX_STAGIAIRES_PAR_RESPONSABLE;
    }

    /**
     * Obtenir les statistiques des responsables
     */
    public function getStatistiques()
    {
        $sql = "
            SELECT 
                statut,
                COUNT(*) as total,
                COUNT(CASE WHEN service IS NOT NULL THEN 1 END) as avec_service
            FROM {$this->table}
            GROUP BY statut
        ";

        return $this->db->fetchAll($sql);
    }

    /**
     * Rechercher des responsables
     */
    public function rechercher($termes, $filtres = [])
    {
        $conditions = [];
        $params = [];

        // Recherche par nom, prénom, email
        if (!empty($termes)) {
            $conditions[] = "(nom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
            $searchTerm = "%$termes%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        // Filtres
        if (!empty($filtres['service'])) {
            $conditions[] = "service = ?";
            $params[] = $filtres['service'];
        }

        if (!empty($filtres['statut'])) {
            $conditions[] = "statut = ?";
            $params[] = $filtres['statut'];
        }

        $whereClause = !empty($conditions) ? implode(' AND ', $conditions) : null;

        return $this->where($whereClause, $params, 'nom ASC');
    }

    /**
     * Vérifier si un email existe déjà
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return ($result['total'] ?? 0) > 0;
    }

    /**
     * Authentifier un responsable
     */
    public function authenticate($email, $password)
    {
        $responsable = $this->findByEmail($email);
        
        if ($responsable && password_verify($password, $responsable['mot_de_passe'])) {
            return $responsable;
        }
        
        return false;
    }

    /**
     * Changer le statut d'un responsable
     */
    public function changerStatut($id, $statut)
    {
        return $this->update($id, ['statut' => $statut]);
    }

    /**
     * Obtenir les services disponibles
     */
    public function getServices()
    {
        $sql = "SELECT DISTINCT service FROM {$this->table} WHERE service IS NOT NULL ORDER BY service";
        return $this->db->fetchAll($sql);
    }
}
?>