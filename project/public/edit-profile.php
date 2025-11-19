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

$data = [
    'username' => trim($_POST['username'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'current_password' => $_POST['current_password'] ?? ''
];

$result = $userController->updateProfile($userId, $data);

if ($result['success']) {
    header('Location: profile.php?success=' . urlencode($result['message']));
} else {
    header('Location: profile.php?error=' . urlencode($result['message']));
}
exit();

