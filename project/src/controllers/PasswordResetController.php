<?php
require_once __DIR__ . '/../models/User.php';

class PasswordResetController
{
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function requestReset($data) {
        if (empty($data['email'])) {
            return ['success' => false, 'message' => 'Email address is required'];
        }

        $email = trim($data['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (!$this->checkRateLimit($email)) {
            return ['success' => false, 'message' => 'Too many reset requests. Please try again in 1 hour.'];
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => true, 'message' => 'If an account exists with this email, you will receive a password reset link shortly.'];
        }

        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':token' => $hashedToken,
            ':expires' => $expires,
            ':id' => $user['id']
        ]);

        $this->logResetAttempt($email);

        $this->sendResetEmail($email, $token, $user['username']);

        return ['success' => true, 'message' => 'If an account exists with this email, you will receive a password reset link shortly.'];
    }

    public function validateToken($token) {
        if (empty($token)) {
            return false;
        }

        $hashedToken = hash('sha256', $token);

        $sql = "SELECT id FROM users WHERE password_reset_token = :token AND password_reset_expires > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $hashedToken]);

        return $stmt->fetch() !== false;
    }

    public function resetPassword($data) {
        if (empty($data['token']) || empty($data['password']) || empty($data['confirm_password'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        if ($data['password'] !== $data['confirm_password']) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }

        $hashedToken = hash('sha256', $data['token']);

        $sql = "SELECT id FROM users WHERE password_reset_token = :token AND password_reset_expires > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $hashedToken]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid or expired reset token'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = :password, password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $user['id']
        ]);

        return ['success' => true, 'message' => 'Your password has been reset successfully. You can now login with your new password.'];
    }

    private function checkRateLimit($email) {
        $sql = "SELECT COUNT(*) as count FROM password_reset_attempts WHERE email = :email AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();

        return $result['count'] < 3;
    }

    private function logResetAttempt($email) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

        $sql = "INSERT INTO password_reset_attempts (email, attempt_time, ip_address) VALUES (:email, NOW(), :ip)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':ip' => $ipAddress
        ]);
    }

    private function sendResetEmail($email, $token, $username) {
        $resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=" . $token;

        $subject = "Password Reset Request - SkillLink";
        $message = $this->getEmailTemplate($username, $resetLink);

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SkillLink <noreply@skilllink.com>" . "\r\n";

        mail($email, $subject, $message, $headers);
    }

    private function getEmailTemplate($username, $resetLink) {
        $template = file_get_contents(__DIR__ . '/../templates/password-reset-email.html');
        $template = str_replace('{{USERNAME}}', htmlspecialchars($username), $template);
        $template = str_replace('{{RESET_LINK}}', $resetLink, $template);
        return $template;
    }
}

