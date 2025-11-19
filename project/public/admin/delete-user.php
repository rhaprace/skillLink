<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit();
}

$userId = $_POST['user_id'] ?? null;

if (!$userId) {
    header('Location: users.php?error=' . urlencode('Invalid user ID'));
    exit();
}

$adminController = new AdminController($pdo);
$result = $adminController->deleteUser($userId);

if ($result['success']) {
    header('Location: users.php?success=' . urlencode($result['message']));
} else {
    header('Location: users.php?error=' . urlencode($result['message']));
}
exit();

