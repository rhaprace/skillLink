<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

$pageTitle = 'Admin Dashboard - SkillLink';
$adminController = new AdminController($pdo);

$stats = $adminController->getDashboardStats();
$recentUsers = $adminController->getRecentUsers(5);
$popularBooks = $adminController->getPopularBooks(5);

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-black mb-2">Dashboard</h1>
    <p class="text-gray-600">Overview of your SkillLink platform</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <?php
    $title = 'Total Users';
    $value = $stats['total_users'];
    $icon = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>';
    $animationDelay = '0ms';
    require '../../src/includes/components/admin-stats-card.php';
    ?>

    <?php
    $title = 'Total Books';
    $value = $stats['total_books'];
    $icon = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>';
    $animationDelay = '50ms';
    require '../../src/includes/components/admin-stats-card.php';
    ?>

    <?php
    $title = 'Categories';
    $value = $stats['total_categories'];
    $icon = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>';
    $animationDelay = '100ms';
    require '../../src/includes/components/admin-stats-card.php';
    ?>

    <?php
    $title = 'Bookmarks';
    $value = $stats['total_bookmarks'];
    $icon = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>';
    $animationDelay = '150ms';
    require '../../src/includes/components/admin-stats-card.php';
    ?>

    <?php
    $title = 'Reviews';
    $value = $stats['total_reviews'];
    $icon = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>';
    $animationDelay = '200ms';
    require '../../src/includes/components/admin-stats-card.php';
    ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card animate-slide-up" style="animation-delay: 200ms;">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-black">Recent Users</h2>
                <a href="users.php" class="text-sm text-gray-600 hover:text-black">View All →</a>
            </div>

            <?php if (empty($recentUsers)): ?>
                <p class="text-gray-500 text-center py-8">No users yet</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-black"><?php echo htmlspecialchars($user['username']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <span class="text-xs text-gray-500">
                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card animate-slide-up" style="animation-delay: 250ms;">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-black">Popular Books</h2>
                <a href="books.php" class="text-sm text-gray-600 hover:text-black">View All →</a>
            </div>

            <?php if (empty($popularBooks)): ?>
                <p class="text-gray-500 text-center py-8">No books yet</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($popularBooks as $book): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-semibold text-black"><?php echo htmlspecialchars($book['title']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($book['category_name'] ?? 'Uncategorized'); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-black"><?php echo $book['views_count']; ?> views</p>
                                <p class="text-xs text-gray-500"><?php echo $book['bookmark_count']; ?> bookmarks</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

