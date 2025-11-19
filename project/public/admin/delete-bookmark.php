<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: bookmarks.php');
    exit();
}

$bookmarkId = $_POST['bookmark_id'] ?? null;

if (!$bookmarkId) {
    header('Location: bookmarks.php?error=' . urlencode('Invalid bookmark ID'));
    exit();
}

$adminController = new AdminController($pdo);
$result = $adminController->deleteBookmark($bookmarkId);

if ($result['success']) {
    header('Location: bookmarks.php?success=' . urlencode($result['message']));
} else {
    header('Location: bookmarks.php?error=' . urlencode($result['message']));
}
exit();

