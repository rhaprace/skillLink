<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookmarkController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=' . urlencode('Please login to view your bookmarks'));
    exit();
}

$pageTitle = 'My Bookmarks - SkillLink';
$bookmarkController = new BookmarkController($pdo);
$userId = $_SESSION['user_id'];

$bookmarks = $bookmarkController->getUserBookmarks($userId);

require_once '../src/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/library.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link rel="stylesheet" href="assets/css/books-carousel.css">

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">My Bookmarks</h1>
            <p class="text-gray-600">Books you've saved for later</p>
        </div>

        <?php if (count($bookmarks) > 0): ?>
        <div id="booksGrid" class="books-grid animate-slide-up hidden md:grid" style="animation-delay: 100ms;">
            <?php foreach ($bookmarks as $index => $book): ?>
                <?php
                $bookId = $book['id'] ?? $book['book_id'];
                if (!empty($book['cover_image']) && trim($book['cover_image']) !== '') {
                    $coverImage = $book['cover_image'];
                } else {
                    $placeholderNum = (($bookId - 1) % 4) + 1;
                    $coverImage = 'placeholder-' . $placeholderNum . '.jpg';
                }

                $hasProgress = isset($book['progress_percentage']);
                $progressPercentage = $hasProgress ? $book['progress_percentage'] : 0;
                $status = $book['status'] ?? 'not_started';
                ?>
                <div class="card card-hover book-card animate-slide-up view-transition overflow-hidden"
                     data-book-id="<?php echo $bookId; ?>"
                     data-title="<?php echo htmlspecialchars(strtolower($book['title'])); ?>"
                     data-author="<?php echo htmlspecialchars(strtolower($book['author'] ?? '')); ?>"
                     data-category="<?php echo htmlspecialchars(strtolower($book['category_name'] ?? '')); ?>"
                     data-bookmarked-at="<?php echo strtotime($book['bookmarked_at']); ?>"
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

                                <?php if ($hasProgress): ?>
                                    <?php if ($status === 'completed'): ?>
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                            Read completed
                                        </span>
                                    <?php elseif ($status === 'in_progress'): ?>
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                            Learning in progress
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <button
                                class="bookmark-btn flex-shrink-0 p-1.5 rounded-full hover:bg-gray-100 transition-colors"
                                data-book-id="<?php echo $bookId; ?>"
                                data-bookmarked="true"
                                title="Remove bookmark">
                                <svg class="w-5 h-5 bookmark-icon" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                            </button>
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

                        <?php if ($hasProgress && $progressPercentage > 0): ?>
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-semibold text-black">
                                        <?php echo round($progressPercentage); ?>%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-black h-2 rounded-full transition-all"
                                         style="width: <?php echo $progressPercentage; ?>%">
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-4" style="min-height: 52px;"></div>
                        <?php endif; ?>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <?php echo $book['estimated_duration'] ?? 'N/A'; ?> mins
                            </span>
                            <span class="book-difficulty capitalize px-2 py-1 bg-gray-50 rounded">
                                <?php echo htmlspecialchars($book['difficulty_level'] ?? 'N/A'); ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-4">
                        <?php if ($hasProgress): ?>
                            <a href="book.php?id=<?php echo $bookId; ?>"
                               class="btn btn-primary btn-sm w-full">
                                Read
                            </a>
                        <?php else: ?>
                            <a href="start-book.php?id=<?php echo $bookId; ?>"
                               class="btn btn-primary btn-sm w-full">
                                Learn
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="booksCarousel" class="block md:hidden">
            <div class="swiper books-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($bookmarks as $index => $book): ?>
                        <?php
                        $bookId = $book['id'] ?? $book['book_id'];
                        if (!empty($book['cover_image']) && trim($book['cover_image']) !== '') {
                            $coverImage = $book['cover_image'];
                        } else {
                            $placeholderNum = (($bookId - 1) % 4) + 1;
                            $coverImage = 'placeholder-' . $placeholderNum . '.jpg';
                        }

                        $hasProgress = isset($book['progress_percentage']);
                        $progressPercentage = $hasProgress ? $book['progress_percentage'] : 0;
                        $status = $book['status'] ?? 'not_started';
                        ?>
                        <div class="swiper-slide">
                            <div class="card card-hover book-card animate-slide-up view-transition overflow-hidden"
                                 data-book-id="<?php echo $bookId; ?>"
                                 data-title="<?php echo htmlspecialchars(strtolower($book['title'])); ?>"
                                 data-author="<?php echo htmlspecialchars(strtolower($book['author'] ?? '')); ?>"
                                 data-category="<?php echo htmlspecialchars(strtolower($book['category_name'] ?? '')); ?>"
                                 data-bookmarked-at="<?php echo strtotime($book['bookmarked_at']); ?>"
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

                                            <?php if ($hasProgress): ?>
                                                <?php if ($status === 'completed'): ?>
                                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                                        Read completed
                                                    </span>
                                                <?php elseif ($status === 'in_progress'): ?>
                                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                                        Learning in progress
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <button
                                            class="bookmark-btn flex-shrink-0 p-1.5 rounded-full hover:bg-gray-100 transition-colors"
                                            data-book-id="<?php echo $bookId; ?>"
                                            data-bookmarked="true"
                                            title="Remove bookmark">
                                            <svg class="w-5 h-5 bookmark-icon" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                            </svg>
                                        </button>
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

                                    <?php if ($hasProgress && $progressPercentage > 0): ?>
                                        <div class="mb-4">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600">Progress</span>
                                                <span class="font-semibold text-black">
                                                    <?php echo round($progressPercentage); ?>%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-black h-2 rounded-full transition-all"
                                                     style="width: <?php echo $progressPercentage; ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mb-4" style="min-height: 52px;"></div>
                                    <?php endif; ?>

                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <?php echo $book['estimated_duration'] ?? 'N/A'; ?> mins
                                        </span>
                                        <span class="book-difficulty capitalize px-2 py-1 bg-gray-50 rounded">
                                            <?php echo htmlspecialchars($book['difficulty_level'] ?? 'N/A'); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <?php if ($hasProgress): ?>
                                        <a href="book.php?id=<?php echo $bookId; ?>"
                                           class="btn btn-primary btn-sm w-full">
                                            Read
                                        </a>
                                    <?php else: ?>
                                        <a href="start-book.php?id=<?php echo $bookId; ?>"
                                           class="btn btn-primary btn-sm w-full">
                                            Learn
                                        </a>
                                    <?php endif; ?>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">No Bookmarks Yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                You haven't bookmarked any books yet. Browse our catalog to find books you'd like to save for later.
            </p>
            <a href="books.php" class="btn btn-primary">
                Browse Books
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/bookmarks.js"></script>
<script src="assets/js/bookmarks-page.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="assets/js/books-carousel.js"></script>

<?php require_once '../src/includes/footer.php'; ?>

