<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    // Show login form
    public function loginForm()
    {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    // Handle login
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->user->login($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                
                // Create session record
                $this->createSessionRecord($user['id']);
                
                header('Location: /dashboard');
                exit;
            }

            $_SESSION['error'] = 'Invalid email or password';
        }

        $this->loginForm();
    }

    // Show registration form
    public function registerForm()
    {
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    // Handle registration
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match';
                $this->registerForm();
                return;
            }

            if ($this->user->register($name, $email, $password)) {
                $_SESSION['success'] = 'Registration successful! Please login.';
                header('Location: /login');
                exit;
            }

            $_SESSION['error'] = 'Registration failed';
        }

        $this->registerForm();
    }

    // Logout user
    public function logout()
    {
        // Delete session record
        $this->deleteSessionRecord($_SESSION['user_id']);
        
        session_destroy();
        header('Location: /login');
        exit;
    }

    // Create session record
    private function createSessionRecord($userId)
    {
        $data = [
            'user_id' => $userId,
            'session_id' => session_id(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        return insert('user_sessions', $data);
    }

    // Delete session record
    private function deleteSessionRecord($userId)
    {
        return delete('user_sessions', "user_id = ? AND session_id = ?", [$userId, session_id()]);
    }
}
