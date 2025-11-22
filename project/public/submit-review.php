<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/ReviewController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit();
}

try {
    $bookId = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $reviewText = isset($_POST['review_text']) ? trim($_POST['review_text']) : null;

    if (!$bookId || !$rating) {
        echo json_encode(['success' => false, 'message' => 'Book ID and rating are required']);
        exit();
    }

    $reviewController = new ReviewController($pdo);
    $result = $reviewController->submitReview($_SESSION['user_id'], $bookId, $rating, $reviewText);

    echo json_encode($result);
} catch (Exception $e) {
    error_log("Submit Review Exception: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while submitting your review. Please try again.'
    ]);
}

