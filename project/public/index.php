<?php
session_start();
$pageTitle = 'Home - SkillLink';

require_once '../src/config/database.php';
require_once '../src/controllers/BookController.php';

$bookController = new BookController($pdo);
$totalBooks = $bookController->getTotalBooksCount();

$isLoggedInUser = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin_id']);

if ($isLoggedInUser) {
    $userStats = $bookController->getUserDashboardStats($_SESSION['user_id']);
    $recentBooks = $bookController->getRecentlyAccessed($_SESSION['user_id'], 3);
}

require_once '../src/includes/header.php';
?>

<?php if ($isLoggedInUser): ?>
    <div class="min-h-screen bg-white py-8">
        <div class="container-custom w-full">
            <div class="w-full">
                <div class="mb-6 animate-fade-in">
                    <div>
                        <h1 class="text-2xl font-bold text-black mb-1">
                            Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </h1>
                        <p class="text-sm text-gray-600">Continue your learning journey</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="card animate-slide-up" style="animation-delay: 50ms;">
                        <div class="p-4">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Books Started</p>
                            <p class="text-3xl font-bold text-black"><?php echo $userStats['total_books']; ?></p>
                        </div>
                    </div>
                    <div class="card animate-slide-up" style="animation-delay: 100ms;">
                        <div class="p-4">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">In Progress</p>
                            <p class="text-3xl font-bold text-black"><?php echo $userStats['in_progress_books']; ?></p>
                        </div>
                    </div>
                    <div class="card animate-slide-up" style="animation-delay: 150ms;">
                        <div class="p-4">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Completed</p>
                            <p class="text-3xl font-bold text-black"><?php echo $userStats['completed_books']; ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($recentBooks)): ?>
                    <div class="mb-6 animate-slide-up" style="animation-delay: 200ms;">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-black">Continue Reading</h2>
                            <a href="my-library.php" class="text-sm text-gray-600 hover:text-black">View All →</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($recentBooks as $book): ?>
                                <a href="book.php?id=<?php echo $book['book_id']; ?>" class="card card-hover">
                                    <div class="p-4">
                                        <h3 class="font-semibold text-black mb-2 line-clamp-2">
                                            <?php echo htmlspecialchars($book['title']); ?>
                                        </h3>
                                        <p class="text-xs text-gray-600 mb-3">
                                            <?php echo htmlspecialchars($book['category_name']); ?>
                                        </p>
                                        <div class="mb-2">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="text-gray-600">Progress</span>
                                                <span class="font-semibold text-black">
                                                    <?php echo round($book['progress_percentage']); ?>%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-black h-1.5 rounded-full"
                                                     style="width: <?php echo $book['progress_percentage']; ?>%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="card animate-slide-up" style="animation-delay: 250ms;">
                        <div class="p-4">
                            <h3 class="font-semibold text-black mb-3">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="books.php" class="w-full btn btn-primary btn-sm block text-center">Browse Books</a>
                                <a href="my-library.php" class="w-full btn btn-secondary btn-sm block text-center">My Library</a>
                            </div>
                        </div>
                    </div>
                    <div class="card animate-slide-up" style="animation-delay: 300ms;">
                        <div class="p-4">
                            <h3 class="font-semibold text-black mb-3">Account Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email</span>
                                    <span class="text-black font-medium"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Username</span>
                                    <span class="text-black font-medium"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="min-h-screen bg-white py-8">
        <div class="container-custom w-full">
            <div class="w-full">
                <?php if ($isAdmin): ?>
                    <div class="mb-6 animate-fade-in">
                        <div class="card p-6 bg-gray-50 border border-gray-200">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-black">Admin Preview Mode</h3>
                                    <p class="text-sm text-gray-600">You're viewing the public site as an admin. This is how visitors see SkillLink.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div class="animate-fade-in">
                        <h1 class="text-5xl font-bold text-black mb-4 leading-tight">
                            Master New Skills with
                            <span class="block mt-2">SkillLink</span>
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Access interactive lessons and quizzes designed to help you learn faster and retain more. Start your learning journey today.
                        </p>
                        <?php if ($isAdmin): ?>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="admin/dashboard.php" class="btn btn-primary">Back to Admin Panel</a>
                                <a href="books.php" class="btn btn-secondary">Browse Books</a>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="register.php" class="btn btn-primary">Get Started</a>
                                <a href="login.php" class="btn btn-secondary">Sign In</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4 animate-slide-up">
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2"><?php echo $totalBooks; ?></p>
                                <p class="text-sm text-gray-600">Books Available</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">8</p>
                                <p class="text-sm text-gray-600">Categories</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">100%</p>
                                <p class="text-sm text-gray-600">Free Access</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">24/7</p>
                                <p class="text-sm text-gray-600">Learn Anytime</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../src/includes/footer.php'; ?>
