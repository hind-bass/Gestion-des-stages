<?php
/**
 * Contrôleur Administrateur
 * Gère toutes les fonctionnalités de l'administration
 */
namespace controllers;

use core\BaseController;
use models\Stagiaire;
use models\Responsable;
use PDO;

class AdminController extends BaseController
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
     * Affiche le tableau de bord de l'administration
     */
    public function dashboard()
    {
        // Récupérer les statistiques
        $stats = [
            'totalStagiaires' => $this->stagiaireModel->count(),
            'totalResponsables' => $this->responsableModel->count(),
            'stagiairesEnAttente' => $this->stagiaireModel->countByStatus('en_attente'),
            'stagiairesValides' => $this->stagiaireModel->countByStatus('valide'),
            'totalAdmins' => $this->countAdmins()
        ];
        
        // Récupérer les derniers enregistrements
        $recentData = [
            'admins' => $this->getRecentAdmins(5), // 5 derniers admins
            'stagiaires' => $this->stagiaireModel->getRecent(5), // 5 derniers stagiaires
            'responsables' => $this->responsableModel->getRecent(5) // 5 derniers responsables
        ];
        
        $this->view('admin/dashboard', [
            'user' => $this->user,
            'stats' => $stats,
            'recentData' => $recentData,
            'title' => 'Tableau de bord administrateur'
        ]);
    }
    
    /**
     * Compte le nombre total d'administrateurs
     */
    private function countAdmins()
    {
        $query = "SELECT COUNT(*) FROM administrateurs WHERE statut = 'actif'";
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn();
    }
    
    /**
     * Récupère les derniers administrateurs ajoutés
     */
    private function getRecentAdmins($limit = 5)
    {
        $query = "SELECT id, nom, prenom, email, date_creation 
                 FROM administrateurs 
                 WHERE statut = 'actif'
                 ORDER BY date_creation DESC 
                 LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gestion des stagiaires
     */
    /**
     * Affiche les détails d'un stagiaire
     */
    public function show($id)
    {
        $stagiaire = $this->stagiaireModel->find($id);

        if (!$stagiaire) {
            // Gérer le cas où le stagiaire n'est pas trouvé
            $this->jsonError('Stagiaire non trouvé', 404);
            return;
        }

        $this->view('admin/stagiaires/show', [
            'stagiaire' => $stagiaire,
            'user' => $this->user,
            'title' => 'Détails du Stagiaire'
        ]);
    }

    /**
     * Gestion des stagiaires
     */
    public function stagiaires()
    {
        $page = $this->getQuery('page') ?? 1;
        $search = $this->getQuery('search') ?? '';
        
        $stagiaires = $this->stagiaireModel->paginate($page, 10);
        
        $this->view('admin/stagiaires', [
            'stagiaires' => $stagiaires,
            'user' => $this->user,
            'title' => 'Gestion des stagiaires'
        ]);
    }

    /**
     * Gestion des responsables
     */
    public function responsables()
    {
        $responsables = $this->responsableModel->findAll();
        
        $this->view('admin/responsables', [
            'responsables' => $responsables,
            'user' => $this->user
        ]);
    }

    /**
     * Statistiques
     */
    public function statistiques()
    {
        $stats = $this->stagiaireModel->getStatistiques();
        
        $this->view('admin/statistiques', [
            'stats' => $stats,
            'user' => $this->user
        ]);
    }

    /**
     * Générer une attestation de début de stage
     */
    public function genererAttestationDebut($stagiaireId)
    {
        try {
            $stagiaire = $this->stagiaireModel->find($stagiaireId);
            
            if (!$stagiaire) {
                $this->jsonError('Stagiaire non trouvé', 404);
                return;
            }

            $generator = new \core\AttestationGenerator();
            $result = $generator->generateAttestationDebut($stagiaire);
            
            // Enregistrer l'attestation en base
            $this->enregistrerAttestation($stagiaireId, 'debut', $result['filename']);
            
            $this->jsonSuccess([
                'message' => 'Attestation générée avec succès',
                'filename' => $result['filename'],
                'path' => $result['path']
            ]);
            
        } catch (\Exception $e) {
            error_log("Erreur génération attestation début: " . $e->getMessage());
            $this->jsonError('Erreur lors de la génération de l\'attestation');
        }
    }

    /**
     * Télécharger une attestation
     */
    public function telechargerAttestation($type, $stagiaireId)
    {
        try {
            $stagiaire = $this->stagiaireModel->find($stagiaireId);
            
            if (!$stagiaire) {
                $this->jsonError('Stagiaire non trouvé', 404);
                return;
            }

            $generator = new \core\AttestationGenerator();
            
            if ($type === 'debut') {
                $result = $generator->generateAttestationDebut($stagiaire);
            } elseif ($type === 'fin') {
                $result = $generator->generateAttestationFin($stagiaire);
            } else {
                $this->jsonError('Type d\'attestation invalide', 400);
                return;
            }
            
            $generator->downloadPDF($result['path'], $result['filename']);
            
        } catch (\Exception $e) {
            error_log("Erreur téléchargement attestation: " . $e->getMessage());
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