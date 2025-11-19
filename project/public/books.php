<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

$pageTitle = 'Browse Books - SkillLink';
$bookController = new BookController($pdo);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;

$books = $bookController->getAllBooks($categoryId);
$categories = $bookController->getCategories();

$userProgressMap = [];
if ($userId) {
    require_once '../src/models/UserProgress.php';
    $progressModel = new UserProgress($pdo);
    $allProgress = $progressModel->getAllUserProgress($userId);
    foreach ($allProgress as $progress) {
        $userProgressMap[$progress['book_id']] = $progress;
    }
}

require_once '../src/includes/header.php';
?>

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

        <div id="booksGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($books as $index => $book): ?>
                    <?php
                    $hasProgress = isset($userProgressMap[$book['id']]);
                    $progress = $hasProgress ? $userProgressMap[$book['id']] : null;
                    ?>
                    <a href="book.php?id=<?php echo $book['id']; ?>"
                       class="book-card card card-hover animate-slide-up"
                       data-book-id="<?php echo $book['id']; ?>"
                       data-category-id="<?php echo $book['category_id'] ?? ''; ?>"
                       data-title="<?php echo htmlspecialchars($book['title']); ?>"
                       data-author="<?php echo htmlspecialchars($book['author'] ?? ''); ?>"
                       data-description="<?php echo htmlspecialchars($book['description']); ?>"
                       data-category="<?php echo htmlspecialchars($book['category_name'] ?? ''); ?>"
                       style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">
                        <div class="p-6">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <?php if ($book['category_name']): ?>
                                    <span class="book-category inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                        <?php echo htmlspecialchars($book['category_name']); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($hasProgress): ?>
                                    <?php if ($progress['status'] === 'completed'): ?>
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                            ✓ Completed
                                        </span>
                                    <?php elseif ($progress['status'] === 'in_progress'): ?>
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                            In Progress
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <h3 class="book-title text-xl font-bold text-black mb-2 line-clamp-2">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </h3>

                            <?php if ($book['author']): ?>
                                <p class="text-sm text-gray-600 mb-3">
                                    by <span class="book-author"><?php echo htmlspecialchars($book['author']); ?></span>
                                </p>
                            <?php endif; ?>

                            <p class="book-description text-gray-600 text-sm mb-4 line-clamp-3">
                                <?php echo htmlspecialchars($book['description']); ?>
                            </p>

                            <?php if ($hasProgress && $progress['progress_percentage'] > 0): ?>
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-600">Your Progress</span>
                                        <span class="font-semibold text-black">
                                            <?php echo round($progress['progress_percentage']); ?>%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-black h-1.5 rounded-full transition-all"
                                             style="width: <?php echo $progress['progress_percentage']; ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-100">
                                <span class="flex items-center gap-1">
                                    ⏱️ <?php echo $book['estimated_duration']; ?> min
                                </span>
                                <span class="capitalize px-2 py-1 bg-gray-50 rounded">
                                    <?php echo htmlspecialchars($book['difficulty_level']); ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
<script src="assets/js/book-search.js"></script>

<?php require_once '../src/includes/footer.php'; ?>
