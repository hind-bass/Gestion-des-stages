<?php
session_start();
include '../config.php';

require_once '../vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;
use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer tous les stagiaires en attente
    $stmt = $pdo->prepare("
        SELECT s.*, COUNT(d.id) as documents_count
        FROM stagiaires s 
        LEFT JOIN documents d ON s.id = d.stagiaire_id
        WHERE s.statut = 'en_attente'
        GROUP BY s.id
    ");
    $stmt->execute();
    $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $validations = 0;
    $refus = 0;
    
    foreach ($stagiaires as $stagiaire) {
        // Vérifier si tous les documents requis sont présents
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM documents 
            WHERE stagiaire_id = ? AND statut = 'valide'
        ");
        $stmt->execute([$stagiaire['id']]);
        $documents_valides = $stmt->fetchColumn();
        
        if ($documents_valides >= 5) {
            // Tous les documents sont présents, valider le stagiaire
            $stmt = $pdo->prepare("
                UPDATE stagiaires 
                SET statut = 'valide', date_validation = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$stagiaire['id']]);
            $validations++;
            
            // Générer et envoyer l'attestation de début de stage
            // Récupérer les infos du stagiaire à jour
            $stmtStagiaire = $pdo->prepare("SELECT * FROM stagiaires WHERE id = ?");
            $stmtStagiaire->execute([$stagiaire['id']]);
            $stagiaireData = $stmtStagiaire->fetch(PDO::FETCH_ASSOC);
            
            // Générer l'attestation PDF
            $modele = '../debut_stage_attestation.docx';
            $nom_pdf = '../attestation_debut_stage_' . $stagiaireData['id'] . '.pdf';
            $templateProcessor = new TemplateProcessor($modele);
            $templateProcessor->setValue('NOM_FR', $stagiaireData['nom_fr'] ?? '');
            $templateProcessor->setValue('NOM_AR', $stagiaireData['nom_ar'] ?? '');
            $templateProcessor->setValue('PRENOM_FR', $stagiaireData['prenom_fr'] ?? '');
            $templateProcessor->setValue('PRENOM_AR', $stagiaireData['prenom_ar'] ?? '');
            $templateProcessor->setValue('NUMERO_DEMANDE', $stagiaireData['id'] ?? '');
            $templateProcessor->setValue('NOMBRE_DEMANDES', '1');
            $templateProcessor->setValue('DATE_DEPOT', $stagiaireData['date_inscription'] ?? '');
            $templateProcessor->setValue('SERVICE_DEMANDE', $stagiaireData['service_demande'] ?? '');
            $templateProcessor->setValue('DATE_DEBUT_SOUHAITEE', $stagiaireData['date_debut_souhaitee'] ?? '');
            $templateProcessor->setValue('DATE_FIN_SOUHAITEE', $stagiaireData['date_fin_souhaitee'] ?? '');
            $templateProcessor->setValue('DATE_AUJOURDHUI', date('Y-m-d'));
            
            $tempHtml = '../attestation_temp_' . uniqid() . '.html';
            $templateProcessor->saveAs($tempHtml);
            $htmlContent = file_get_contents($tempHtml);
            $mpdf = new Mpdf();
            $mpdf->WriteHTML($htmlContent);
            $mpdf->Output($nom_pdf, 'F');
            unlink($tempHtml);
            
            // Générer l'attestation de fin de stage PDF
            $modele_fin = '../fin_stage_attestation.docx';
            $nom_pdf_fin = '../attestation_fin_stage_' . $stagiaireData['id'] . '.pdf';
            $templateProcessorFin = new TemplateProcessor($modele_fin);
            $templateProcessorFin->setValue('NOM_FR', $stagiaireData['nom_fr'] ?? '');
            $templateProcessorFin->setValue('NOM_AR', $stagiaireData['nom_ar'] ?? '');
            $templateProcessorFin->setValue('PRENOM_FR', $stagiaireData['prenom_fr'] ?? '');
            $templateProcessorFin->setValue('PRENOM_AR', $stagiaireData['prenom_ar'] ?? '');
            $templateProcessorFin->setValue('NUMERO_DEMANDE', $stagiaireData['id'] ?? '');
            $templateProcessorFin->setValue('NOMBRE_DEMANDES', '1');
            $templateProcessorFin->setValue('DATE_DEPOT', $stagiaireData['date_inscription'] ?? '');
            $templateProcessorFin->setValue('SERVICE_DEMANDE', $stagiaireData['service_demande'] ?? '');
            $templateProcessorFin->setValue('DATE_DEBUT_SOUHAITEE', $stagiaireData['date_debut_souhaitee'] ?? '');
            $templateProcessorFin->setValue('DATE_FIN_SOUHAITEE', $stagiaireData['date_fin_souhaitee'] ?? '');
            $templateProcessorFin->setValue('DATE_AUJOURDHUI', date('Y-m-d'));
            
            $tempHtmlFin = '../attestation_temp_fin_' . uniqid() . '.html';
            $templateProcessorFin->saveAs($tempHtmlFin);
            $htmlContentFin = file_get_contents($tempHtmlFin);
            $mpdfFin = new Mpdf();
            $mpdfFin->WriteHTML($htmlContentFin);
            $mpdfFin->Output($nom_pdf_fin, 'F');
            unlink($tempHtmlFin);
            
            // Envoyer l'email avec les deux attestations en pièces jointes
            $mail = new PHPMailer(true);
            try {
                $mail->setFrom('tonemail@domaine.com', 'Commune d\'Oujda');
                $mail->addAddress($stagiaireData['email'], $stagiaireData['nom_fr'] . ' ' . $stagiaireData['prenom_fr']);
                $mail->Subject = 'Vos attestations de stage';
                $mail->Body = "Bonjour,\nVeuillez trouver en pièces jointes vos attestations de début et de fin de stage.";
                $mail->addAttachment($nom_pdf);
                $mail->addAttachment($nom_pdf_fin);
                $mail->send();
                unlink($nom_pdf); // Supprimer le PDF après envoi
                unlink($nom_pdf_fin);
            } catch (Exception $e) {
                // Gérer l'erreur d'envoi si besoin
            }
        } else {
            // Documents incomplets, refuser le stagiaire
            $stmt = $pdo->prepare("
                UPDATE stagiaires 
                SET statut = 'refuse', date_validation = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$stagiaire['id']]);
            $refus++;
            
            // Envoyer un email de refus
            // TODO: Implémenter l'envoi d'email
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Vérification terminée: $validations validations, $refus refus"
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>
