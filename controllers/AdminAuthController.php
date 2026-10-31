<?php
namespace controllers;

use core\BaseController;
use models\Admin;

class AdminAuthController extends BaseController
{
    private $adminModel;

    public function __construct()
    {
        parent::__construct();
        $this->adminModel = new Admin();
    }

    /**
     * Affiche le formulaire de connexion administrateur
     */
    public function login()
    {
        // Si l'utilisateur est déjà connecté en tant qu'admin, redirection vers le tableau de bord
        if (Admin::isLoggedIn()) {
            header('Location: /admin/dashboard');
            exit();
        }

        // Traitement du formulaire de connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validation des données
            if (empty($email) || empty($password)) {
                $this->view('admin/login', [
                    'error' => 'Veuillez remplir tous les champs',
                    'email' => $email
                ]);
                return;
            }

            // Vérification des identifiants
            $admin = $this->adminModel->checkCredentials($email, $password);

            if ($admin) {
                // Création de la session (assure les clés attendues par BaseController)
                $this->adminModel->createSession($admin);
                // Compléter les informations manquantes si nécessaire
                if (!isset($_SESSION['admin_prenom'])) {
                    $_SESSION['admin_prenom'] = $admin['prenom'] ?? '';
                }
                
                // Redirection vers le tableau de bord admin
                header('Location: ' . BASE_URL . 'admin/dashboard');
                exit();
            } else {
                $this->view('admin/login', [
                    'error' => 'Email ou mot de passe incorrect',
                    'email' => $email
                ]);
            }
            return;
        }

        // Affichage du formulaire de connexion
        $this->view('admin/login');
    }

    /**
     * Déconnecte l'administrateur
     */
    public function logout()
    {
        // Détruit la session
        Admin::logout();
        
        // Redirige vers la page de connexion
        header('Location: /admin/login');
        exit();
    }

    /**
     * Vérifie si l'utilisateur est un administrateur connecté
     * 
     * @return bool true si l'utilisateur est un administrateur connecté, false sinon
     */
    public static function requireAdmin()
    {
        if (!Admin::isLoggedIn()) {
            header('Location: /admin/login');
            exit();
        }
        return true;
    }
}
