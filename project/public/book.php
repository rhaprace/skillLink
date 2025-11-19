<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

if (!isset($_GET['id'])) {
    header('Location: books.php');
    exit();
}

$bookId = intval($_GET['id']);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$bookController = new BookController($pdo);
$book = $bookController->getBook($bookId, $userId);

if (!$book) {
    header('Location: books.php');
    exit();
}

$bookController->trackView($bookId, $userId);

$pageTitle = htmlspecialchars($book['title']) . ' - SkillLink';

require_once '../src/includes/header.php';
?>

<div class="min-h-screen bg-white">
    <div class="container-custom py-8">
        <div class="mb-6 animate-fade-in">
            <a href="books.php" class="inline-flex items-center text-gray-600 hover:text-black transition-colors">
                <span class="mr-2">←</span> Back to Books
            </a>
        </div>
        <div class="mb-8 animate-slide-up" style="animation-delay: 50ms;">
            <div class="card p-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="flex-1">
                        <?php if ($book['category_name']): ?>
                            <div class="mb-3">
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                                    <?php echo htmlspecialchars($book['category_name']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <h1 class="text-4xl font-bold text-black mb-3">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h1>
                        <?php if ($book['author']): ?>
                            <p class="text-lg text-gray-600 mb-4">
                                by <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                        <?php endif; ?>
                        <p class="text-gray-700 mb-6">
                            <?php echo htmlspecialchars($book['description']); ?>
                        </p>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Duration:</span>
                                <span><?php echo $book['estimated_duration']; ?> minutes</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Level:</span>
                                <span class="capitalize"><?php echo htmlspecialchars($book['difficulty_level']); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Views:</span>
                                <span><?php echo number_format($book['views_count']); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($userId && isset($book['user_progress'])): ?>
                        <div class="card-flat p-6 min-w-[250px]">
                            <h3 class="font-semibold text-black mb-3">Your Progress</h3>
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-semibold text-black">
                                        <?php echo round($book['user_progress']['progress_percentage']); ?>%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-black h-2 rounded-full transition-all" 
                                         style="width: <?php echo $book['user_progress']['progress_percentage']; ?>%">
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 capitalize">
                                Status: <?php echo str_replace('_', ' ', $book['user_progress']['status']); ?>
                            </p>
                        </div>
                    <?php elseif (!$userId): ?>
                        <div class="card-flat p-6 min-w-[250px] text-center">
                            <p class="text-sm text-gray-600 mb-3">Track your progress</p>
                            <a href="login.php" class="btn btn-primary btn-sm w-full">Login to Continue</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="animate-slide-up" style="animation-delay: 100ms;">
            <div class="card p-8 md:p-12">
                <div class="prose prose-lg max-w-none">
                    <?php echo $book['content']; ?>
                </div>

                <?php if ($userId): ?>
                    <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                        <form method="POST" action="update-progress.php">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                            <input type="hidden" name="progress" value="100">
                            <button type="submit" class="btn btn-primary">
                                Mark as Completed
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.prose h1 { font-size: 2rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #000; }
.prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #000; }
.prose h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #000; }
.prose p { margin-bottom: 1rem; line-height: 1.75; color: #374151; }
.prose pre { background: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0; }
.prose code { background: #f3f4f6; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-size: 0.875rem; }
.prose pre code { background: none; padding: 0; }
.prose ul, .prose ol { margin: 1rem 0; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
</style>

<?php require_once '../src/includes/footer.php'; ?>
