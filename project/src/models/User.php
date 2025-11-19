<?php

class User
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
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
}
