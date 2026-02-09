<?php

namespace App\Core;

class Auth
{
    private $config;
    private $db;

    public function __construct($config, $db)
    {
        $this->config = $config;
        $this->db = $db;
        $this->startSession();
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionPath = $this->getAbsolutePath($this->config['paths']['sessions']);
            
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0700, true);
            }

            ini_set('session.save_path', $sessionPath);
            ini_set('session.gc_maxlifetime', $this->config['security']['session_lifetime']);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }

            session_name($this->config['security']['session_name']);
            session_start();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }

    public function attempt($username, $password)
    {
        $user = $this->db->fetchOne(
            'SELECT * FROM users WHERE username = ?',
            [$username]
        );

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            // Note: last_login_at column update removed (column doesn't exist in SQLite schema)

            return true;
        }

        return false;
    }

    public function check()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return false;
        }

        // Check session timeout
        if (isset($_SESSION['login_time'])) {
            $elapsed = time() - $_SESSION['login_time'];
            if ($elapsed > $this->config['security']['session_lifetime']) {
                $this->logout();
                return false;
            }
        }

        return true;
    }

    public function user()
    {
        if (!$this->check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
        ];
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }

    public function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function requireAuth()
    {
        if (!$this->check()) {
            header('Location: /dashboard/login');
            exit;
        }
    }

    private function getAbsolutePath($path)
    {
        if ($path[0] === '/') {
            return $path;
        }
        return __DIR__ . '/../../' . $path;
    }
}
