<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookmarkController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to bookmark books'
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
$bookmarkController = new BookmarkController($pdo);

$result = $bookmarkController->toggleBookmark($userId, $bookId);

echo json_encode($result);
exit();

