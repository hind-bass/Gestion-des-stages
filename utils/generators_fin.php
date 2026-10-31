<?php
include 'config.php';
include 'generer_attestation_arabe.php';

function generer_attestation_fin_stage($stagiaire_id) {
    return generer_attestation_arabe($stagiaire_id, 'fin');
}

// Test de la fonction
if (isset($_GET['test']) && isset($_GET['id'])) {
    $result = generer_attestation_fin_stage($_GET['id']);
    if ($result) {
        echo "Attestation de fin de stage générée : <a href='$result' target='_blank'>Voir l'attestation</a>";
    } else {
        echo "Erreur lors de la génération";
    }
}
?> 