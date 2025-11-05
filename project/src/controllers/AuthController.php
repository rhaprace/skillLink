<?php
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }
    public function register($data) {
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['confirm_password'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        if ($data['password'] !== $data['confirm_password']) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }
        if ($this->userModel->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        if ($this->userModel->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username already taken'];
        }
        try {
            $this->userModel->create($data['username'], $data['email'], $data['password']);
            return ['success' => true, 'message' => 'Registration successful! You can now login.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    public function login($data)
    {
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        $user = $this->userModel->findByEmail($data['email']);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        if (!$this->userModel->verifyPassword($data['password'], $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        return ['success' => true, 'message' => 'Login successful'];
    }

    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
}
