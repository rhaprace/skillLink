<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function getProfile($userId) {
        if (!$userId) {
            return null;
        }

        return $this->userModel->findById($userId);
    }

    public function updateProfile($userId, $data) {
        if (!$userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if (empty($data['username']) && empty($data['email'])) {
            return ['success' => false, 'message' => 'No data to update'];
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $updateData = [];

        if (!empty($data['username'])) {
            if (!$this->validateUsername($data['username'])) {
                return ['success' => false, 'message' => 'Username must be 3-50 characters and contain only letters, numbers, and underscores'];
            }

            if ($data['username'] !== $user['username']) {
                if ($this->userModel->checkUsernameExists($data['username'], $userId)) {
                    return ['success' => false, 'message' => 'Username already taken'];
                }
                $updateData['username'] = $data['username'];
            }
        }

        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }

            if ($data['email'] !== $user['email']) {
                if ($this->userModel->checkEmailExists($data['email'], $userId)) {
                    return ['success' => false, 'message' => 'Email already registered'];
                }
                $updateData['email'] = $data['email'];
            }
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No changes detected'];
        }

        if (!empty($data['current_password'])) {
            if (!$this->userModel->verifyPassword($data['current_password'], $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
        } else {
            return ['success' => false, 'message' => 'Current password is required to update profile'];
        }

        try {
            $result = $this->userModel->updateUser($userId, $updateData);
            
            if ($result) {
                if (isset($updateData['username'])) {
                    $_SESSION['username'] = $updateData['username'];
                }
                if (isset($updateData['email'])) {
                    $_SESSION['email'] = $updateData['email'];
                }
                
                return ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update profile'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred while updating profile'];
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        if (!$userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'All password fields are required'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'New passwords do not match'];
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!$this->userModel->verifyPassword($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        if ($currentPassword === $newPassword) {
            return ['success' => false, 'message' => 'New password must be different from current password'];
        }

        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $result = $this->userModel->updatePassword($userId, $hashedPassword);
            
            if ($result) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to change password'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred while changing password'];
        }
    }

    public function deleteAccount($userId, $password) {
        if (!$userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if (empty($password)) {
            return ['success' => false, 'message' => 'Password is required to delete account'];
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => 'Password is incorrect'];
        }

        try {
            $result = $this->userModel->deleteUser($userId);

            if ($result) {
                session_destroy();
                return ['success' => true, 'message' => 'Account deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete account'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred while deleting account'];
        }
    }

    private function validateUsername($username) {
        $length = strlen($username);
        if ($length < 3 || $length > 50) {
            return false;
        }

        return preg_match('/^[a-zA-Z0-9_]+$/', $username);
    }
}

