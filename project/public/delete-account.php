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
$password = $_POST['password'] ?? '';

$userController = new UserController($pdo);
$result = $userController->deleteAccount($userId, $password);

if ($result['success']) {
    header('Location: login.php?success=' . urlencode('Your account has been deleted successfully'));
} else {
    header('Location: profile.php?error=' . urlencode($result['message']));
}
exit();

