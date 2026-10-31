<?php

namespace controllers\stagiaire;

use core\BaseController;   
use models\Stagiaire;
use models\Responsable;
use models\Tache;
use models\Document;
use models\Remarque;

/**
 * Contrôleur Stagiaire
 */

class StagiaireController extends BaseController
{
    private $stagiaireModel;
    private $tacheModel;
    private $documentModel;
    private $remarqueModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('stagiaire');
        
        $this->stagiaireModel = new Stagiaire();
        $this->tacheModel = new Tache();
        $this->documentModel = new Document();
        $this->remarqueModel = new Remarque();
    }

    /**
     * Dashboard stagiaire
     */
    public function dashboard()
    {
        $stagiaire = $this->stagiaireModel->find($this->user['id']);
        $taches = $this->tacheModel->findByStagiaire($this->user['id']);
        $remarques = $this->remarqueModel->findByStagiaire($this->user['id']);

        // Initialisation pour éviter les erreurs si les données sont nulles
        $stagiaire = $stagiaire ?: [];
        $taches = $taches ?: [];
        $remarques = $remarques ?: [];

        // Calcul des statistiques
        $stats = [
            'taches_terminees' => 0,
            'taches_en_cours' => 0,
            'progression' => 0
        ];

        if (!empty($taches)) {
            $taches_terminees = count(array_filter($taches, fn($t) => $t['statut'] === 'terminee'));
            $taches_en_cours = count(array_filter($taches, fn($t) => $t['statut'] === 'en_cours'));
            $total_taches = count($taches);

            $stats['taches_terminees'] = $taches_terminees;
            $stats['taches_en_cours'] = $taches_en_cours;
            if ($total_taches > 0) {
                $stats['progression'] = round(($taches_terminees / $total_taches) * 100);
            }
        }

        $this->view('stagiaire/dashboard', [
            'stagiaire' => $stagiaire,
            'taches' => $taches,
            'stats' => $stats,
            'remarques' => $remarques,
            'user' => $this->user
        ]);
    }

    /**
     * Gestion des documents
     */
    public function documents()
    {
        $documents = $this->documentModel->findByStagiaire($this->user['id']);
        
        $this->view('stagiaire/documents', [
            'documents' => $documents,
            'user' => $this->user
        ]);
    }

    /**
     * Gestion des tâches
     */
    public function taches()
    {
        $taches = $this->tacheModel->findByStagiaire($this->user['id']);
        
        $this->view('stagiaire/taches', [
            'taches' => $taches,
            'user' => $this->user
        ]);
    }

    public function profil()
    {
        $stagiaire = $this->stagiaireModel->find($this->user['id']);
        $this->view('stagiaire/profil', ['stagiaire' => $stagiaire, 'user' => $this->user]);
    }

    public function attestations()
    {
        // Logique pour récupérer les attestations
        $this->view('stagiaire/attestations', ['user' => $this->user]);
    }

    public function attestationFin()
    {
        // Logique pour l'attestation de fin
        $this->view('stagiaire/attestation_fin', ['user' => $this->user]);
    }

    public function evaluations()
    {
        // Logique pour récupérer les évaluations
        $this->view('stagiaire/evaluations', ['user' => $this->user]);
    }
}
?>