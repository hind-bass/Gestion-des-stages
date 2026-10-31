<?php

namespace Utils;

class Security
{
    // Algorithm for password hashing
    private const PASSWORD_ALGO = PASSWORD_BCRYPT;
    private const PASSWORD_OPTIONS = ['cost' => 12];

    /**
     * Hash a password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, self::PASSWORD_ALGO, self::PASSWORD_OPTIONS);
    }

    /**
     * Verify a password against a hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehashing
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, self::PASSWORD_ALGO, self::PASSWORD_OPTIONS);
    }

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize input
     */
    public static function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Secure session start with strict settings
     */
    public static function secureSessionStart()
    {
        $sessionName = 'secure_session';
        $secure = true; // Only send cookie over HTTPS
        $httponly = true; // Prevent JavaScript access to session cookie
        
        // Force session to only use cookies
        ini_set('session.use_only_cookies', 1);
        
        // Get current cookies params
        $cookieParams = session_get_cookie_params();
        
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'] ?? '',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => 'Strict'
        ]);

        // Set session name
        session_name($sessionName);
        
        // Start session
        session_start();
        
        // Regenerate session ID to prevent session fixation
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }
}
