<?php

namespace controllers;

use core\BaseController;
use models\Stagiaire;

class AuthController extends BaseController
{
    private $stagiaireModel;

    public function __construct()
    {
        parent::__construct();
        $this->stagiaireModel = new Stagiaire();
    }

    /**
     * Affiche le formulaire de connexion et gère l'authentification.
     */
    public function login()
    {
        if (!$this->isLoggedIn() && isset($_COOKIE['remember_token'])) {
            $this->loginWithRememberToken($_COOKIE['remember_token']);
        }

        if ($this->isLoggedIn()) {
            $this->redirectAfterLogin();
        }

        $error = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            if (empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs';
            } else {
                $stagiaire = $this->stagiaireModel->authenticate($email, $password);
                
                if ($stagiaire) {
                    $this->createUserSession($stagiaire);
                    if ($remember) {
                        $this->createRememberToken($stagiaire['id']);
                    }
                    $this->redirectAfterLogin();
                } else {
                    $error = 'Email ou mot de passe incorrect';
                }
            }
        }

        $this->view('auth/login', [
            'error' => $error,
            'email' => $email
        ]);
    }

    /**
     * Gère l'affichage du formulaire d'inscription et le traitement des données.
     */
    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation de base
            $required_fields = ['nom_fr', 'prenom_fr', 'nom_ar', 'prenom_ar', 'email', 'mot_de_passe', 'confirm_password', 'etablissement', 'filiere', 'niveau', 'date_debut_souhaitee', 'date_fin_souhaitee'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $this->view('auth/inscription', ['error' => 'Tous les champs marqués d\'un * sont obligatoires.']);
                    return;
                }
            }

            if ($_POST['mot_de_passe'] !== $_POST['confirm_password']) {
                $this->view('auth/inscription', ['error' => 'Les mots de passe ne correspondent pas.']);
                return;
            }

            // Gérer le téléversement des fichiers
            $upload_dir = __DIR__ . '/../../public/uploads/documents_stagiaire/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $cv_path = $this->handleFileUpload($_FILES['cv'], $upload_dir);
            $demande_path = $this->handleFileUpload($_FILES['demande_stage'], $upload_dir);
            $assurance_path = $this->handleFileUpload($_FILES['assurance'], $upload_dir);
            $cin_path = $this->handleFileUpload($_FILES['cin_scan'], $upload_dir);

            if (!$cv_path || !$demande_path || !$assurance_path || !$cin_path) {
                $this->view('auth/inscription', ['error' => 'Erreur lors du téléversement des fichiers. Veuillez réessayer.']);
                return;
            }

            $data = [
                'cv_path' => $cv_path,
                'demande_stage_path' => $demande_path,
                'assurance_path' => $assurance_path,
                'cin_scan_path' => $cin_path,
                'nom_fr' => $_POST['nom_fr'],
                'prenom_fr' => $_POST['prenom_fr'],
                'nom_ar' => $_POST['nom_ar'],
                'prenom_ar' => $_POST['prenom_ar'],
                'email' => $_POST['email'],
                'telephone' => $_POST['telephone'] ?? null,
                'mot_de_passe' => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                'etablissement' => $_POST['etablissement'],
                'filiere' => $_POST['filiere'],
                'niveau' => $_POST['niveau'],
                'date_debut_souhaitee' => $_POST['date_debut_souhaitee'],
                'date_fin_souhaitee' => $_POST['date_fin_souhaitee'],
                'numero_stagiaire' => $this->stagiaireModel->generateNumeroStagiaire(),
                'statut' => 'en_attente'
            ];

            $stagiaireId = $this->stagiaireModel->create($data);

            if ($stagiaireId) {
                $_SESSION['success_message'] = 'Inscription réussie ! Votre demande sera examinée. Vous pouvez maintenant vous connecter.';
                header('Location: /login?role=stagiaire');
                exit();
            } else {
                $error = 'Une erreur est survenue lors de l\'inscription. L\'email est peut-être déjà utilisé.';
                $this->view('auth/inscription', ['error' => $error]);
            }
        } else {
            $this->view('auth/inscription');
        }
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout()
    {
        if (isset($_SESSION['stagiaire_id'])) {
            $this->stagiaireModel->clearRememberToken($_SESSION['stagiaire_id']);
        }
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        $this->clearRememberMeCookie();

        header('Location: /login');
        exit();
    }

    /**
     * Crée une session utilisateur.
     */
    private function createUserSession($user)
    {
        // Clés spécifiques attendues par BaseController pour les stagiaires
        $_SESSION['stagiaire_id'] = $user['id'];
        $_SESSION['stagiaire_email'] = $user['email'];
        $_SESSION['stagiaire_nom'] = $user['nom_fr'] ?? ($user['nom'] ?? '');
        $_SESSION['stagiaire_prenom'] = $user['prenom_fr'] ?? ($user['prenom'] ?? '');

        // Conserver aussi les clés génériques utilisées dans ce contrôleur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nom'] = $user['nom_fr'] ?? ($user['nom'] ?? '');
        $_SESSION['role'] = 'stagiaire';
    }

    /**
     * Redirige l'utilisateur après la connexion.
     */
    private function redirectAfterLogin()
    {
        $redirect = $_SESSION['redirect_after_login'] ?? (BASE_URL . 'stagiaire');
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit();
    }

    /**
     * Crée un jeton "Se souvenir de moi".
     */
    private function createRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        if ($this->stagiaireModel->setRememberToken($userId, $token, $expires)) {
            setcookie('remember_token', $token, [
                'expires' => strtotime('+30 days'),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }

    /**
     * Tente de connecter l'utilisateur avec un jeton "Se souvenir de moi".
     */
    private function loginWithRememberToken($token)
    {
        $user = $this->stagiaireModel->findByRememberToken($token);
        
        if ($user) {
            $this->createUserSession($user);
            $this->createRememberToken($user['id']); // Renouveler le token
            return true;
        }
        
        $this->clearRememberMeCookie();
        return false;
    }

    /**
     * Supprime le cookie "Se souvenir de moi".
     */
    private function clearRememberMeCookie()
    {
        setcookie('remember_token', '', time() - 3600, '/');
    }

    /**
     * Vérifie si un utilisateur est connecté.
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    /**
     * Gère le téléversement d'un fichier.
     * @param array $file Le tableau de fichier de $_FILES.
     * @param string $upload_dir Le dossier de destination.
     * @return string|false Le chemin du fichier ou false en cas d'échec.
     */
    private function handleFileUpload($file, $upload_dir)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($extension, $allowed_extensions)) {
            return false;
        }

        $filename = uniqid('', true) . '.' . $extension;
        $destination = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Retourner un chemin relatif depuis la racine du projet web
            return 'uploads/documents_stagiaire/' . $filename;
        }

        return false;
    }
}