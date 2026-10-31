<?php
/**
 * Configuration des champs stagiaires
 * Ce fichier définit quels champs utiliser pour l'affichage
 */

// Champs à utiliser pour l'affichage principal
define('STAGIAIRE_NOM_FIELD', 'nom');           // Utilise la vue (nom_fr ou nom_ar)
define('STAGIAIRE_PRENOM_FIELD', 'prenom');     // Utilise la vue (prenom_fr ou prenom_ar)

// Champs spécifiques par langue
define('STAGIAIRE_NOM_AR', 'nom_ar');
define('STAGIAIRE_NOM_FR', 'nom_fr');
define('STAGIAIRE_PRENOM_AR', 'prenom_ar');
define('STAGIAIRE_PRENOM_FR', 'prenom_fr');

// Langue par défaut (ar = arabe, fr = français)
define('STAGIAIRE_LANGUE_DEFAUT', 'fr');

// Fonction pour obtenir le nom complet dans la langue spécifiée
function getStagiaireNomComplet($stagiaire, $langue = null) {
    if ($langue === null) {
        $langue = STAGIAIRE_LANGUE_DEFAUT;
    }
    
    if ($langue === 'ar') {
        return ($stagiaire['prenom_ar'] ?? '') . ' ' . ($stagiaire['nom_ar'] ?? '');
    } else {
        return ($stagiaire['prenom_fr'] ?? '') . ' ' . ($stagiaire['nom_fr'] ?? '');
    }
}

// Fonction pour obtenir le nom dans la langue spécifiée
function getStagiaireNom($stagiaire, $langue = null) {
    if ($langue === null) {
        $langue = STAGIAIRE_LANGUE_DEFAUT;
    }
    
    return $langue === 'ar' ? ($stagiaire['nom_ar'] ?? '') : ($stagiaire['nom_fr'] ?? '');
}

// Fonction pour obtenir le prénom dans la langue spécifiée
function getStagiairePrenom($stagiaire, $langue = null) {
    if ($langue === null) {
        $langue = STAGIAIRE_LANGUE_DEFAUT;
    }
    
    return $langue === 'ar' ? ($stagiaire['prenom_ar'] ?? '') : ($stagiaire['prenom_fr'] ?? '');
}
?>