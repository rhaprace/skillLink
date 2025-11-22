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
        if (empty($data['terms'])) {
            return ['success' => false, 'message' => 'You must agree to the Terms and Conditions'];
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
            $this->userModel->create($data['username'], $data['email'], $data['password'], true);
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

        if (!empty($data['remember'])) {
            $this->setRememberMeCookie($user['id']);
        }

        return ['success' => true, 'message' => 'Login successful'];
    }

    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

        $this->userModel->setRememberToken($userId, $hashedToken, $expires);

        setcookie('remember_token', $token, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'samesite' => 'Lax'
        ]);
    }

    public function checkRememberMe() {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $hashedToken = hash('sha256', $token);

            $user = $this->userModel->findByRememberToken($hashedToken);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                $this->setRememberMeCookie($user['id']);

                return true;
            } else {
                $this->clearRememberMeCookie();
            }
        }

        return false;
    }

    private function clearRememberMeCookie() {
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'samesite' => 'Lax'
        ]);
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->userModel->clearRememberToken($_SESSION['user_id']);
        }

        $this->clearRememberMeCookie();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
}
