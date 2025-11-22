<?php

class User
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($username, $email, $password, $termsAccepted = false) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $termsAcceptedAt = $termsAccepted ? date('Y-m-d H:i:s') : null;

        $sql = "INSERT INTO users (username, email, password, terms_accepted_at, created_at) VALUES (:username, :email, :password, :terms_accepted_at, NOW())";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':terms_accepted_at' => $termsAcceptedAt
        ]);
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);

        return $stmt->fetch();
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function updateUser($userId, $data) {
        $fields = [];
        $params = [':id' => $userId];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params[':username'] = $data['username'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function updatePassword($userId, $hashedPassword) {
        $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $userId]);
    }

    public function checkUsernameExists($username, $excludeUserId = null) {
        if ($excludeUserId) {
            $sql = "SELECT id FROM users WHERE username = :username AND id != :exclude_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':exclude_id' => $excludeUserId
            ]);
        } else {
            $sql = "SELECT id FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
        }

        return $stmt->fetch() !== false;
    }

    public function checkEmailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $sql = "SELECT id FROM users WHERE email = :email AND id != :exclude_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':exclude_id' => $excludeUserId
            ]);
        } else {
            $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
        }

        return $stmt->fetch() !== false;
    }

    public function setRememberToken($userId, $hashedToken, $expires) {
        $sql = "UPDATE users SET remember_token = :token, remember_token_expires = :expires WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':token' => $hashedToken,
            ':expires' => $expires,
            ':id' => $userId
        ]);
    }

    public function findByRememberToken($hashedToken) {
        $sql = "SELECT * FROM users WHERE remember_token = :token AND remember_token_expires > NOW() LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $hashedToken]);
        return $stmt->fetch();
    }

    public function clearRememberToken($userId) {
        $sql = "UPDATE users SET remember_token = NULL, remember_token_expires = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }
}
