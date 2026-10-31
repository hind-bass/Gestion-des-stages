<?php
/**
 * Contrôleur de base
 * Fournit les fonctionnalités communes à tous les contrôleurs
 */
namespace core;

use core\Database;

abstract class BaseController
{
    protected $db;
    protected $user;
    protected $role;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadUser();
    }

    /**
     * Charger les informations de l'utilisateur connecté
     */
    protected function loadUser()
    {
        if (isset($_SESSION['admin_id'])) {
            $this->role = 'admin';
            $this->user = [
                'id' => $_SESSION['admin_id'],
                'email' => $_SESSION['admin_email'],
                'nom' => $_SESSION['admin_nom'],
                'prenom' => $_SESSION['admin_prenom']
            ];
        } elseif (isset($_SESSION['responsable_id'])) {
            $this->role = 'responsable';
            $this->user = [
                'id' => $_SESSION['responsable_id'],
                'email' => $_SESSION['responsable_email'],
                'nom' => $_SESSION['responsable_nom'],
                'prenom' => $_SESSION['responsable_prenom'],
                'service' => $_SESSION['responsable_service']
            ];
        } elseif (isset($_SESSION['stagiaire_id'])) {
            $this->role = 'stagiaire';
            $this->user = [
                'id' => $_SESSION['stagiaire_id'],
                'email' => $_SESSION['stagiaire_email'],
                'nom' => $_SESSION['stagiaire_nom'],
                'prenom' => $_SESSION['stagiaire_prenom']
            ];
        } else {
            $this->role = null;
            $this->user = null;
        }
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function requireAuth($expectedRole = null)
    {
        if (!$this->user) {
            if ($expectedRole) {
                $this->redirectToRoleLogin($expectedRole);
            }
            $this->redirect(BASE_URL . 'login');
        }
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    protected function requireRole($role)
    {
        // Si pas connecté, rediriger vers la page de connexion du rôle demandé
        if (!$this->user) {
            $this->redirectToRoleLogin($role);
        }

        // Si le rôle ne correspond pas, rediriger également vers la bonne page de connexion
        if ($this->role !== $role) {
            $this->redirectToRoleLogin($role);
        }
    }

    /**
     * Rediriger vers l'écran de connexion approprié selon le rôle
     */
    private function redirectToRoleLogin($role)
    {
        switch ($role) {
            case 'admin':
                $this->redirect(BASE_URL . 'admin/login');
                break;
            case 'responsable':
                $this->redirect(BASE_URL . 'responsable/login');
                break;
            case 'stagiaire':
            default:
                $this->redirect(BASE_URL . 'login');
                break;
        }
    }

    /**
     * Vérifier si l'utilisateur a un des rôles spécifiés
     */
    protected function requireAnyRole($roles)
    {
        $this->requireAuth();
        
        if (!in_array($this->role, $roles)) {
            $this->redirect('/unauthorized');
        }
    }

    /**
     * Rediriger vers une URL
     */
    protected function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    /**
     * Afficher une vue
     */
    protected function view($view, $data = [])
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Inclure la vue
        $viewPath = VIEWS_PATH . '/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("Vue non trouvée: $view");
        }
    }

    /**
     * Retourner une réponse JSON
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Retourner une réponse JSON de succès
     */
    protected function jsonSuccess($data = [], $message = 'Succès')
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Retourner une réponse JSON d'erreur
     */
    protected function jsonError($message = 'Erreur', $statusCode = 400)
    {
        $this->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Valider les données de requête
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Règle required
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "Le champ $field est obligatoire";
                continue;
            }
            
            // Règle email
            if (strpos($rule, 'email') !== false && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Le champ $field doit être un email valide";
            }
            
            // Règle min
            if (preg_match('/min:(\d+)/', $rule, $matches) && !empty($value)) {
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = "Le champ $field doit contenir au moins $min caractères";
                }
            }
            
            // Règle max
            if (preg_match('/max:(\d+)/', $rule, $matches) && !empty($value)) {
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = "Le champ $field ne peut pas dépasser $max caractères";
                }
            }
        }
        
        return $errors;
    }

    /**
     * Nettoyer les données d'entrée
     */
    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Obtenir les données POST
     */
    protected function getPost($key = null)
    {
        if ($key) {
            return $_POST[$key] ?? null;
        }
        return $_POST;
    }

    /**
     * Obtenir les données GET
     */
    protected function getQuery($key = null)
    {
        if ($key) {
            return $_GET[$key] ?? null;
        }
        return $_GET;
    }

    /**
     * Obtenir un fichier uploadé
     */
    protected function getFile($key)
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Vérifie si la requête est une requête AJAX
     * 
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Vérifier si la requête est en POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Vérifier si la requête est en GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Logger une action
     */
    protected function logAction($action, $details = [])
    {
        $logData = array_merge([
            'action' => $action,
            'user_id' => $this->user['id'] ?? null,
            'role' => $this->role,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ], $details);

        $logFile = LOGS_PATH . '/actions.log';
        $logEntry = json_encode($logData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Générer un token CSRF
     */
    protected function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifier un token CSRF
     */
    protected function verifyCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Uploader un fichier
     */
    protected function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'])
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erreur lors de l\'upload du fichier');
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedTypes)) {
            throw new Exception('Type de fichier non autorisé');
        }

        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $destination . '/' . $filename;

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Erreur lors du déplacement du fichier');
        }

        return $filename;
    }

    /**
     * Paginer les résultats
     */
    protected function paginate($data, $page = 1, $perPage = 10)
    {
        $total = count($data);
        $totalPages = ceil($total / $perPage);
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        return [
            'data' => array_slice($data, $offset, $perPage),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
    }
}
?>