<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/models/UserProgress.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to reset progress'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$bookId = isset($data['book_id']) ? intval($data['book_id']) : 0;

if ($bookId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid book ID'
    ]);
    exit();
}

$userId = $_SESSION['user_id'];
$progressModel = new UserProgress($pdo);

try {
    $result = $progressModel->updateProgress($userId, $bookId, 0);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Progress reset successfully. You can start reading again!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to reset progress'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while resetting progress'
    ]);
}
exit();

