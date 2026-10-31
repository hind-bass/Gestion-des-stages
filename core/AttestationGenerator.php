<?php

namespace core;

// Inclure l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Générateur d'attestations PDF
 * Utilise DomPDF pour générer les attestations de début et fin de stage
 */
class AttestationGenerator
{
    private $dompdf;
    private $templatePath;

    public function __construct()
    {
        // Configuration DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $this->dompdf = new Dompdf($options);
        $this->templatePath = __DIR__ . '/../views/components/';
    }

    /**
     * Générer une attestation de début de stage
     */
    public function generateAttestationDebut($stagiaire, $numeroDemande = null)
    {
        $template = $this->loadTemplate('attestation_debut_template.php');
        
        // Remplacer les placeholders
        $content = $this->replacePlaceholders($template, [
            'NUMERO_DEMANDE' => $numeroDemande ?: $this->generateNumeroDemande(),
            'DATE_DEPOT' => $this->formatDate($stagiaire['date_inscription']),
            'NOM_FR' => $stagiaire['nom_fr'],
            'PRENOM_FR' => $stagiaire['prenom_fr'],
            'NOM_AR' => $stagiaire['nom_ar'],
            'PRENOM_AR' => $stagiaire['prenom_ar'],
            'SERVICE_DEMANDE' => $stagiaire['service_demande'],
            'DATE_DEBUT_SOUHAITEE' => $this->formatDate($stagiaire['date_debut_souhaitee']),
            'DATE_FIN_SOUHAITEE' => $this->formatDate($stagiaire['date_fin_souhaitee']),
            'DATE_AUJOURDHUI' => $this->formatDate(date('Y-m-d'))
        ]);

        return $this->generatePDF($content, 'attestation_debut_' . $stagiaire['id']);
    }

    /**
     * Générer une attestation de fin de stage
     */
    public function generateAttestationFin($stagiaire, $numeroDemande = null)
    {
        $template = $this->loadTemplate('attestation_fin_template.php');
        
        // Remplacer les placeholders
        $content = $this->replacePlaceholders($template, [
            'NUMERO_DEMANDE' => $numeroDemande ?: $this->generateNumeroDemande(),
            'DATE_DEPOT' => $this->formatDate($stagiaire['date_inscription']),
            'NOM_FR' => $stagiaire['nom_fr'],
            'PRENOM_FR' => $stagiaire['prenom_fr'],
            'NOM_AR' => $stagiaire['nom_ar'],
            'PRENOM_AR' => $stagiaire['prenom_ar'],
            'SERVICE_DEMANDE' => $stagiaire['service_demande'],
            'DATE_DEBUT_SOUHAITEE' => $this->formatDate($stagiaire['date_debut_souhaitee']),
            'DATE_FIN_SOUHAITEE' => $this->formatDate($stagiaire['date_fin_souhaitee']),
            'DATE_AUJOURDHUI' => $this->formatDate(date('Y-m-d'))
        ]);

        return $this->generatePDF($content, 'attestation_fin_' . $stagiaire['id']);
    }

    /**
     * Charger un template
     */
    private function loadTemplate($filename)
    {
        $filePath = $this->templatePath . $filename;
        
        if (!file_exists($filePath)) {
            throw new \Exception("Template non trouvé: " . $filePath);
        }
        
        return file_get_contents($filePath);
    }

    /**
     * Remplacer les placeholders dans le template
     */
    private function replacePlaceholders($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Générer le PDF
     */
    private function generatePDF($html, $filename)
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        // Sauvegarder le PDF
        $output = $this->dompdf->output();
        $pdfPath = __DIR__ . '/../public/uploads/attestations/' . $filename . '.pdf';
        
        // Créer le dossier s'il n'existe pas
        $dir = dirname($pdfPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($pdfPath, $output);
        
        return [
            'path' => $pdfPath,
            'filename' => $filename . '.pdf',
            'size' => strlen($output)
        ];
    }

    /**
     * Générer un numéro de demande
     */
    private function generateNumeroDemande()
    {
        return date('Y') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Formater une date en français/arabe
     */
    private function formatDate($date)
    {
        if (!$date) {
            return '';
        }
        
        $timestamp = is_string($date) ? strtotime($date) : $date;
        return date('d/m/Y', $timestamp);
    }

    /**
     * Télécharger un PDF
     */
    public function downloadPDF($pdfPath, $filename)
    {
        if (!file_exists($pdfPath)) {
            throw new \Exception("Fichier PDF non trouvé: " . $pdfPath);
        }
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        readfile($pdfPath);
        exit;
    }

    /**
     * Afficher un PDF dans le navigateur
     */
    public function displayPDF($pdfPath)
    {
        if (!file_exists($pdfPath)) {
            throw new \Exception("Fichier PDF non trouvé: " . $pdfPath);
        }
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($pdfPath) . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        readfile($pdfPath);
        exit;
    }
}
?>
