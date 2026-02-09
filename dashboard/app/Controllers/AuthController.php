<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Database;

class AuthController
{
    private $config;
    private $auth;
    private $view;
    private $db;

    public function __construct($config)
    {
        global $auth, $view, $db;
        $this->config = $config;
        $this->auth = $auth;
        $this->view = $view;
        $this->db = $db;
    }

    public function showLogin()
    {
        if ($this->auth->check()) {
            $this->view->redirect('/dashboard');
            return;
        }

        echo $this->view->render('auth.login', [], 'auth');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->redirect('/dashboard/login');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->auth->attempt($username, $password)) {
            $this->view->redirect('/dashboard');
        } else {
            echo $this->view->render('auth.login', [
                'error' => 'Invalid username or password',
            ], 'auth');
        }
    }

    public function logout()
    {
        $this->auth->logout();
        $this->view->redirect('/dashboard/login');
    }
}
