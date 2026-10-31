<?php

namespace models;

use core\BaseModel;

/**
 * Modèle Document
 * Gère les documents des stagiaires (CV, attestations, etc.)
 */
class Document extends BaseModel
{
    protected $table = 'documents';
    protected $primaryKey = 'id';

    /**
     * Trouver tous les documents d'un stagiaire
     */
    public function findByStagiaire($stagiaireId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE stagiaire_id = ? ORDER BY date_upload DESC";
        return $this->db->fetchAll($sql, [$stagiaireId]);
    }

    /**
     * Trouver un document par type et stagiaire
     */
    public function findByTypeAndStagiaire($type, $stagiaireId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE type_document = ? AND stagiaire_id = ?";
        return $this->db->fetchOne($sql, [$type, $stagiaireId]);
    }

    /**
     * Vérifier si tous les documents requis sont présents pour un stagiaire
     */
    public function checkDocumentsComplets($stagiaireId)
    {
        $types_requis = ['cv', 'attestation_admin', 'carte_identite', 'demande_stage', 'engagement'];
        
        foreach ($types_requis as $type) {
            $doc = $this->findByTypeAndStagiaire($type, $stagiaireId);
            if (!$doc) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Obtenir le statut des documents d'un stagiaire
     */
    public function getStatutDocuments($stagiaireId)
    {
        $sql = "SELECT type_document, statut FROM {$this->table} WHERE stagiaire_id = ?";
        $documents = $this->db->fetchAll($sql, [$stagiaireId]);
        
        $statuts = [];
        foreach ($documents as $doc) {
            $statuts[$doc['type_document']] = $doc['statut'];
        }
        
        return $statuts;
    }

    /**
     * Valider un document
     */
    public function validerDocument($documentId)
    {
        $sql = "UPDATE {$this->table} SET statut = 'valide' WHERE id = ?";
        return $this->db->update($sql, [$documentId]);
    }

    /**
     * Refuser un document
     */
    public function refuserDocument($documentId, $motif = '')
    {
        $sql = "UPDATE {$this->table} SET statut = 'refuse' WHERE id = ?";
        return $this->db->update($sql, [$documentId]);
    }

    /**
     * Supprimer un document
     */
    public function supprimerDocument($documentId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->delete($sql, [$documentId]);
    }

    /**
     * Compter les documents par statut pour un stagiaire
     */
    public function countByStatut($stagiaireId, $statut)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE stagiaire_id = ? AND statut = ?";
        $result = $this->db->fetchOne($sql, [$stagiaireId, $statut]);
        return $result ? (int)$result['COUNT(*)'] : 0;
    }
}
?>
