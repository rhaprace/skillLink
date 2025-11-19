<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

$pageTitle = 'Bookmarks Management - Admin - SkillLink';
$adminController = new AdminController($pdo);

$userId = $_GET['user_id'] ?? null;
$bookId = $_GET['book_id'] ?? null;

$bookmarks = $adminController->getAllBookmarks($userId, $bookId);

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-black mb-2">Bookmarks Management</h1>
    <p class="text-gray-600">View and manage all user bookmarks</p>
</div>

<div class="card mb-6">
    <div class="p-6">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input
                type="number"
                name="user_id"
                placeholder="Filter by User ID..."
                class="form-input"
                value="<?php echo htmlspecialchars($userId ?? ''); ?>"
            >
            <input
                type="number"
                name="book_id"
                placeholder="Filter by Book ID..."
                class="form-input"
                value="<?php echo htmlspecialchars($bookId ?? ''); ?>"
            >
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">Filter</button>
                <a href="bookmarks.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Book</th>
                    <th>Category</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookmarks)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">No bookmarks found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookmarks as $bookmark): ?>
                        <tr>
                            <td class="font-mono text-sm">#<?php echo $bookmark['id']; ?></td>
                            <td>
                                <div>
                                    <p class="font-semibold text-sm"><?php echo htmlspecialchars($bookmark['username']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($bookmark['user_email']); ?></p>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <p class="font-semibold text-sm"><?php echo htmlspecialchars($bookmark['book_title']); ?></p>
                                    <p class="text-xs text-gray-500">by <?php echo htmlspecialchars($bookmark['book_author']); ?></p>
                                </div>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-default">
                                    <?php echo htmlspecialchars($bookmark['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                            </td>
                            <td class="text-sm text-gray-500">
                                <?php echo date('M j, Y', strtotime($bookmark['created_at'])); ?>
                            </td>
                            <td>
                                <button 
                                    onclick="showDeleteBookmarkModal(<?php echo $bookmark['id']; ?>, '<?php echo htmlspecialchars($bookmark['username']); ?>', '<?php echo htmlspecialchars($bookmark['book_title']); ?>')"
                                    class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="deleteBookmarkModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('deleteBookmarkModal', event)">
    <div class="card max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Delete Bookmark</h3>
            <p class="text-gray-600 mb-6" id="deleteBookmarkMessage"></p>
            <form method="POST" action="delete-bookmark.php">
                <input type="hidden" name="bookmark_id" id="deleteBookmarkId">
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-danger flex-1">Delete</button>
                    <button type="button" onclick="Modal.hide('deleteBookmarkModal')" class="btn btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDeleteBookmarkModal(id, username, bookTitle) {
    document.getElementById('deleteBookmarkId').value = id;
    document.getElementById('deleteBookmarkMessage').textContent = 
        `Are you sure you want to delete ${username}'s bookmark for "${bookTitle}"?`;
    Modal.show('deleteBookmarkModal');
}
</script>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

