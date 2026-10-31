<?php
/**
 * Configuration email
 * Paramètres pour l'envoi d'emails
 */

return [
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'votre-email@gmail.com',
        'password' => 'votre-mot-de-passe-app'
    ],
    
    'from' => [
        'address' => 'noreply@commune-oujda.ma',
        'name' => 'Commune d\'Oujda - Gestion des Stages'
    ],
    
    'templates' => [
        'validation_stagiaire' => [
            'subject' => 'Validation de votre demande de stage',
            'template' => 'emails/validation_stagiaire.html'
        ],
        'refus_stagiaire' => [
            'subject' => 'Réponse à votre demande de stage',
            'template' => 'emails/refus_stagiaire.html'
        ],
        'attestation_debut' => [
            'subject' => 'Attestation de début de stage',
            'template' => 'emails/attestation_debut.html'
        ],
        'attestation_fin' => [
            'subject' => 'Attestation de fin de stage',
            'template' => 'emails/attestation_fin.html'
        ],
        'nouvelle_tache' => [
            'subject' => 'Nouvelle tâche assignée',
            'template' => 'emails/nouvelle_tache.html'
        ],
        'remarque_ajoutee' => [
            'subject' => 'Nouvelle remarque ajoutée',
            'template' => 'emails/remarque_ajoutee.html'
        ]
    ]
]; 