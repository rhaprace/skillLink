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

        <div class="mb-6 animate-slide-up" style="animation-delay: 100ms;">
            <div class="card p-4">
                <div class="library-filter-bar">
                    <div class="library-filter-buttons">
                        <a href="my-library.php?view=<?php echo $viewMode; ?>"
                           class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-colors whitespace-nowrap <?php echo !$statusFilter ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            All
                        </a>
                        <a href="my-library.php?status=in_progress&view=<?php echo $viewMode; ?>"
                           class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-colors whitespace-nowrap <?php echo $statusFilter === 'in_progress' ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            In Progress
                        </a>
                        <a href="my-library.php?status=completed&view=<?php echo $viewMode; ?>"
                           class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-colors whitespace-nowrap <?php echo $statusFilter === 'completed' ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            Completed
                        </a>
                    </div>

                    <div class="library-controls">
                        <?php if ($totalBooks > 0): ?>
                            <span class="results-counter">
                                Showing <?php echo $showingFrom; ?>-<?php echo $showingTo; ?> of <?php echo $totalBooks; ?>
                            </span>
                        <?php endif; ?>

                        <div class="view-toggle">
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'grid'])); ?>"
                               class="view-toggle-btn <?php echo $viewMode === 'grid' ? 'active' : ''; ?>"
                               title="Grid View">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'list'])); ?>"
                               class="view-toggle-btn <?php echo $viewMode === 'list' ? 'active' : ''; ?>"
                               title="List View">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
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
