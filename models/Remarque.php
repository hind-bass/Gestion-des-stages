<?php
namespace models;

use core\BaseModel;

/**
 * Modèle Remarque
 * Gère toutes les opérations liées aux remarques
 */
class Remarque extends BaseModel
{
    protected $table = 'remarques';
    protected $fillable = [
        'stage_id',
        'responsable_id',
        'titre',
        'contenu',
        'type',
        'date_creation',
        'date_modification'
    ];

    /**
     * Trouver les remarques d'un stagiaire
     */
    public function findByStagiaire($stagiaireId)
    {
        $sql = "
            SELECT r.*, 
                   CONCAT(res.prenom, ' ', res.nom) as responsable_nom,
                   CONCAT(res.prenom, ' ', res.nom) as responsable_prenom,
                   s.sujet as stage_titre
            FROM {$this->table} r
            JOIN stages s ON r.stage_id = s.id
            JOIN responsables res ON r.responsable_id = res.id
            WHERE s.stagiaire_id = ?
            ORDER BY r.date_creation DESC
        ";
        
        return $this->query($sql, [$stagiaireId]);
    }

    /**
     * Trouver les remarques d'un responsable
     */
    public function findByResponsable($responsableId)
    {
        $sql = "
            SELECT r.*, 
                   CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire_nom,
                   CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire_prenom,
                   s.email as stagiaire_email,
                   st.sujet as stage_titre
            FROM {$this->table} r
            JOIN stages st ON r.stage_id = st.id
            JOIN stagiaires s ON st.stagiaire_id = s.id
            WHERE r.responsable_id = ?
            ORDER BY r.date_creation DESC
        ";
        
        return $this->query($sql, [$responsableId]);
    }

    /**
     * Trouver les remarques d'un stage
     */
    public function findByStage($stageId)
    {
        return $this->where('stage_id = ?', [$stageId], 'date_creation DESC');
    }

    /**
     * Trouver les remarques par type
     */
    public function findByType($type)
    {
        return $this->where('type = ?', [$type], 'date_creation DESC');
    }

    /**
     * Créer une nouvelle remarque
     */
    public function create($data)
    {
        $data['date_creation'] = date('Y-m-d H:i:s');
        $data['date_modification'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }

    /**
     * Mettre à jour une remarque
     */
    public function update($id, $data)
    {
        $data['date_modification'] = date('Y-m-d H:i:s');
        
        return parent::update($id, $data);
    }

    /**
     * Obtenir les statistiques des remarques
     */
    public function getStatistiques()
    {
        $sql = "
            SELECT 
                type,
                COUNT(*) as total,
                COUNT(CASE WHEN DATE(date_creation) = CURDATE() THEN 1 END) as aujourd_hui,
                COUNT(CASE WHEN DATE(date_creation) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as cette_semaine
            FROM {$this->table}
            GROUP BY type
        ";

        return $this->query($sql);
    }

    /**
     * Obtenir les statistiques par responsable
     */
    public function getStatistiquesParResponsable($responsableId = null)
    {
        $sql = "
            SELECT 
                r.nom as responsable_nom,
                r.prenom as responsable_prenom,
                COUNT(rem.id) as total_remarques,
                COUNT(CASE WHEN rem.type = 'positif' THEN 1 END) as remarques_positives,
                COUNT(CASE WHEN rem.type = 'negatif' THEN 1 END) as remarques_negatives,
                COUNT(CASE WHEN rem.type = 'neutre' THEN 1 END) as remarques_neutres
            FROM responsables r
            LEFT JOIN {$this->table} rem ON r.id = rem.responsable_id
        ";

        if ($responsableId) {
            $sql .= " WHERE r.id = ?";
            $sql .= " GROUP BY r.id";
            return $this->queryOne($sql, [$responsableId]);
        }

        $sql .= " GROUP BY r.id ORDER BY total_remarques DESC";
        return $this->query($sql);
    }

    /**
     * Rechercher des remarques
     */
    public function rechercher($termes, $filtres = [])
    {
        $conditions = [];
        $params = [];

        // Recherche par titre, contenu
        if (!empty($termes)) {
            $conditions[] = "(r.titre LIKE ? OR r.contenu LIKE ?)";
            $searchTerm = "%$termes%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        // Filtres
        if (!empty($filtres['type'])) {
            $conditions[] = "r.type = ?";
            $params[] = $filtres['type'];
        }

        if (!empty($filtres['responsable_id'])) {
            $conditions[] = "r.responsable_id = ?";
            $params[] = $filtres['responsable_id'];
        }

        if (!empty($filtres['stage_id'])) {
            $conditions[] = "r.stage_id = ?";
            $params[] = $filtres['stage_id'];
        }

        if (!empty($filtres['date_debut'])) {
            $conditions[] = "DATE(r.date_creation) >= ?";
            $params[] = $filtres['date_debut'];
        }

        if (!empty($filtres['date_fin'])) {
            $conditions[] = "DATE(r.date_creation) <= ?";
            $params[] = $filtres['date_fin'];
        }

        $whereClause = !empty($conditions) ? implode(' AND ', $conditions) : null;

        $sql = "
            SELECT r.*, 
                   CONCAT(res.prenom, ' ', res.nom) as responsable_nom,
                   CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire_nom,
                   st.sujet as stage_titre
            FROM {$this->table} r
            JOIN stages st ON r.stage_id = st.id
            JOIN stagiaires s ON st.stagiaire_id = s.id
            JOIN responsables res ON r.responsable_id = res.id
        ";

        if ($whereClause) {
            $sql .= " WHERE $whereClause";
        }

        $sql .= " ORDER BY r.date_creation DESC";

        return $this->query($sql, $params);
    }

    /**
     * Obtenir les dernières remarques
     */
    public function getRecent($limit = 10)
    {
        $sql = "
            SELECT r.*, 
                   CONCAT(res.prenom, ' ', res.nom) as responsable_nom,
                   CONCAT(s.prenom_fr, ' ', s.nom_fr) as stagiaire_nom,
                   st.sujet as stage_titre
            FROM {$this->table} r
            JOIN stages st ON r.stage_id = st.id
            JOIN stagiaires s ON st.stagiaire_id = s.id
            JOIN responsables res ON r.responsable_id = res.id
            ORDER BY r.date_creation DESC
            LIMIT ?
        ";
        
        return $this->query($sql, [$limit]);
    }
}
?>
