<?php
require 'vendor/autoload.php';
include 'config.php';
use Mpdf\Mpdf;

session_start();
$stagiaire_id = $_SESSION['stagiaire_id'] ?? $_GET['id'] ?? null;
if (!$stagiaire_id) {
    die('Aucun stagiaire sélectionné.');
}

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
$stmt = $pdo->prepare("SELECT * FROM stagiaires WHERE id = ?");
$stmt->execute([$stagiaire_id]);
$stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

$type = $_GET['type'] ?? 'debut'; // 'debut' ou 'fin'
$nom_pdf = ($type === 'fin') ? 'attestation_fin_stage.pdf' : 'attestation_debut_stage.pdf';

ob_start();
if ($type === 'fin') {
    include 'attestation_fin_template.php';
} else {
    include 'attestation_debut_template.php';
}
$htmlContent = ob_get_clean();

$mpdf = new Mpdf();
$mpdf->WriteHTML($htmlContent);
$mpdf->Output($nom_pdf, 'D');
?>
<span class="value"><?php echo htmlspecialchars($stagiaire['nom_ar']) . ' ' . htmlspecialchars($stagiaire['prenom_ar']); ?></span>
<span class="value"><?php echo date('d/m/Y', strtotime($stagiaire['date_debut_souhaitee'])); ?></span>
<span class="value"><?php echo date('d/m/Y', strtotime($stagiaire['date_fin_souhaite'])); ?></span>
<span class="value"><?php echo $numero_demande; ?></span>
<span class="value"><?php echo $date_actuelle; ?></span>