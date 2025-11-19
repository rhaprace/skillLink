<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/UserController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userController = new UserController($pdo);

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$result = $userController->changePassword($userId, $currentPassword, $newPassword, $confirmPassword);

if ($result['success']) {
    header('Location: profile.php?success=' . urlencode($result['message']));
} else {
    header('Location: profile.php?error=' . urlencode($result['message']));
}
exit();

