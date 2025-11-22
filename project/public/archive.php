<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/models/UserProgress.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=' . urlencode('Please login to view your reading archive'));
    exit();
}

$pageTitle = 'Reading Archive - SkillLink';
$progressModel = new UserProgress($pdo);
$userId = $_SESSION['user_id'];

$completedBooks = $progressModel->getAllUserProgress($userId, 'completed');

require_once '../src/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/library.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link rel="stylesheet" href="assets/css/books-carousel.css">

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">Reading Archive</h1>
            <p class="text-gray-600">Books you've completed</p>
        </div>

        <?php if (count($completedBooks) > 0): ?>
        <div id="booksGrid" class="books-grid animate-slide-up hidden md:grid" style="animation-delay: 100ms;">
            <?php foreach ($completedBooks as $index => $book): ?>
                <?php
                $bookId = $book['book_id'] ?? $book['id'];
                if (!empty($book['cover_image']) && trim($book['cover_image']) !== '') {
                    $coverImage = $book['cover_image'];
                } else {
                    $placeholderNum = (($bookId - 1) % 4) + 1;
                    $coverImage = 'placeholder-' . $placeholderNum . '.jpg';
                }
                ?>
                <div class="card card-hover book-card animate-slide-up view-transition overflow-hidden"
                     data-title="<?php echo htmlspecialchars(strtolower($book['title'])); ?>"
                     data-author="<?php echo htmlspecialchars(strtolower($book['author'] ?? '')); ?>"
                     data-category="<?php echo htmlspecialchars(strtolower($book['category_name'] ?? '')); ?>"
                     data-completed-at="<?php echo strtotime($book['completed_at'] ?? $book['updated_at']); ?>"
                     data-book-id="<?php echo $bookId; ?>"
                     style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">

                    <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
                        <img
                            src="assets/images/book-covers/<?php echo htmlspecialchars($coverImage); ?>"
                            alt="<?php echo htmlspecialchars($book['title']); ?> cover"
                            class="w-full h-full object-cover"
                            onerror="this.src='assets/images/book-covers/placeholder-1.jpg'"
                        >
                    </div>

                    <div class="p-6 relative">
                        <div class="mb-3 flex items-start justify-between gap-2" style="min-height: 28px;">
                            <div class="flex flex-wrap items-center gap-2">
                                <?php if (!empty($book['category_name'])): ?>
                                    <span class="book-category inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                        <?php echo htmlspecialchars($book['category_name']); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                    ✓ Completed
                                </span>
                            </div>
                        </div>

                        <h3 class="book-title text-lg font-bold text-black mb-2 line-clamp-2" style="min-height: 3.5rem;">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h3>

                        <div class="mb-3" style="min-height: 20px;">
                            <?php if (!empty($book['author'])): ?>
                                <p class="text-sm text-gray-600">
                                    by <span class="book-author"><?php echo htmlspecialchars($book['author']); ?></span>
                                </p>
                            <?php endif; ?>
                        </div>

                        <p class="book-description text-gray-600 text-sm mb-4 line-clamp-2" style="min-height: 2.5rem;">
                            <?php echo htmlspecialchars($book['description'] ?? ''); ?>
                        </p>

                        <div class="mb-4" style="min-height: 52px;">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Completed on</span>
                                <span class="font-semibold text-black">
                                    <?php
                                    $completedDate = new DateTime($book['completed_at'] ?? $book['updated_at']);
                                    echo $completedDate->format('M j, Y');
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <?php echo $book['estimated_duration'] ?? 'N/A'; ?> mins
                            </span>
                            <span class="book-difficulty capitalize px-2 py-1 bg-gray-50 rounded">
                                <?php echo htmlspecialchars($book['difficulty_level'] ?? 'N/A'); ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 flex gap-2">
                        <a href="book.php?id=<?php echo $bookId; ?>" class="btn btn-primary btn-sm flex-1">
                            View Book
                        </a>
                        <button class="btn btn-secondary btn-sm flex-1 read-again-btn"
                                data-book-id="<?php echo $bookId; ?>"
                                data-book-title="<?php echo htmlspecialchars($book['title']); ?>">
                            Read Again
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="booksCarousel" class="block md:hidden">
            <div class="swiper books-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($completedBooks as $index => $book): ?>
                        <?php
                        $bookId = $book['book_id'] ?? $book['id'];
                        if (!empty($book['cover_image']) && trim($book['cover_image']) !== '') {
                            $coverImage = $book['cover_image'];
                        } else {
                            $placeholderNum = (($bookId - 1) % 4) + 1;
                            $coverImage = 'placeholder-' . $placeholderNum . '.jpg';
                        }
                        ?>
                        <div class="swiper-slide">
                            <div class="card card-hover book-card animate-slide-up view-transition overflow-hidden"
                                 data-title="<?php echo htmlspecialchars(strtolower($book['title'])); ?>"
                                 data-author="<?php echo htmlspecialchars(strtolower($book['author'] ?? '')); ?>"
                                 data-category="<?php echo htmlspecialchars(strtolower($book['category_name'] ?? '')); ?>"
                                 data-completed-at="<?php echo strtotime($book['completed_at'] ?? $book['updated_at']); ?>"
                                 data-book-id="<?php echo $bookId; ?>"
                                 style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">

                                <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
                                    <img
                                        src="assets/images/book-covers/<?php echo htmlspecialchars($coverImage); ?>"
                                        alt="<?php echo htmlspecialchars($book['title']); ?> cover"
                                        class="w-full h-full object-cover"
                                        onerror="this.src='assets/images/book-covers/placeholder-1.jpg'"
                                    >
                                </div>

                                <div class="p-6 relative">
                                    <div class="mb-3 flex items-start justify-between gap-2" style="min-height: 28px;">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <?php if (!empty($book['category_name'])): ?>
                                                <span class="book-category inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                                    <?php echo htmlspecialchars($book['category_name']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                                ✓ Completed
                                            </span>
                                        </div>
                                    </div>

                                    <h3 class="book-title text-lg font-bold text-black mb-2 line-clamp-2" style="min-height: 3.5rem;">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h3>

                                    <div class="mb-3" style="min-height: 20px;">
                                        <?php if (!empty($book['author'])): ?>
                                            <p class="text-sm text-gray-600">
                                                by <span class="book-author"><?php echo htmlspecialchars($book['author']); ?></span>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <p class="book-description text-gray-600 text-sm mb-4 line-clamp-2" style="min-height: 2.5rem;">
                                        <?php echo htmlspecialchars($book['description'] ?? ''); ?>
                                    </p>

                                    <div class="mb-4" style="min-height: 52px;">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Completed on</span>
                                            <span class="font-semibold text-black">
                                                <?php
                                                $completedDate = new DateTime($book['completed_at'] ?? $book['updated_at']);
                                                echo $completedDate->format('M j, Y');
                                                ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <?php echo $book['estimated_duration'] ?? 'N/A'; ?> mins
                                        </span>
                                        <span class="book-difficulty capitalize px-2 py-1 bg-gray-50 rounded">
                                            <?php echo htmlspecialchars($book['difficulty_level'] ?? 'N/A'); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="p-4 flex gap-2">
                                    <a href="book.php?id=<?php echo $bookId; ?>" class="btn btn-primary btn-sm flex-1">
                                        View Book
                                    </a>
                                    <button class="btn btn-secondary btn-sm flex-1 read-again-btn"
                                            data-book-id="<?php echo $bookId; ?>"
                                            data-book-title="<?php echo htmlspecialchars($book['title']); ?>">
                                        Read Again
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination mt-6"></div>
            </div>
        </div>

        <?php else: ?>
        <div class="text-center py-16 animate-fade-in">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">No Completed Books Yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                You haven't completed any books yet. Start reading to build your archive of completed books!
            </p>
            <a href="books.php" class="btn btn-primary">
                Browse Books
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="readAgainModal" class="modal hidden">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <h3 class="text-xl font-bold text-black mb-4">Read Again?</h3>
        <p class="text-gray-600 mb-6">
            Are you sure you want to restart "<span id="modalBookTitle"></span>"? This will reset your progress to 0%.
        </p>
        <div class="flex gap-3 justify-end">
            <button id="cancelReadAgain" class="btn btn-secondary">Cancel</button>
            <button id="confirmReadAgain" class="btn btn-primary">Yes, Read Again</button>
        </div>
    </div>
</div>

<script src="assets/js/archive-page.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="assets/js/books-carousel.js"></script>

<?php require_once '../src/includes/footer.php'; ?>

