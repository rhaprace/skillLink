<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categories.php');
    exit();
}

$categoryId = $_POST['category_id'] ?? null;

if (!$categoryId) {
    header('Location: categories.php?error=' . urlencode('Invalid category ID'));
    exit();
}

$adminController = new AdminController($pdo);
$result = $adminController->deleteCategory($categoryId);

if ($result['success']) {
    header('Location: categories.php?success=' . urlencode($result['message']));
} else {
    header('Location: categories.php?error=' . urlencode($result['message']));
}
exit();

