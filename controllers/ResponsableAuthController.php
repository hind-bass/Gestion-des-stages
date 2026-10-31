<?php

namespace controllers;

use core\BaseController;
use models\Responsable;

/**
 * Contrôleur d'authentification pour les responsables
 */
class ResponsableAuthController extends BaseController
{
    private $responsableModel;

    public function __construct()
    {
        parent::__construct();
        $this->responsableModel = new Responsable();
    }

    /**
     * Affiche le formulaire de connexion pour les responsables
     */
    public function login()
    {
        // Si déjà connecté, rediriger vers le dashboard
        if ($this->isLoggedIn() && $this->role === 'responsable') {
            $this->redirect('/responsable');
        }

        $error = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs';
            } else {
                $responsable = $this->responsableModel->authenticate($email, $password);
                
                if ($responsable) {
                    $this->createResponsableSession($responsable);
                    $this->redirect(BASE_URL . 'responsable');
                } else {
                    $error = 'Email ou mot de passe incorrect';
                }
            }
        }

        $this->view('auth/responsable_login', [
            'error' => $error,
            'email' => $email
        ]);
    }

    /**
     * Déconnexion du responsable
     */
    public function logout()
    {
        // Détruire la session du responsable
        unset($_SESSION['responsable_id']);
        unset($_SESSION['responsable_email']);
        unset($_SESSION['responsable_nom']);
        unset($_SESSION['responsable_prenom']);
        unset($_SESSION['responsable_service']);
        
        // Rediriger vers la page d'accueil
        $this->redirect(BASE_URL);
    }

    /**
     * Créer une session pour le responsable connecté
     */
    private function createResponsableSession($responsable)
    {
        $_SESSION['responsable_id'] = $responsable['id'];
        $_SESSION['responsable_email'] = $responsable['email'];
        $_SESSION['responsable_nom'] = $responsable['nom'] ?? '';
        $_SESSION['responsable_prenom'] = $responsable['prenom'] ?? '';
        $_SESSION['responsable_service'] = $responsable['service'] ?? '';
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    private function isLoggedIn()
    {
        return isset($_SESSION['responsable_id']);
    }
}
?>
