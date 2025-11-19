<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: books.php');
    exit();
}

$userId = $_SESSION['user_id'];
$bookId = intval($_GET['id']);

$bookController = new BookController($pdo);

$result = $bookController->startBook($userId, $bookId);

if ($result) {
    header('Location: book.php?id=' . $bookId);
} else {
    header('Location: book.php?id=' . $bookId);
}
exit();

