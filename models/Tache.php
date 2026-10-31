<?php
/**
 * Modèle Tache
 * Gère toutes les opérations liées aux tâches
 */

namespace models;

use core\BaseModel;

class Tache extends BaseModel
{
    protected $table = 'taches';
    protected $fillable = [
        'titre',
        'description',
        'statut',
        'priorite',
        'date_debut',
        'date_fin',
        'stage_id',
        'date_creation'
    ];

    /**
     * Trouver les tâches d'un stagiaire
     */
    public function findByStagiaire($stagiaireId)
    {
        $sql = "
            SELECT t.*, s.sujet as stage_titre
            FROM {$this->table} t
            JOIN stages s ON t.stage_id = s.id
            WHERE s.stagiaire_id = ?
            ORDER BY t.date_creation DESC
        ";
        
        return $this->query($sql, [$stagiaireId]);
    }

    /**
     * Trouver les tâches d'un responsable
     */
    public function findByResponsable($responsableId)
    {
        $sql = "
            SELECT t.*, s.sujet as stage_titre, st.nom_fr as stagiaire_nom, st.prenom_fr as stagiaire_prenom
            FROM {$this->table} t
            JOIN stages s ON t.stage_id = s.id
            JOIN stagiaires st ON s.stagiaire_id = st.id
            WHERE s.responsable_id = ?
            ORDER BY t.date_creation DESC
        ";
        
        return $this->query($sql, [$responsableId]);
    }

    /**
     * Trouver les tâches par statut
     */
    public function findByStatus($status)
    {
        return $this->where('statut = ?', [$status], 'date_creation DESC');
    }

    /**
     * Trouver les tâches par priorité
     */
    public function findByPriorite($priorite)
    {
        return $this->where('priorite = ?', [$priorite], 'date_creation DESC');
    }

    /**
     * Trouver les tâches en retard
     */
    public function getEnRetard()
    {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE date_fin < CURDATE() 
            AND statut != 'terminee'
            ORDER BY date_fin ASC
        ";
        
        return $this->query($sql);
    }

    /**
     * Trouver les tâches à faire aujourd'hui
     */
    public function getAujourdhui()
    {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE DATE(date_debut) = CURDATE()
            AND statut != 'terminee'
            ORDER BY priorite DESC, date_creation ASC
        ";
        
        return $this->query($sql);
    }

    /**
     * Trouver les tâches de la semaine
     */
    public function getCetteSemaine()
    {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE YEARWEEK(date_debut, 1) = YEARWEEK(CURDATE(), 1)
            AND statut != 'terminee'
            ORDER BY date_debut ASC
        ";
        
        return $this->query($sql);
    }

    /**
     * Obtenir les tâches avec les détails des utilisateurs
     */
    public function getWithDetails($id = null, $conditions = null, $params = [])
    {
        $sql = "
            SELECT 
                t.*,
                s.sujet as stage_titre,
                st.nom_fr as stagiaire_nom,
                st.prenom_fr as stagiaire_prenom,
                st.email as stagiaire_email,
                r.nom as responsable_nom,
                r.prenom as responsable_prenom,
                r.email as responsable_email
            FROM {$this->table} t
            LEFT JOIN stages s ON t.stage_id = s.id
            LEFT JOIN stagiaires st ON s.stagiaire_id = st.id
            LEFT JOIN responsables r ON s.responsable_id = r.id
        ";

        if ($id) {
            $sql .= " WHERE t.id = ?";
            return $this->queryOne($sql, [$id]);
        }

        if ($conditions) {
            $sql .= " WHERE $conditions";
        }

        $sql .= " ORDER BY t.date_creation DESC";

        return $this->query($sql, $params);
    }

    /**
     * Créer une nouvelle tâche
     */
    public function create($data)
    {
        $data['date_creation'] = date(DATETIME_FORMAT);
        $data['date_modification'] = date(DATETIME_FORMAT);
        
        return parent::create($data);
    }

    /**
     * Mettre à jour une tâche
     */
    public function update($id, $data)
    {
        $data['date_modification'] = date(DATETIME_FORMAT);
        
        return parent::update($id, $data);
    }

    /**
     * Changer le statut d'une tâche
     */
    public function changerStatut($id, $statut)
    {
        return $this->update($id, [
            'statut' => $statut,
            'date_modification' => date(DATETIME_FORMAT)
        ]);
    }

    /**
     * Assigner une tâche à un stage
     */
    public function assigner($id, $stageId)
    {
        return $this->update($id, [
            'stage_id' => $stageId
        ]);
    }

    /**
     * Obtenir les statistiques des tâches
     */
    public function getStatistiques()
    {
        $sql = "
            SELECT 
                statut,
                COUNT(*) as total,
                COUNT(CASE WHEN priorite = 'haute' THEN 1 END) as haute_priorite,
                COUNT(CASE WHEN date_fin < CURDATE() AND statut != 'terminee' THEN 1 END) as en_retard
            FROM {$this->table}
            GROUP BY statut
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
                COUNT(t.id) as total_taches,
                COUNT(CASE WHEN t.statut = 'terminee' THEN 1 END) as taches_terminees,
                COUNT(CASE WHEN t.statut = 'en_cours' THEN 1 END) as taches_en_cours,
                COUNT(CASE WHEN t.statut = 'a_faire' THEN 1 END) as taches_a_faire
            FROM responsables r
            LEFT JOIN {$this->table} t ON r.id = t.responsable_id
        ";

        if ($responsableId) {
            $sql .= " WHERE r.id = ?";
            $sql .= " GROUP BY r.id";
            return $this->queryOne($sql, [$responsableId]);
        }

        $sql .= " GROUP BY r.id ORDER BY total_taches DESC";
        return $this->query($sql);
    }

    /**
     * Rechercher des tâches
     */
    public function rechercher($termes, $filtres = [])
    {
        $conditions = [];
        $params = [];

        // Recherche par titre, description
        if (!empty($termes)) {
            $conditions[] = "(titre LIKE ? OR description LIKE ?)";
            $searchTerm = "%$termes%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        // Filtres
        if (!empty($filtres['statut'])) {
            $conditions[] = "statut = ?";
            $params[] = $filtres['statut'];
        }

        if (!empty($filtres['priorite'])) {
            $conditions[] = "priorite = ?";
            $params[] = $filtres['priorite'];
        }

        if (!empty($filtres['stage_id'])) {
            $conditions[] = "stage_id = ?";
            $params[] = $filtres['stage_id'];
        }

        if (!empty($filtres['stagiaire_id'])) {
            $conditions[] = "s.stagiaire_id = ?";
            $params[] = $filtres['stagiaire_id'];
        }

        if (!empty($filtres['responsable_id'])) {
            $conditions[] = "s.responsable_id = ?";
            $params[] = $filtres['responsable_id'];
        }

        if (!empty($filtres['date_debut'])) {
            $conditions[] = "date_debut >= ?";
            $params[] = $filtres['date_debut'];
        }

        if (!empty($filtres['date_fin'])) {
            $conditions[] = "date_fin <= ?";
            $params[] = $filtres['date_fin'];
        }

        $whereClause = !empty($conditions) ? implode(' AND ', $conditions) : null;

        return $this->where($whereClause, $params, 'date_creation DESC');
    }

    /**
     * Vérifier si une tâche est en retard
     */
    public function estEnRetard($id)
    {
        $tache = $this->find($id);
        
        if (!$tache) {
            return false;
        }
        
        return $tache['date_fin'] < date(DATE_FORMAT) && $tache['statut'] !== 'terminee';
    }

    /**
     * Calculer le pourcentage de progression d'une tâche
     */
    public function getProgression($id)
    {
        $tache = $this->find($id);
        
        if (!$tache) {
            return 0;
        }
        
        switch ($tache['statut']) {
            case 'a_faire':
                return 0;
            case 'en_cours':
                return 50;
            case 'terminee':
                return 100;
            default:
                return 0;
        }
    }
}
?>