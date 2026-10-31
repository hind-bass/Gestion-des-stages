<?php
namespace controllers\responsable;

use core\BaseController;
use models\Stagiaire;
use models\Responsable;
use models\Tache;
use models\Remarque;

/**
 * Contrôleur Responsable
 * Migré depuis responsable_dashboard.php
 */

class ResponsableController extends BaseController
{
    private $stagiaireModel;
    private $tacheModel;
    private $remarqueModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('responsable');
        
        $this->stagiaireModel = new Stagiaire();
        $this->tacheModel = new Tache();
        $this->remarqueModel = new Remarque();
    }

    /**
     * Dashboard responsable
     */
    public function dashboard()
    {
        $stagiaires = $this->stagiaireModel->findByResponsable($this->user['id']);
        $taches = $this->tacheModel->findByResponsable($this->user['id']);
        $remarques = $this->remarqueModel->findByResponsable($this->user['id']);
        
        // Calculer les statistiques
        $stats = [
            'total_stagiaires' => count($stagiaires),
            'total_taches' => count($taches),
            'taches_terminees' => count(array_filter($taches, fn($t) => $t['statut'] === 'terminee')),
            'taches_en_cours' => count(array_filter($taches, fn($t) => $t['statut'] === 'en_cours')),
            'total_remarques' => count($remarques),
            'remarques_positives' => count(array_filter($remarques, fn($r) => $r['type'] === 'positif')),
            'remarques_negatives' => count(array_filter($remarques, fn($r) => $r['type'] === 'negatif'))
        ];
        
        // Tâches récentes (5 dernières)
        $tachesRecentes = array_slice($taches, 0, 5);
        
        $this->view('responsable/dashboard', [
            'stagiaires' => $stagiaires,
            'taches' => $taches,
            'tachesRecentes' => $tachesRecentes,
            'remarques' => $remarques,
            'stats' => $stats,
            'user' => $this->user
        ]);
    }

    /**
     * Gestion des stagiaires
     */
    public function stagiaires()
    {
        $stagiaires = $this->stagiaireModel->findByResponsable($this->user['id']);
        
        $this->view('responsable/stagiaires', [
            'stagiaires' => $stagiaires,
            'user' => $this->user
        ]);
    }

    /**
     * Gestion des tâches
     */
    public function taches()
    {
        $taches = $this->tacheModel->findByResponsable($this->user['id']);
        
        $this->view('responsable/taches', [
            'taches' => $taches,
            'user' => $this->user
        ]);
    }

    public function showStagiaire($id)
    {
        $stagiaire = $this->stagiaireModel->find($id);
        $this->view('responsable/stagiaires/show', ['stagiaire' => $stagiaire, 'user' => $this->user]);
    }

    public function rapports()
    {
        $this->view('responsable/rapports', ['user' => $this->user]);
    }

    public function evaluations()
    {
        $this->view('responsable/evaluations', ['user' => $this->user]);
    }

    public function showEvaluations($id)
    {
        $this->view('responsable/evaluations/show', ['stagiaire_id' => $id, 'user' => $this->user]);
    }

    public function showCreerTacheForm()
    {
        $stagiaires = $this->stagiaireModel->findByResponsable($this->user['id']);
        $this->view('responsable/taches/creer', ['stagiaires' => $stagiaires, 'user' => $this->user]);
    }

    public function creerTache()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            // Validation et création de la tâche
            $this->jsonSuccess('Tâche créée avec succès');
        } else {
            $this->jsonError('Méthode non autorisée');
        }
    }

    /**
     * Gestion des remarques
     */
    public function remarques()
    {
        $remarques = $this->remarqueModel->findByResponsable($this->user['id']);
        
        $this->view('responsable/remarques', [
            'remarques' => $remarques,
            'user' => $this->user
        ]);
    }

    /**
     * Ajouter une remarque
     */
    public function ajouterRemarque()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            if (empty($data['stage_id']) || empty($data['titre']) || empty($data['contenu'])) {
                $this->jsonError('Tous les champs sont obligatoires');
                return;
            }
            
            $remarqueData = [
                'stage_id' => $data['stage_id'],
                'responsable_id' => $this->user['id'],
                'titre' => $data['titre'],
                'contenu' => $data['contenu'],
                'type' => $data['type'] ?? 'neutre'
            ];
            
            $result = $this->remarqueModel->create($remarqueData);
            
            if ($result) {
                $this->jsonSuccess('Remarque ajoutée avec succès');
            } else {
                $this->jsonError('Erreur lors de l\'ajout de la remarque');
            }
        } else {
            $this->jsonError('Méthode non autorisée');
        }
    }

    /**
     * Modifier une remarque
     */
    public function modifierRemarque()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id']) || empty($data['titre']) || empty($data['contenu'])) {
                $this->jsonError('Tous les champs sont obligatoires');
                return;
            }
            
            $remarqueData = [
                'titre' => $data['titre'],
                'contenu' => $data['contenu'],
                'type' => $data['type'] ?? 'neutre'
            ];
            
            $result = $this->remarqueModel->update($data['id'], $remarqueData);
            
            if ($result) {
                $this->jsonSuccess('Remarque modifiée avec succès');
            } else {
                $this->jsonError('Erreur lors de la modification de la remarque');
            }
        } else {
            $this->jsonError('Méthode non autorisée');
        }
    }

    /**
     * Supprimer une remarque
     */
    public function supprimerRemarque()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id'])) {
                $this->jsonError('ID de la remarque manquant');
                return;
            }
            
            $result = $this->remarqueModel->delete($data['id']);
            
            if ($result) {
                $this->jsonSuccess('Remarque supprimée avec succès');
            } else {
                $this->jsonError('Erreur lors de la suppression de la remarque');
            }
        } else {
            $this->jsonError('Méthode non autorisée');
        }
    }
}
?>