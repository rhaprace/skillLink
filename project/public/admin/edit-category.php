<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categories.php');
    exit();
}

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$icon = trim($_POST['icon'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$id) {
    header('Location: categories.php?error=' . urlencode('Invalid category ID'));
    exit();
}

$adminController = new AdminController($pdo);
$result = $adminController->updateCategory($id, $name, $slug, $icon, $description);

if ($result['success']) {
    header('Location: categories.php?success=' . urlencode($result['message']));
} else {
    header('Location: categories.php?error=' . urlencode($result['message']));
}
exit();

