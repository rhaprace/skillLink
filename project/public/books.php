<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';
require_once '../src/controllers/BookmarkController.php';

$pageTitle = 'Browse Books - SkillLink';
$bookController = new BookController($pdo);
$bookmarkController = new BookmarkController($pdo);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;

$books = $bookController->getAllBooks($categoryId);
$categories = $bookController->getCategories();

$userProgressMap = [];
$userBookmarkIds = [];
$completedBookIds = [];
if ($userId) {
    require_once '../src/models/UserProgress.php';
    $progressModel = new UserProgress($pdo);
    $allProgress = $progressModel->getAllUserProgress($userId);
    foreach ($allProgress as $progress) {
        $userProgressMap[$progress['book_id']] = $progress;
        if ($progress['status'] === 'completed' || $progress['progress_percentage'] >= 100) {
            $completedBookIds[] = $progress['book_id'];
        }
    }

    $userBookmarkIds = $bookmarkController->getUserBookmarkIds($userId);

    $books = array_filter($books, function($book) use ($completedBookIds) {
        return !in_array($book['id'], $completedBookIds);
    });
}

require_once '../src/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/library.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link rel="stylesheet" href="assets/css/books-carousel.css">

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">Browse Learning Materials</h1>
            <p class="text-gray-600">Explore our collection of books and tutorials</p>
        </div>
        <div class="mb-8 animate-slide-up" style="animation-delay: 100ms;">
            <div class="card p-6">
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1 min-w-0">
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Search books by title, author, description, or category..."
                                class="form-input w-full"
                                autocomplete="off"
                            >
                            <button
                                id="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                                type="button"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            data-category="all"
                            class="category-filter px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-colors whitespace-nowrap <?php echo !$categoryId ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            All
                        </button>
                        <?php foreach ($categories as $category): ?>
                            <button
                                data-category="<?php echo $category['id']; ?>"
                                class="category-filter px-3 py-1.5 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-colors whitespace-nowrap <?php echo $categoryId == $category['id'] ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                <span class="hidden sm:inline"><?php echo htmlspecialchars($category['icon']); ?> </span><?php echo htmlspecialchars($category['name']); ?>
                                <span class="ml-1 text-xs opacity-75">(<?php echo $category['books_count']; ?>)</span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="resultsCount" class="mb-4 animate-fade-in" style="animation-delay: 150ms;">
            <p class="text-gray-600">
                Found <span id="bookCount" class="font-semibold text-black"><?php echo count($books); ?></span>
                <span id="bookLabel"><?php echo count($books) === 1 ? 'book' : 'books'; ?></span>
            </p>
        </div>

        <div id="noResults" class="card p-12 text-center animate-fade-in hidden" style="animation-delay: 200ms;">
            <p class="text-gray-500 text-lg mb-4">No books found matching your search</p>
            <button id="resetSearch" class="btn btn-primary">Clear Search</button>
        </div>
        <div id="booksGrid" class="library-grid-container hidden md:grid">
            <?php foreach ($books as $index => $book): ?>
                <?php require '../src/includes/components/book-card-grid.php'; ?>
            <?php endforeach; ?>
        </div>
        <div id="booksCarousel" class="block md:hidden">
            <div class="swiper books-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($books as $index => $book): ?>
                        <div class="swiper-slide">
                            <?php require '../src/includes/components/book-card-grid.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination mt-6"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="assets/js/book-search.js"></script>
<script src="assets/js/bookmarks.js"></script>
<script src="assets/js/books-carousel.js"></script>

<?php require_once '../src/includes/footer.php'; ?>
