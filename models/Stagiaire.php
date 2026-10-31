<?php
/**
 * Modèle Stagiaire
 * Gère toutes les opérations liées aux stagiaires
 */

namespace models;

use core\BaseModel;
use PDO;
use PDOException;
use Utils\Security;

class Stagiaire extends BaseModel
{
    protected $table = 'stagiaires';
    protected $fillable = [
        'numero_stagiaire',
        'nom_fr',
        'nom_ar',
        'prenom_fr',
        'prenom_ar',
        'cin',
        'cv_path',
        'demande_stage_path',
        'assurance_path',
        'cin_scan_path',
        'etablissement',
        'filiere',
        'niveau',
        'email',
        'telephone',
        'mot_de_passe',
        'date_debut_souhaitee',
        'date_fin_souhaitee',
        'service_demande',
        'statut',
        'responsable_id',
        'date_validation',
        'motif_refus',
        'remember_token',
        'remember_token_expires',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $hidden = [
        'mot_de_passe', 
        'remember_token', 
        'remember_token_expires',
        'deleted_at'
    ];
    
    protected $casts = [
        'date_debut_souhaitee' => 'date',
        'date_fin_souhaitee' => 'date',
        'date_validation' => 'datetime',
        'remember_token_expires' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    /**
     * Règles de validation pour la création d'un stagiaire
     */
    public static function getValidationRules($id = null): array
    {
        $rules = [
            'nom_fr' => 'required|string|max:100',
            'prenom_fr' => 'required|string|max:100',
            'nom_ar' => 'required|string|max:100',
            'prenom_ar' => 'required|string|max:100',
            'cin' => 'required|string|max:20|unique:stagiaires,cin' . ($id ? ",$id" : ''),
            'etablissement' => 'required|string|max:200',
            'filiere' => 'nullable|string|max:100',
            'niveau' => 'nullable|string|max:50',
            'email' => 'required|email|max:150|unique:stagiaires,email' . ($id ? ",$id" : ''),
            'telephone' => 'nullable|string|max:20',
            'date_debut_souhaitee' => 'required|date',
            'date_fin_souhaitee' => 'required|date|after_or_equal:date_debut_souhaitee',
            'service_demande' => 'nullable|string|max:100',
            'statut' => 'required|in:en_attente,valide,refuse,rejete',
            'responsable_id' => 'nullable|exists:responsables,id'
        ];
        
        if (!$id) {
            $rules['mot_de_passe'] = 'required|string|min:8';
        } else {
            $rules['mot_de_passe'] = 'nullable|string|min:8';
        }
        
        return $rules;
    }

    /**
     * Récupère un stagiaire par son adresse e-mail
     *
     * @param string $email L'adresse e-mail du stagiaire
     * @return array|null Les informations du stagiaire ou null si non trouvé
     */
    public function getByEmail($email)
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->where('email = :email')
            ->andWhere('deleted_at IS NULL')
            ->setParameter('email', $email)
            ->execute()
            ->fetchAssociative() ?: null;
    }

    /**
     * Crée un nouveau stagiaire avec hachage du mot de passe
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data)
    {
        if (isset($data['mot_de_passe'])) {
            $data['mot_de_passe'] = Security::hashPassword($data['mot_de_passe']);
        }
        
        return parent::create($data);
    }
    
    /**
     * Met à jour un stagiaire avec hachage du mot de passe si fourni
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = Security::hashPassword($data['mot_de_passe']);
        } else {
            unset($data['mot_de_passe']);
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Vérifie les identifiants de connexion
     * 
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate($email, $password)
    {
        $user = $this->getByEmail($email);
        
        if ($user && Security::verifyPassword($password, $user['mot_de_passe'])) {
            // Vérifier si le mot de passe doit être réhaché
            if (Security::needsRehash($user['mot_de_passe'])) {
                $this->update($user['id'], ['mot_de_passe' => $password]);
            }
            return $user;
        }
        
        return false;
    }
    
    /**
     * Récupère les stagiaires avec pagination et filtres
     * 
     * @param array $filters
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getPaginated(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $query = $this->db->createQueryBuilder()
            ->select('s.*')
            ->from($this->table, 's')
            ->where('s.deleted_at IS NULL');
            
        // Appliquer les filtres
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->andWhere("s.$field = :$field")
                    ->setParameter($field, $value);
            }
        }
        
        // Compter le nombre total d'éléments
        $countQuery = clone $query;
        $total = (int) $countQuery->select('COUNT(s.id) as total')
            ->execute()
            ->fetchOne();
            
        // Paginer les résultats
        $offset = ($page - 1) * $perPage;
        $results = $query->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->orderBy('s.created_at', 'DESC')
            ->execute()
            ->fetchAllAssociative();
            
        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Récupère les statistiques des stagiaires
     * 
     * @return array
     */
    public function getStats(): array
    {
        $stats = [
            'total' => $this->countStagiaires(),
            'en_attente' => $this->countStagiaires(['statut' => 'en_attente']),
            'valides' => $this->countStagiaires(['statut' => 'valide']),
            'refuses' => $this->countStagiaires(['statut' => 'refuse']),
            'rejetes' => $this->countStagiaires(['statut' => 'rejete']),
            'par_service' => []
        ];
        
        // Récupérer la répartition par service
        $sql = "SELECT service_demande, COUNT(*) as count FROM {$this->table} 
                WHERE deleted_at IS NULL AND service_demande IS NOT NULL 
                GROUP BY service_demande ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        foreach ($services as $service) {
            $stats['par_service'][$service['service_demande']] = (int) $service['count'];
        }
        
        return $stats;
    }
    
    /**
     * Compte le nombre de stagiaires selon des critères
     * 
     * @param array $criteria
     * @return int
     */
    public function countStagiaires(array $criteria = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
        $params = [];
        
        foreach ($criteria as $field => $value) {
            $sql .= " AND $field = ?";
            $params[] = $value;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['total'];
    }
    
    /**
     * Trouver un stagiaire par son email
     */
    public function findByEmail($email)
    {
        return $this->whereOne('email = ?', [$email]);
    }

    /**
     * Trouver un stagiaire par son numéro
     */
    public function findByNumero($numero)
    {
        return $this->whereOne('numero_stagiaire = ?', [$numero]);
    }

    /**
     * Trouver un stagiaire par son CIN
     */
    public function findByCIN($cin)
    {
        return $this->whereOne('cin = ?', [$cin]);
    }

    /**
     * Trouver les stagiaires par statut
     */
    public function findByStatus($status)
    {
        return $this->where('statut = ?', [$status], 'date_inscription DESC');
    }

    /**
     * Trouver les stagiaires par service
     */
    public function findByService($service)
    {
        return $this->where('service_demande = ?', [$service], 'date_inscription DESC');
    }

    /**
     * Trouver les stagiaires d'un responsable
     */
    public function findByResponsable($responsableId)
    {
        // Utiliser la table affectations pour la relation responsable-stagiaire
        $sql = "
            SELECT s.*, a.service, a.date_affectation
            FROM stagiaires s
            JOIN affectations a ON s.id = a.stagiaire_id
            WHERE a.responsable_id = ? AND a.statut = 'active'
            ORDER BY a.date_affectation DESC
        ";
        
        return $this->query($sql, [$responsableId]);
    }

    /**
     * Trouver les stagiaires en attente
     */
    public function getEnAttente()
    {
        return $this->findByStatus(STAGIAIRE_STATUS_EN_ATTENTE);
    }

    /**
     * Trouver les stagiaires validés
     */
    public function getValides()
    {
        return $this->findByStatus(STAGIAIRE_STATUS_VALIDE);
    }

    /**
     * Trouver les stagiaires refusés
     */
    public function getRefuses()
    {
        return $this->findByStatus(STAGIAIRE_STATUS_REFUSE);
    }

    /**
     * Valider un stagiaire
     */
    public function valider($id, $responsableId = null)
    {
        $data = [
            'statut' => STAGIAIRE_STATUS_VALIDE,
            'date_validation' => date(DATE_FORMAT)
        ];

        if ($responsableId) {
            $data['responsable_id'] = $responsableId;
        }

        return $this->update($id, $data);
    }

    /**
     * Refuser un stagiaire
     */
    public function refuser($id, $motif = null)
    {
        $data = [
            'statut' => STAGIAIRE_STATUS_REFUSE,
            'responsable_id' => null
        ];

        if ($motif) {
            $data['motif_refus'] = $motif;
        }

        return $this->update($id, $data);
    }

    /**
     * Affecter un stagiaire à un responsable
     */
    public function affecter($id, $responsableId)
    {
        $data = [
            'responsable_id' => $responsableId,
            'statut' => STAGIAIRE_STATUS_VALIDE,
            'date_validation' => date(DATE_FORMAT)
        ];

        return $this->update($id, $data);
    }

    /**
     * Désaffecter un stagiaire
     */
    public function desaffecter($id)
    {
        $data = [
            'responsable_id' => null
        ];

        return $this->update($id, $data);
    }

    /**
     * Générer un numéro de stagiaire unique
     */
    public function generateNumeroStagiaire()
    {
        $prefix = 'STG';
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        $numero = $prefix . $year . $random;

        // Vérifier que le numéro n'existe pas déjà
        while ($this->findByNumero($numero)) {
            $random = strtoupper(substr(md5(uniqid()), 0, 6));
            $numero = $prefix . $year . $random;
        }

        return $numero;
    }

    /**
     * Obtenir les statistiques des stagiaires
     */
    public function getStatistiques()
    {
        $sql = "
            SELECT 
                statut,
                COUNT(*) as total,
                COUNT(CASE WHEN date_inscription >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as nouveaux_30j,
                COUNT(CASE WHEN date_inscription >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as nouveaux_7j
            FROM {$this->table}
            GROUP BY statut
        ";

        $resultats = $this->db->fetchAll($sql);
        
        $stats = [
            'total' => 0,
            'en_attente' => 0,
            'valides' => 0,
            'refuses' => 0,
            'nouveaux_30j' => 0,
            'nouveaux_7j' => 0
        ];

        foreach ($resultats as $row) {
            $stats['total'] += $row['total'];
            $stats[$row['statut']] = $row['total'];
            $stats['nouveaux_30j'] += $row['nouveaux_30j'];
            $stats['nouveaux_7j'] += $row['nouveaux_7j'];
        }

        return $stats;
    }

    /**
     * Obtenir les statistiques par service
     */
    public function getStatistiquesParService()
    {
        $sql = "
            SELECT 
                service_demande,
                COUNT(*) as total,
                COUNT(CASE WHEN statut = 'valide' THEN 1 END) as valides,
                COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as en_attente,
                COUNT(CASE WHEN statut = 'refuse' THEN 1 END) as refuses
            FROM {$this->table}
            WHERE service_demande IS NOT NULL
            GROUP BY service_demande
            ORDER BY total DESC
        ";

        return $this->db->fetchAll($sql);
    }

    /**
     * Rechercher des stagiaires
     */
    public function rechercher($termes, $filtres = [])
    {
        $conditions = [];
        $params = [];

        // Recherche par nom, prénom, email, numéro
        if (!empty($termes)) {
            $conditions[] = "(nom_fr LIKE ? OR prenom_fr LIKE ? OR email LIKE ? OR numero_stagiaire LIKE ?)";
            $searchTerm = "%$termes%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        // Filtres
        if (!empty($filtres['statut'])) {
            $conditions[] = "statut = ?";
            $params[] = $filtres['statut'];
        }

        if (!empty($filtres['service'])) {
            $conditions[] = "service_demande = ?";
            $params[] = $filtres['service'];
        }

        if (!empty($filtres['date_debut'])) {
            $conditions[] = "date_inscription >= ?";
            $params[] = $filtres['date_debut'];
        }

        if (!empty($filtres['date_fin'])) {
            $conditions[] = "date_inscription <= ?";
            $params[] = $filtres['date_fin'];
        }

        $whereClause = !empty($conditions) ? implode(' AND ', $conditions) : null;

        return $this->where($whereClause, $params, 'date_inscription DESC');
    }

    /**
     * Obtenir les stagiaires avec leurs informations complètes
     */
    public function getWithDetails($id = null, $conditions = null, $params = [])
    {
        $sql = "
            SELECT 
                s.*,
                r.nom as responsable_nom,
                r.prenom as responsable_prenom,
                r.email as responsable_email,
                r.service as responsable_service
            FROM {$this->table} s
            LEFT JOIN responsables r ON s.responsable_id = r.id
        ";

        if ($id) {
            $sql .= " WHERE s.id = ?";
            return $this->db->fetchOne($sql, [$id]);
        }

        if ($conditions) {
            $sql .= " WHERE $conditions";
        }

        $sql .= " ORDER BY s.date_inscription DESC";

        return $this->db->fetchAll($sql, $params);
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
     * Vérifier si un CIN existe déjà
     */
    public function cinExists($cin, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE cin = ?";
        $params = [$cin];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return ($result['total'] ?? 0) > 0;
    }

    /**
     * Crée ou met à jour un jeton de rappel pour le stagiaire
     * 
     * @param int $userId ID du stagiaire
     * @param string $token Jeton de rappel
     * @param string $expires Date d'expiration au format Y-m-d H:i:s
     * @return bool Succès de l'opération
     */
    public function setRememberToken($userId, $token, $expires)
    {
        $sql = "UPDATE {$this->table} SET remember_token = ?, remember_token_expires = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token, $expires, $userId]);
    }

    /**
     * Récupère un stagiaire par son jeton de rappel
     * 
     * @param string $token Jeton de rappel
     * @return array|null Les informations du stagiaire ou null si non trouvé ou expiré
     */
    public function findByRememberToken($token)
    {
        if (empty($token)) {
            return null;
        }

        $sql = "SELECT * FROM {$this->table} WHERE remember_token = ? AND remember_token_expires > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Supprime le jeton de rappel d'un stagiaire
     * 
     * @param int $userId ID du stagiaire
     * @return bool Succès de l'opération
     */
    public function clearRememberToken($userId)
    {
        $sql = "UPDATE {$this->table} SET remember_token = NULL, remember_token_expires = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
?>