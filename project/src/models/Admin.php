<?php

class Admin
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function authenticate($email, $password) {
        $sql = "SELECT * FROM admins WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        
        return false;
    }

    public function getById($id) {
        $sql = "SELECT id, username, email, role, created_at, updated_at FROM admins WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $sql = "SELECT id, username, email, role, created_at, updated_at FROM admins WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = "SELECT id, username, email, role, created_at, updated_at FROM admins ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function create($username, $email, $password, $role = 'admin') {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO admins (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role' => $role
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $username, $email, $role) {
        try {
            $sql = "UPDATE admins SET username = :username, email = :email, role = :role WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':username' => $username,
                ':email' => $email,
                ':role' => $role
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE admins SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':password' => $hashedPassword
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM admins WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function checkEmailExists($email, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM admins WHERE email = :email AND id != :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM admins WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
        }
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function checkUsernameExists($username, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM admins WHERE username = :username AND id != :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username' => $username, ':id' => $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM admins WHERE username = :username";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
        }
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM admins";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
}

