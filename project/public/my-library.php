<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'My Library - SkillLink';
$userId = $_SESSION['user_id'];
$bookController = new BookController($pdo);

$booksPerPage = 12;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $booksPerPage;

$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

$viewMode = isset($_GET['view']) ? $_GET['view'] : 'grid';

$totalBooks = $bookController->getUserLibraryCount($userId, $statusFilter);
$totalPages = ceil($totalBooks / $booksPerPage);

$userBooks = $bookController->getUserLibrary($userId, $statusFilter, $booksPerPage, $offset);

$stats = $bookController->getUserDashboardStats($userId);

$showingFrom = $totalBooks > 0 ? $offset + 1 : 0;
$showingTo = min($offset + $booksPerPage, $totalBooks);

require_once '../src/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/library.css">

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">My Library</h1>
            <p class="text-gray-600">Track your learning progress and continue where you left off</p>
        </div>
        <div class="library-stats-grid animate-slide-up" style="animation-delay: 50ms;">
            <div class="card p-6">
                <p class="text-sm text-gray-600 uppercase tracking-wide mb-1">Total Books</p>
                <p class="text-3xl font-bold text-black"><?php echo $stats['total_books']; ?></p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-gray-600 uppercase tracking-wide mb-1">In Progress</p>
                <p class="text-3xl font-bold text-black"><?php echo $stats['in_progress_books']; ?></p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-gray-600 uppercase tracking-wide mb-1">Completed</p>
                <p class="text-3xl font-bold text-black"><?php echo $stats['completed_books']; ?></p>
            </div>
        </div>



        <?php if (empty($userBooks)): ?>
            <div class="library-empty-state card animate-fade-in" style="animation-delay: 150ms;">
                <p class="text-gray-500 text-lg mb-4">
                    <?php if ($statusFilter): ?>
                        No <?php echo str_replace('_', ' ', $statusFilter); ?> books found
                    <?php else: ?>
                        You haven't started reading any books yet
                    <?php endif; ?>
                </p>
                <a href="books.php" class="btn btn-primary">Browse Books</a>
            </div>
        <?php else: ?>

            <?php if ($viewMode === 'grid'): ?>
                <div class="library-grid-container view-transition">
                    <?php foreach ($userBooks as $index => $item): ?>
                        <?php require '../src/includes/components/library-book-card-grid.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="library-list-container view-transition">
                    <?php foreach ($userBooks as $index => $item): ?>
                        <?php require '../src/includes/components/library-book-card-list.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php require '../src/includes/components/pagination.php'; ?>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/library-pagination.js"></script>

<?php require_once '../src/includes/footer.php'; ?>
