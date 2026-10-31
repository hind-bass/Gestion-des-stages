<?php

namespace models;

use core\BaseModel;

class Admin extends BaseModel
{
    protected $table = 'administrateurs';
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'statut'
    ];
    protected $hidden = ['mot_de_passe'];

    public function findByEmail($email)
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->where('email = :email')
            ->setParameter('email', $email)
            ->execute()
            ->fetchAssociative();
    }

    /**
     * Vérifie les identifiants de connexion
     */
    public function checkCredentials($email, $password)
    {
        $admin = $this->findByEmail($email);
        
        if ($admin && password_verify($password, $admin['mot_de_passe'])) {
            return $admin;
        }
        
        return false;
    }

    /**
     * Crée une session pour l'administrateur
     */
    public function createSession($admin)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_nom'] = $admin['nom'];
        $_SESSION['user_type'] = 'admin';
    }

    /**
     * Vérifie si l'administrateur est connecté
     */
    public static function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin';
    }

    /**
     * Déconnecte l'administrateur
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_nom']);
        unset($_SESSION['user_type']);
        
        session_destroy();
    }

    /**
     * Récupère l'administrateur connecté
     */
    public static function getCurrentUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return [
            'id' => $_SESSION['admin_id'],
            'email' => $_SESSION['admin_email'],
            'nom' => $_SESSION['admin_nom']
        ];
    }
}
