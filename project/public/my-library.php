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

$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

$userBooks = $bookController->getUserLibrary($userId, $statusFilter);

$stats = $bookController->getUserDashboardStats($userId);

require_once '../src/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">My Library</h1>
            <p class="text-gray-600">Track your learning progress and continue where you left off</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 animate-slide-up" style="animation-delay: 50ms;">
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
                <div class="flex flex-wrap gap-2">
                    <a href="my-library.php" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo !$statusFilter ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        All Books
                    </a>
                    <a href="my-library.php?status=in_progress" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $statusFilter === 'in_progress' ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        In Progress
                    </a>
                    <a href="my-library.php?status=completed" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $statusFilter === 'completed' ? 'bg-black text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Completed
                    </a>
                </div>
            </div>
        </div>

        <?php if (empty($userBooks)): ?>
            <div class="card p-12 text-center animate-fade-in" style="animation-delay: 150ms;">
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
            <div class="space-y-4">
                <?php foreach ($userBooks as $index => $item): ?>
                    <div class="card card-hover animate-slide-up" style="animation-delay: <?php echo 150 + ($index * 50); ?>ms;">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="flex-1">
                                    <?php if ($item['category_name']): ?>
                                        <div class="mb-2">
                                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                                <?php echo htmlspecialchars($item['category_name']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <h3 class="text-xl font-bold text-black mb-2">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </h3>

                                    <?php if ($item['author']): ?>
                                        <p class="text-sm text-gray-600 mb-3">
                                            by <?php echo htmlspecialchars($item['author']); ?>
                                        </p>
                                    <?php endif; ?>

                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        <?php echo htmlspecialchars($item['description']); ?>
                                    </p>

                                    <div class="mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Progress</span>
                                            <span class="font-semibold text-black">
                                                <?php echo round($item['progress_percentage']); ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-black h-2 rounded-full transition-all" 
                                                 style="width: <?php echo $item['progress_percentage']; ?>%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                        <span>Last accessed: <?php echo date('M j, Y', strtotime($item['last_accessed'])); ?></span>
                                        <span class="capitalize">Status: <?php echo str_replace('_', ' ', $item['status']); ?></span>
                                        <?php if ($item['completed_at']): ?>
                                            <span>Completed: <?php echo date('M j, Y', strtotime($item['completed_at'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <a href="book.php?id=<?php echo $item['book_id']; ?>" class="btn btn-primary whitespace-nowrap">
                                        <?php echo $item['status'] === 'completed' ? 'Read Again' : 'Continue Reading'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../src/includes/footer.php'; ?>
