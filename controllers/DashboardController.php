<?php
namespace controllers;

use core\BaseController;
use models\Stagiaire;
use models\Responsable;

/**
 * Contrôleur du Dashboard Administrateur
 * Gère toutes les actions liées au dashboard admin
 */

class DashboardController extends BaseController
{
    private $stagiaireModel;
    private $responsableModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
        
        $this->stagiaireModel = new Stagiaire();
        $this->responsableModel = new Responsable();
    }

    /**
     * Afficher le dashboard principal
     */
    public function index()
    {
        try {
            // Récupérer les statistiques
            $stats = $this->getStatistiques();
            
            // Récupérer les derniers stagiaires
            $derniersStagiaires = $this->stagiaireModel->findAll('date_inscription DESC', 5);
            
            // Récupérer les stagiaires en attente
            $stagiairesEnAttente = $this->stagiaireModel->getEnAttente();
            
            // Récupérer les statistiques par service
            $statsParService = $this->stagiaireModel->getStatistiquesParService();
            
            $this->view('admin/dashboard', [
                'stats' => $stats,
                'derniersStagiaires' => $derniersStagiaires,
                'stagiairesEnAttente' => $stagiairesEnAttente,
                'statsParService' => $statsParService,
                'user' => $this->user
            ]);
            
        } catch (Exception $e) {
            logError('Erreur dashboard admin: ' . $e->getMessage());
            $this->view('admin/error', ['message' => 'Erreur lors du chargement du dashboard']);
        }
    }

    /**
     * Obtenir les statistiques du dashboard
     */
    private function getStatistiques()
    {
        $stats = $this->stagiaireModel->getStatistiques();
        
        // Ajouter les statistiques des responsables
        $stats['responsables'] = $this->responsableModel->count();
        $stats['responsables_disponibles'] = $this->responsableModel->count('statut = ?', ['disponible']);
        
        return $stats;
    }

    /**
     * API pour obtenir les statistiques en AJAX
     */
    public function getStats()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Requête invalide', 400);
        }

        try {
            $stats = $this->getStatistiques();
            $this->jsonSuccess($stats);
        } catch (Exception $e) {
            logError('Erreur stats API: ' . $e->getMessage());
            $this->jsonError('Erreur lors de la récupération des statistiques');
        }
    }

    /**
     * API pour obtenir les stagiaires en attente
     */
    public function getStagiairesEnAttente()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Requête invalide', 400);
        }

        try {
            $stagiaires = $this->stagiaireModel->getEnAttente();
            $this->jsonSuccess($stagiaires);
        } catch (Exception $e) {
            logError('Erreur stagiaires en attente API: ' . $e->getMessage());
            $this->jsonError('Erreur lors de la récupération des stagiaires');
        }
    }

    /**
     * API pour obtenir les derniers stagiaires
     */
    public function getDerniersStagiaires()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Requête invalide', 400);
        }

        try {
            $limit = (int)($this->getQuery('limit') ?? 5);
            $stagiaires = $this->stagiaireModel->findAll('date_inscription DESC', $limit);
            $this->jsonSuccess($stagiaires);
        } catch (Exception $e) {
            logError('Erreur derniers stagiaires API: ' . $e->getMessage());
            $this->jsonError('Erreur lors de la récupération des stagiaires');
        }
    }

    /**
     * API pour obtenir les statistiques par service
     */
    public function getStatsParService()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Requête invalide', 400);
        }

        try {
            $stats = $this->stagiaireModel->getStatistiquesParService();
            $this->jsonSuccess($stats);
        } catch (Exception $e) {
            logError('Erreur stats par service API: ' . $e->getMessage());
            $this->jsonError('Erreur lors de la récupération des statistiques');
        }
    }

    /**
     * Valider un stagiaire depuis le dashboard
     */
    public function validerStagiaire()
    {
        if (!$this->isPost()) {
            $this->jsonError('Méthode non autorisée', 405);
        }

        $stagiaireId = (int)$this->getPost('stagiaire_id');
        $responsableId = (int)($this->getPost('responsable_id') ?? 0);

        try {
            // Valider le stagiaire
            $this->stagiaireModel->valider($stagiaireId, $responsableId ?: null);
            
            // Logger l'action
            $this->logAction('validation_stagiaire', [
                'stagiaire_id' => $stagiaireId,
                'responsable_id' => $responsableId
            ]);
            
            $this->jsonSuccess([], 'Stagiaire validé avec succès');
        } catch (Exception $e) {
            logError('Erreur validation stagiaire: ' . $e->getMessage());
            $this->jsonError('Erreur lors de la validation du stagiaire');
        }
    }

    /**
     * Refuser un stagiaire depuis le dashboard
     */
    public function refuserStagiaire()
    {
        if (!$this->isPost()) {
            $this->jsonError('Méthode non autorisée', 405);
        }

        $stagiaireId = (int)$this->getPost('stagiaire_id');
        $motif = $this->getPost('motif');

        try {
            // Refuser le stagiaire
            $this->stagiaireModel->refuser($stagiaireId, $motif);
            
            // Logger l'action
            $this->logAction('refus_stagiaire', [
                'stagiaire_id' => $stagiaireId,
                'motif' => $motif
            ]);
            
            $this->jsonSuccess([], 'Stagiaire refusé');
        } catch (Exception $e) {
            logError('Erreur refus stagiaire: ' . $e->getMessage());
            $this->jsonError('Erreur lors du refus du stagiaire');
        }
    }

    /**
     * Rechercher des stagiaires
     */
    public function rechercherStagiaires()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Requête invalide', 400);
        }

        $termes = $this->getQuery('q');
        $filtres = [
            'statut' => $this->getQuery('statut'),
            'service' => $this->getQuery('service'),
            'date_debut' => $this->getQuery('date_debut'),
            'date_fin' => $this->getQuery('date_fin')
        ];

        try {
            $stagiaires = $this->stagiaireModel->rechercher($termes, $filtres);
            $this->jsonSuccess($stagiaires);
        } catch (Exception $e) {
            logError('Erreur recherche stagiaires: ' . $e->getMessage());
            $this->jsonError('Erreur lors de la recherche');
        }
    }

    /**
     * Exporter les données du dashboard
     */
    public function exporter()
    {
        $format = $this->getQuery('format') ?? 'csv';
        $type = $this->getQuery('type') ?? 'stagiaires';

        try {
            switch ($type) {
                case 'stagiaires':
                    $data = $this->stagiaireModel->findAll('date_inscription DESC');
                    break;
                case 'statistiques':
                    $data = $this->getStatistiques();
                    break;
                default:
                    throw new Exception('Type d\'export non supporté');
            }

            $this->exporterDonnees($data, $format, $type);
        } catch (Exception $e) {
            logError('Erreur export: ' . $e->getMessage());
            $this->jsonError('Erreur lors de l\'export');
        }
    }

    /**
     * Exporter les données dans différents formats
     */
    private function exporterDonnees($data, $format, $type)
    {
        $filename = "export_{$type}_" . date('Y-m-d_H-i-s');

        switch ($format) {
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
                
                $output = fopen('php://output', 'w');
                
                if (!empty($data)) {
                    // En-têtes
                    fputcsv($output, array_keys($data[0]));
                    
                    // Données
                    foreach ($data as $row) {
                        fputcsv($output, $row);
                    }
                }
                
                fclose($output);
                break;
                
            case 'json':
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="' . $filename . '.json"');
                echo json_encode($data, JSON_PRETTY_PRINT);
                break;
                
            default:
                throw new Exception('Format d\'export non supporté');
        }
        
        exit();
    }
}
?>