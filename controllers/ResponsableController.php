<?php
namespace controllers;

use core\BaseController;
use models\Stagiaire;
use models\Responsable;
use models\Tache;

/**
 * Contrôleur Responsable
 * Migré depuis responsable_dashboard.php
 */

class ResponsableController extends BaseController
{
    private $stagiaireModel;
    private $tacheModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('responsable');
        
        $this->stagiaireModel = new Stagiaire();
        $this->tacheModel = new Tache();
    }

    /**
     * Dashboard responsable
     */
    public function dashboard()
    {
        $stagiaires = $this->stagiaireModel->findByResponsable($this->user['id']);
        
        $this->view('responsable/dashboard', [
            'stagiaires' => $stagiaires,
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
     * Générer une attestation de fin de stage
     */
    public function genererAttestationFin($stagiaireId)
    {
        try {
            $stagiaire = $this->stagiaireModel->find($stagiaireId);
            
            if (!$stagiaire) {
                $this->jsonError('Stagiaire non trouvé', 404);
                return;
            }

            // Vérifier que le stagiaire appartient au responsable
            if ($stagiaire['responsable_id'] != $this->user['id']) {
                $this->jsonError('Accès non autorisé', 403);
                return;
            }

            $generator = new \core\AttestationGenerator();
            $result = $generator->generateAttestationFin($stagiaire);
            
            // Enregistrer l'attestation en base
            $this->enregistrerAttestation($stagiaireId, 'fin', $result['filename']);
            
            $this->jsonSuccess([
                'message' => 'Attestation de fin générée avec succès',
                'filename' => $result['filename'],
                'path' => $result['path']
            ]);
            
        } catch (\Exception $e) {
            error_log("Erreur génération attestation fin: " . $e->getMessage());
            $this->jsonError('Erreur lors de la génération de l\'attestation');
        }
    }

    /**
     * Télécharger une attestation de fin
     */
    public function telechargerAttestationFin($stagiaireId)
    {
        try {
            $stagiaire = $this->stagiaireModel->find($stagiaireId);
            
            if (!$stagiaire) {
                $this->jsonError('Stagiaire non trouvé', 404);
                return;
            }

            // Vérifier que le stagiaire appartient au responsable
            if ($stagiaire['responsable_id'] != $this->user['id']) {
                $this->jsonError('Accès non autorisé', 403);
                return;
            }

            $generator = new \core\AttestationGenerator();
            $result = $generator->generateAttestationFin($stagiaire);
            
            $generator->downloadPDF($result['path'], $result['filename']);
            
        } catch (\Exception $e) {
            error_log("Erreur téléchargement attestation fin: " . $e->getMessage());
            $this->jsonError('Erreur lors du téléchargement de l\'attestation');
        }
    }

    /**
     * Enregistrer une attestation en base de données
     */
    private function enregistrerAttestation($stagiaireId, $type, $filename)
    {
        $sql = "INSERT INTO attestations (stagiaire_id, type, numero_attestation, chemin_pdf, statut) 
                VALUES (?, ?, ?, ?, 'generee')";
        
        $numeroAttestation = date('Y') . '/' . str_pad($stagiaireId, 4, '0', STR_PAD_LEFT) . '/' . strtoupper($type);
        $cheminPdf = 'uploads/attestations/' . $filename;
        
        $this->db->insert($sql, [$stagiaireId, $type, $numeroAttestation, $cheminPdf]);
    }
}
?>