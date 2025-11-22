<?php
error_reporting(0);
ini_set('display_errors', 0);

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
    $reviewId = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;

    if (!$reviewId) {
        echo json_encode(['success' => false, 'message' => 'Review ID is required']);
        exit();
    }

    $reviewController = new ReviewController($pdo);
    $result = $reviewController->deleteReview($reviewId, $_SESSION['user_id']);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please ensure review tables are created.']);
}

