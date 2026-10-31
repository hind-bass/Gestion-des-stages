<?php

namespace controllers;

use core\BaseController;   // la classe BaseController est dans l'espace core
use models\Stagiaire;
use models\Responsable;
use models\Tache;
use models\Document;

/**
 * Contrôleur Stagiaire
 * Migré depuis espace_stagiaire.php
 */

class StagiaireController extends BaseController
{
    private $stagiaireModel;
    private $tacheModel;
    private $documentModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('stagiaire');
        
        $this->stagiaireModel = new Stagiaire();
        $this->tacheModel = new Tache();
        $this->documentModel = new Document();
    }

    /**
     * Dashboard stagiaire
     */
    public function dashboard()
    {
        $stagiaire = $this->stagiaireModel->find($this->user['id']);
        $taches = $this->tacheModel->findByStagiaire($this->user['id']);

        // Initialisation pour éviter les erreurs si les données sont nulles
        $stagiaire = $stagiaire ?: [];
        $taches = $taches ?: [];
        $remarques = []; // Modèle de remarque non existant pour le moment

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

    /**
     * Gestion des attestations
     */
    public function attestations()
    {
        $sql = "SELECT * FROM attestations WHERE stagiaire_id = ? ORDER BY date_generation DESC";
        $attestations = $this->db->fetchAll($sql, [$this->user['id']]);
        
        $this->view('stagiaire/attestations', [
            'attestations' => $attestations,
            'user' => $this->user
        ]);
    }

    /**
     * Télécharger une attestation
     */
    public function telechargerAttestation($attestationId)
    {
        try {
            $sql = "SELECT * FROM attestations WHERE id = ? AND stagiaire_id = ?";
            $attestation = $this->db->fetchOne($sql, [$attestationId, $this->user['id']]);
            
            if (!$attestation) {
                $this->jsonError('Attestation non trouvée', 404);
                return;
            }

            $pdfPath = __DIR__ . '/../../public/' . $attestation['chemin_pdf'];
            
            if (!file_exists($pdfPath)) {
                $this->jsonError('Fichier PDF non trouvé', 404);
                return;
            }

            $generator = new \core\AttestationGenerator();
            $generator->downloadPDF($pdfPath, $attestation['numero_attestation'] . '.pdf');
            
        } catch (\Exception $e) {
            error_log("Erreur téléchargement attestation stagiaire: " . $e->getMessage());
            $this->jsonError('Erreur lors du téléchargement de l\'attestation');
        }
    }

    /**
     * Afficher une attestation dans le navigateur
     */
    public function afficherAttestation($attestationId)
    {
        try {
            $sql = "SELECT * FROM attestations WHERE id = ? AND stagiaire_id = ?";
            $attestation = $this->db->fetchOne($sql, [$attestationId, $this->user['id']]);
            
            if (!$attestation) {
                $this->jsonError('Attestation non trouvée', 404);
                return;
            }

            $pdfPath = __DIR__ . '/../../public/' . $attestation['chemin_pdf'];
            
            if (!file_exists($pdfPath)) {
                $this->jsonError('Fichier PDF non trouvé', 404);
                return;
            }

            $generator = new \core\AttestationGenerator();
            $generator->displayPDF($pdfPath);
            
        } catch (\Exception $e) {
            error_log("Erreur affichage attestation stagiaire: " . $e->getMessage());
            $this->jsonError('Erreur lors de l\'affichage de l\'attestation');
        }
    }
}
?>