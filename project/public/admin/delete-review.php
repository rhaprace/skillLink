<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reviews.php');
    exit();
}

$reviewId = $_POST['review_id'] ?? null;

if (!$reviewId) {
    header('Location: reviews.php?error=' . urlencode('Invalid review ID'));
    exit();
}

$adminController = new AdminController($pdo);
$result = $adminController->deleteReview($reviewId);

if ($result['success']) {
    header('Location: reviews.php?success=' . urlencode($result['message']));
} else {
    header('Location: reviews.php?error=' . urlencode($result['message']));
}
exit();

