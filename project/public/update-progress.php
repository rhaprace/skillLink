<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: books.php');
    exit();
}

$userId = $_SESSION['user_id'];
$bookId = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$progress = isset($_POST['progress']) ? floatval($_POST['progress']) : 0;

if ($bookId <= 0 || $progress < 0 || $progress > 100) {
    header('Location: books.php');
    exit();
}

$bookController = new BookController($pdo);
$result = $bookController->updateProgress($userId, $bookId, $progress);

header('Location: book.php?id=' . $bookId);
exit();
