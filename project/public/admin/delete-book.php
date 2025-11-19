<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: books.php');
    exit();
}

$bookId = $_POST['book_id'] ?? null;

if (!$bookId) {
    header('Location: books.php?error=' . urlencode('Invalid book ID'));
    exit();
}

$adminController = new AdminController($pdo);
$result = $adminController->deleteBook($bookId);

if ($result['success']) {
    header('Location: books.php?success=' . urlencode($result['message']));
} else {
    header('Location: books.php?error=' . urlencode($result['message']));
}
exit();

